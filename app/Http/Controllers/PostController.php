<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Recommendation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with('user')->paginate(10);

        $recommendation = Recommendation::where('user_id', auth()->id())->first();

        $recommendedPosts = collect();
        if ($recommendation && isset($recommendation->recommended_items['recomendaciones'])) {
            $recommendedPosts = Post::whereIn('id', $recommendation->recommended_items['recomendaciones'])->get();
        }

        return view('posts.index', compact('posts', 'recommendation', 'recommendedPosts'));
    }

    public function create ()
    {
        return view('posts.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $data['image'] = $path;
        }

        $data['user_id'] = auth()->id();

        $post = Post::create($data); 

        try {
            Http::post('http://localhost:5678/webhook-test/24423912-a00b-4540-9942-994a66b0b79f', [
                'user_id' => $post->user_id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al notificar a n8n: ' . $e->getMessage());
        }

        return redirect()->route('posts.index')->with('success', 'Post creado con éxito.');
    }


    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este post.');
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este post.');
        }

        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $data['image'] = $path;
        }

        $post->update($data);


        try {
            Http::post('http://localhost:5678/webhook-test/24423912-a00b-4540-9942-994a66b0b79f', [
                'user_id' => $post->user_id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al notificar a n8n: ' . $e->getMessage());
        }

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post actualizado con éxito.');
    }



    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'No tienes permiso para eliminar este post.');
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post eliminado correctamente.');
    }

    public function generarPDF()
    {
        $user = auth()->user();
        $posts = \App\Models\Post::where('user_id', $user->id)->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.posts', compact('user', 'posts'));

        return $pdf->download('posts-' . $user->name . '.pdf');
    }


}
