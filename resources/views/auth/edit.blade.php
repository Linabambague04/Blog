@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Editar Perfil</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- FORMULARIO EDITAR PERFIL -->
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

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>

    <!-- FORMULARIO ELIMINAR PERFIL -->
<hr class="my-4">
<h4>Eliminar cuenta</h4>
<p class="text-danger">Esta acción no se puede deshacer. Todos tus datos serán eliminados.</p>

<form action="{{ route('profile.destroy') }}" method="POST" style="max-width: 400px;">
    @csrf
    @method('DELETE')

    <div class="mb-3">
        <label for="password" class="form-label">Confirma tu contraseña</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    @error('password')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <button type="submit" class="btn btn-danger">Eliminar mi cuenta</button>
</form>
</div>
@endsection
