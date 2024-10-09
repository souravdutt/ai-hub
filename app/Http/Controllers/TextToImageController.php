<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\WorkersAIService;
use Illuminate\Support\Facades\Validator;

class TextToImageController extends Controller
{
    protected $workersAI;

    public function __construct(WorkersAIService $workersAI)
    {
        $this->workersAI = $workersAI;
    }

    public function index()
    {
        return Inertia::render('TextToImage');
    }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:3|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $prompt = $request->message;

        $response = $this->workersAI->textToImage($prompt);

        return $response;
    }
}
