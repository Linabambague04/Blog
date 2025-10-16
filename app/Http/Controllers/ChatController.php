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

        // Construir mensajes MCP con un prompt mejorado
        $messagesForModel = [
            [
                'role' => 'system',
                'content' => 'Eres un asistente útil en español que puede interactuar con una base de datos.

                REGLAS IMPORTANTES:
                1. Responde SIEMPRE en español de manera conversacional y amigable
                2. Cuando el usuario pida crear/insertar algo, usa la función insert_record
                3. Cuando el usuario pida ver/consultar datos, usa query_database
                4. NO devuelvas JSON literal en tu respuesta - usa las herramientas disponibles
                5. Después de ejecutar una acción, confirma lo que hiciste en lenguaje natural

                TABLAS DISPONIBLES:
                - posts: Tiene columnas "title" (título) y "content" (contenido)
                - users: Información de usuarios

                IMPORTANTE SOBRE POSTS:
                - Usa "title" (no "titulo") 
                - Usa "content" (no "contenido")
                - NO necesitas especificar user_id o session_id, el sistema lo hace automáticamente
                - Las fechas created_at y updated_at también se agregan automáticamente

                EJEMPLOS DE USO:

                Usuario: "crea un post sobre la felicidad"
                Tú: [Usas insert_record con {"table": "posts", "data": {"title": "La felicidad", "content": "..."}}]
                Luego respondes: "¡Listo! He creado una publicación sobre la felicidad."

                Usuario: "muéstrame los posts más populares"
                Tú: [Usas query_database con "SELECT * FROM posts ORDER BY likes DESC LIMIT 5"]
                Luego respondes con los resultados de manera amigable.

                Usuario: "cuánto es 2+2"
                Tú: "2 + 2 = 4"

                Sé natural, útil y usa las herramientas cuando sea apropiado.'
            ],
        ];

        foreach ($historyMessages as $msg) {
            $messagesForModel[] = [
                'role' => $msg->sender === 'user' ? 'user' : 'assistant',
                'content' => $msg->message,
            ];
        }

        // Preparar contexto para el servicio MCP
        $context = [
            'user_id' => $user ? $user->id : null,
            'session_id' => $user ? null : $sessionId,
        ];

        // Llamar servicio MCP con contexto
        $response = $this->mcp->chat($messagesForModel, $context);

        if (!$response['success']) {
            return response()->json([
                'error' => true,
                'message' => 'Error al procesar la solicitud: ' . ($response['error'] ?? 'Desconocido')
            ], 500);
        }

        $botReply = $response['reply'];

        // Limpiar respuesta si contiene JSON accidental
        $botReply = preg_replace('/\{"type":\s*"function"[^}]*\}/', '', $botReply);
        $botReply = trim($botReply);

        // Si la respuesta está vacía después de limpiar, dar un mensaje por defecto
        if (empty($botReply)) {
            $botReply = 'He procesado tu solicitud correctamente.';
        }

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