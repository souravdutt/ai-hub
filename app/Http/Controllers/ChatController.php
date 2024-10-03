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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return $this->workersAI->regular($request->message);
    }

}
