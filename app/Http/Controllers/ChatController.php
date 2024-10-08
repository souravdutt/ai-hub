<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\WorkersAIService;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected $workersAI;

    public function __construct(WorkersAIService $workersAI)
    {
        $this->workersAI = $workersAI;
    }

    public function index()
    {
        return Inertia::render('Welcome');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:3',
            'history' => 'nullable|array',
            'history.*.message' => 'nullable|string',
            'history.*.sender' => 'required|string|in:user,server',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $history = collect($request->history)->map(function ($message) {
            return "{$message['sender']}:\n{$message['message']}";
        })->implode("\n\n");

        return $this->workersAI->regular($request->message, $history);
    }

}
