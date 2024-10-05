<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\WorkersAIService;

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
}
