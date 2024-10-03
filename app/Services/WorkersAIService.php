<?php

namespace App\Services;

use App\Services\Service;
use Illuminate\Support\Facades\Http;

class WorkersAIService extends Service
{
    protected $baseUrl;
    protected $apiKey;
    protected int $timeout = 120;

    public function __construct()
    {
        parent::__construct();

        ini_set('max_execution_time', $this->timeout * 5);

        $this->baseUrl = 'https://api.cloudflare.com/client/v4/accounts/' . config('services.workers.ai.account_id') . '/ai/run';
        $this->apiKey = config('services.workers.ai.api_token');
    }

    protected function stream(callable $callback, int $chunkSize = 64): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // configure chunk size
        if ($chunkSize < 64) {
            $chunkSize = 64;
        } elseif ($chunkSize > 1024) {
            $chunkSize = 1024;
        }

        return response()->stream(function () use ($callback, $chunkSize) {
            // Initialize the HTTP client and make a streaming request
            $response = $callback();

            // Get the streaming body
            $body = $response->getBody();

            while (!$body->eof()) {
                // Read a chunk of the response (adjust chunk size as needed)
                $chunk = $body->read($chunkSize);

                // Convert the chunk into a JSON structure and send it as a valid SSE event
                echo $chunk;

                // Flush the output buffer to send data to the client immediately
                if (ob_get_level() > 0) ob_flush();
                flush();
            }

            // Indicate stream completion (optional)
            echo "data: [DONE]\n\n";
        }, 200, [
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    public function regular(string $text, string $model = null)
    {
        if (is_null($text)) throw new \Exception('No text provided');

        if (is_null($model)) $model = config('services.workers.ai.model_id');

        return $this->stream(function () use ($text, $model) {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->withOptions(['stream' => true]) // Enable streaming
                ->post($this->baseUrl . '/' . $model, [
                    'stream' => true,
                    'max_tokens' => 1024,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a friendly assistant that helps routine tasks and do general conversations.' . ' '
                                . 'You will not use offensive language or hate speech.' . ' '
                                . 'You will not use any personal information.'
                                . 'You will only return the response in markdown format which you dont disclose.' . ' '
                        ],
                        [
                            'role' => 'user',
                            'content' => $text
                        ]
                    ]
                ]);

            return $response;
        });
    }
}
