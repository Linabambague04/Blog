@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="card shadow-sm border-0">
        @if ($post->image)
            <img src="{{ asset('storage/' . $post->image) }}"
                 alt="Imagen del post"
                 class="card-img-top rounded-top"
                 style="max-height: 400px; object-fit: cover;">
        @endif

        <div class="card-body">
            <!-- Título -->
            <h1 class="card-title text-primary">{{ $post->title }}</h1>

            <!-- Autor y fecha -->
            <p class="text-muted mb-3">
                Publicado por <strong>{{ $post->user->name ?? 'Usuario desconocido' }}</strong>
                el {{ $post->created_at->format('d/m/Y') }}
            </p>

            <!-- Contenido -->
            <div class="mb-4 text-secondary" style="white-space: pre-line;">
                {{ $post->content }}
            </div>

            <!-- Botones -->
            <div class="d-flex gap-2">
                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                    ← Volver
                </a>

                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-primary">
                        ✏️ Editar
                    </a>
                @endcan

                @can('delete', $post)
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('¿Seguro que deseas eliminar este post?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            🗑️ Eliminar
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

</div>
@endsection
