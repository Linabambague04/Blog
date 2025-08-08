@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center text-primary">Crear nuevo post</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="{{ old('title') }}" required>
                        </div>

                        <!-- Contenido -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenido</label>
                            <textarea name="content" id="content" class="form-control" rows="5" required>{{ old('content') }}</textarea>
                        </div>

                        <!-- Imagen -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen (opcional)</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>

                        <!-- Vista previa mini -->
                        <div class="mb-3" id="preview-container" style="display: none;">
                            <p class="mb-1">Vista previa:</p>
                            <div class="position-relative d-inline-block">
                                <img id="preview-image" class="img-thumbnail rounded" style="width: 120px; height: 120px; object-fit: cover;">
                                <button type="button" id="remove-preview" class="btn-close position-absolute top-0 end-0" aria-label="Cerrar"></button>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Publicar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script para preview --}}
<script>
    const inputImage = document.getElementById('image');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const removeButton = document.getElementById('remove-preview');

    inputImage.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    removeButton.addEventListener('click', function () {
        inputImage.value = '';
        previewImage.src = '';
        previewContainer.style.display = 'none';
    });
</script>
@endsection
