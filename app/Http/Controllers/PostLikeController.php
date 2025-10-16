<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\Notification;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function toggleLike(Post $post)
    {
        $user = auth()->user();

        $existingLike = PostLike::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json([
                'status' => 'unliked',
                'count' => $post->likes()->count(),
            ]);
        }

        PostLike::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        // 🔔 Crear notificación automática para el autor (si no es él mismo)
        if ($post->user_id !== $user->id) {
            Notification::create([
                'user_id' => $post->user_id,
                'title' => 'Nuevo “Me gusta” ❤️',
                'message' => "{$user->name} le dio me gusta a tu publicación: \"{$post->title}\"",
            ]);
        }

        return response()->json([
            'status' => 'liked',
            'count' => $post->likes()->count(),
        ]);
    }
}
