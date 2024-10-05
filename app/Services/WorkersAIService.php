<?php

namespace App\Services;

use App\Services\Service;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkersAIService extends Service
{
    protected $baseUrl;
    protected $apiKey;
    protected int $timeout = 120;

    /**
     * Construct a new instance of the WorkersAIService.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        ini_set('max_execution_time', $this->timeout * 5);

        $this->baseUrl = 'https://api.cloudflare.com/client/v4/accounts/' . config('services.workers.ai.account_id') . '/ai/run';
        $this->apiKey = config('services.workers.ai.api_token');
    }

    /**
     * Create a server-sent event stream using the given callback.
     *
     * @param callable $callback
     * @param int $chunkSize
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function stream(callable $callback, int $chunkSize = 64): StreamedResponse
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

    /**
     * Given a prompt string, generate a markdown response using the WorkerAI Language Model.
     *
     * @param string $prompt The prompt string to generate a response to.
     * @param string $model The model to use from the WorkerAI API. Defaults to the value of `services.workers.ai.models.nlp` in the config.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse A streamed response containing the markdown response.
     * @throws \Exception If no prompt is provided.
     */
    public function regular(string $prompt, string $model = null)
    {
        if (is_null($prompt)) throw new \Exception('No prompt provided');

        if (is_null($model)) $model = config('services.workers.ai.models.nlp');

        return $this->stream(function () use ($prompt, $model) {
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
                            'content' => $prompt
                        ]
                    ]
                ]);

            return $response;
        });
    }


    public function textToImage(string $prompt, string $model = null)
    {
        if (is_null($prompt)) throw new \Exception('No prompt provided');

        if (is_null($model)) $model = config('services.workers.ai.models.tti');

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->post($this->baseUrl . '/' . $model, [
                'max_tokens' => 1024,
                'prompt' => $prompt,
                'negative_prompt' => 'Do not create nude, abusive and harmful images.'
                    . 'Do not include sensitive personal information.'
                    . 'Do not use offensive language or hate speech.',
                'height' => 512,
                'width' => 512,
                'num_steps' => 10, // max 20, higher values can improve quality but take longer
                'guidance' => 10, // how closely the generated image should adhere to the prompt; higher values make the image more aligned with the prompt
            ]);

        if ($response->status() !== 200) {
            return response()->json([
                'success' => false,
                'message' => $response->body()
            ], $response->status());
        }

        return response()->json([
            'success' => true,
            'data' => $response->body()
        ]);
    }
}
