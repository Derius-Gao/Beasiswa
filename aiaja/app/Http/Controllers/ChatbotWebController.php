<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotWebController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'language' => 'nullable|string|in:auto,id,en,both',
        ]);

        $reply = $this->aiService->generateChatbotResponse(
            $validated['message'],
            Auth::user(),
            $validated['language'] ?? null
        );

        return response()->json([
            'success' => true,
            'response' => $reply,
        ]);
    }
}


