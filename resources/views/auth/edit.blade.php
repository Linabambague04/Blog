@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Editar Perfil</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ old('name', $user->name) }}" required>
        </div>

        @if ($user->avatar)
            <div class="mb-3">
                <img src="{{ asset('storage/' . $user->avatar) }}" 
                     alt="Avatar"
                     class="rounded-circle" 
                     style="width: 80px; height: 80px; object-fit: cover;">
            </div>
        @endif 

        <div class="mb-3">
            <label for="avatar" class="form-label">Cambiar avatar</label>
            <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
