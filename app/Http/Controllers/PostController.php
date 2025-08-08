<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with('user')->paginate(10); 
        return view('posts.index', compact('posts'));
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

        Post::create($data);

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


}
