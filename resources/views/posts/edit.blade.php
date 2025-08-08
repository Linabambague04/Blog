@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-primary">Editar Post</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="card shadow-sm p-4 border-0">
        @csrf
        @method('PUT')

        <!-- Título -->
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" id="title" class="form-control" 
                   value="{{ old('title', $post->title) }}" required>
        </div>

        <!-- Contenido -->
        <div class="mb-3">
            <label for="content" class="form-label">Contenido</label>
            <textarea name="content" id="content" class="form-control" rows="5" required>{{ old('content', $post->content) }}</textarea>
        </div>

        <!-- Imagen actual -->
        @if ($post->image)
            <div class="mb-3">
                <label class="form-label">Imagen actual:</label><br>
                <img src="{{ asset('storage/' . $post->image) }}" 
                     alt="Imagen actual del post"
                     class="img-thumbnail rounded"
                     style="max-width: 200px; height: auto;">
            </div>
        @endif

        <!-- Nueva imagen -->
        <div class="mb-3">
            <label for="image" class="form-label">Cambiar imagen (opcional)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>

        <!-- Vista previa de nueva imagen -->
        <div class="mb-3" id="preview-container" style="display: none;">
            <label class="form-label">Vista previa:</label><br>
            <img id="preview-image" class="img-thumbnail rounded" style="max-width: 200px;">
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                ← Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                💾 Actualizar
            </button>
        </div>
    </form>
</div>

<!-- Script de preview -->
<script>
    document.getElementById('image').addEventListener('change', function (event) {
        const input = event.target;
        const preview = document.getElementById('preview-image');
        const container = document.getElementById('preview-container');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            container.style.display = 'none';
            preview.src = '';
        }
    });
</script>
@endsection
