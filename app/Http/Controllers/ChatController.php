<?php

namespace App\Http\Controllers;

use App\Services\MCPService;
use Illuminate\Http\Request;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    protected $mcp;

    public function __construct(MCPService $mcp)
    {
        $this->mcp = $mcp;
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $userMessage = $request->input('message');
        $user = $request->user();
        $sessionId = $request->session()->getId();

        // Guardar mensaje usuario
        ChatMessage::create([
            'sender' => 'user',
            'message' => $userMessage,
            'user_id' => $user ? $user->id : null,
            'session_id' => $user ? null : $sessionId,
        ]);

        // Obtener historial
        $historyMessages = ChatMessage::where(function ($q) use ($user, $sessionId) {
            if ($user) $q->where('user_id', $user->id);
            else $q->where('session_id', $sessionId);
        })->orderBy('created_at', 'desc')->take(20)->get()->reverse();

        // Construir mensajes MCP
        $messagesForModel = [
            [
                'role' => 'system',
                'content' => 'Eres un asistente en español con acceso a una base de datos.
                Puedes usar las siguientes herramientas:
                - query_database: Para ejecutar consultas SELECT
                - get_table_schema: Para ver la estructura de una tabla
                - list_tables: Para listar todas las tablas disponibles

                Cuando el usuario pregunte sobre datos, usa estas herramientas para obtener información actualizada.'
            ],
        ];

        foreach ($historyMessages as $msg) {
            $messagesForModel[] = [
                'role' => $msg->sender === 'user' ? 'user' : 'assistant',
                'content' => $msg->message,
            ];
        }

        // Llamar servicio MCP
        $response = $this->mcp->chat($messagesForModel);

        if (!$response['success']) {
            return response()->json([
                'error' => true,
                'message' => 'Error al procesar la solicitud'
            ], 500);
        }

        $botReply = $response['reply'];

        // Guardar respuesta del bot
        ChatMessage::create([
            'sender' => 'bot',
            'message' => $botReply,
            'user_id' => $user ? $user->id : null,
            'session_id' => $user ? null : $sessionId,
        ]);

        return response()->json(['reply' => $botReply]);
    }
}
