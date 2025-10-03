<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();

        $messages = ChatMessage::where(function($query) use ($request, $sessionId) {
            if ($request->user()) {
                $query->where('user_id', $request->user()->id);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->orderBy('created_at')->get();

        return view('chat', compact('messages'));
    }

    public function send(Request $request)
    {
        $userMessage = $request->input('message');
        $user = $request->user();
        $sessionId = $request->session()->getId();

        ChatMessage::create([
            'sender' => 'user',
            'message' => $userMessage,
            'user_id' => $user ? $user->id : null,
            'session_id' => $user ? null : $sessionId,
        ]);

        $messagesForModel = [
            ['role' => 'system', 'content' => 'Eres un asistente conversacional en español.'],
            ['role' => 'user', 'content' => $userMessage],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'deepseek/deepseek-chat-v3.1:free',
            'messages' => $messagesForModel,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => true,
                'message' => 'Error al contactar al modelo.',
                'details' => $response->body()
            ], 500);
        }

        $data = $response->json();
        $botReply = $data['choices'][0]['message']['content'] ?? 'Sin respuesta del modelo.';

        ChatMessage::create([
            'sender' => 'bot',
            'message' => $botReply,
            'user_id' => $user ? $user->id : null,
            'session_id' => $user ? null : $sessionId,
        ]);

        return response()->json(['reply' => $botReply]);
    }
}
