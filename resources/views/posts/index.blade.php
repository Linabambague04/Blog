@extends('layouts.app')

@section('title', 'Lista de Posts')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary">Lista de Publicaciones</h1>
        <a href="{{ route('posts.create') }}" class="btn btn-success">+ Nuevo Post</a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
    @forelse ($posts as $post)
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                {{-- Envolvemos la parte clickeable en un <a> --}}
                <a href="{{ route('posts.show', $post->id) }}" class="text-decoration-none text-dark d-block" style="cursor: pointer;">
                    @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}"
                            alt="Imagen del post"
                            class="card-img-top rounded-top"
                            style="height: 180px; object-fit: cover;">
                    @endif

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $post->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($post->content, 100) }}</p>

                        <div class="d-flex align-items-center my-2">
                            @if ($post->user->avatar)
                                <img src="{{ asset('storage/' . $post->user->avatar) }}"
                                    alt="Avatar del autor"
                                    class="rounded-circle me-2"
                                    style="width: 32px; height: 32px; object-fit: cover;">
                            @endif
                            <small class="text-muted"><strong>{{ $post->user->name }}</strong></small>
                        </div>
                    </div>
                </a>
                {{-- ❤️ Botón Me gusta --}}
                <div class="d-flex justify-content-center align-items-center mt-auto mb-2">
                    <button 
                        class="btn btn-link p-0 border-0" 
                        onclick="toggleLike({{ $post->id }})"
                        style="font-size: 1.4rem; color: {{ $post->isLikedBy(Auth::user()) ? 'red' : '#6c757d' }};">
                        <i id="heart-icon-{{ $post->id }}" 
                        class="bi {{ $post->isLikedBy(Auth::user()) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                    </button>
                    <span id="like-count-{{ $post->id }}" class="ms-1 text-muted small">
                        {{ $post->likeCount() }}
                    </span>
                </div>

                {{-- Botones separados para no interferir con el enlace --}}
                <div class="card-footer d-flex gap-2">
                    @if (auth()->id() === $post->user_id)
                        <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-outline-primary btn-sm w-100">
                            Editar
                        </a>

                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                            onsubmit="return confirm('¿Seguro que deseas eliminar este post?')" class="w-100 m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">Eliminar</button>
                        </form>

                    @else
                    
                        <a href="{{ route('posts.show', $post->id) }}" class="btn btn-outline-secondary btn-sm w-100">Ver</a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col">
            <div class="alert alert-info text-center w-100">No hay publicaciones aún.</div>
        </div>
    @endforelse

    </div>

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>
<script>
async function toggleLike(postId) {
    const response = await fetch(`/posts/${postId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    const data = await response.json();
    const icon = document.getElementById(`heart-icon-${postId}`);
    const count = document.getElementById(`like-count-${postId}`);

    if (data.status === 'liked') {
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
        icon.style.color = 'red';
    } else {
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
        icon.style.color = '#6c757d';
    }

    count.textContent = data.count;
}
</script>
@endsection

