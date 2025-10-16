<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mostrar todas las notificaciones del usuario autenticado
     */
    public function index()
    {
        $user = auth()->user();

        $notificaciones = Notification::where('user_id', $user->id)
            ->orWhereNull('user_id')
            ->latest()
            ->paginate(10); // 👈 cambia get() por paginate()

        return view('notifications.index', compact('notificaciones'));
    }


    /**
     * Mostrar una notificación específica
     */
    public function show(Notification $notification)
    {
        // Opcional: verifica que la notificación pertenezca al usuario
        if ($notification->user_id && $notification->user_id !== auth()->id()) {
            abort(403);
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Eliminar una notificación
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id && $notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Notificación eliminada correctamente.');
    }
}
