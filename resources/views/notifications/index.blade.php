@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">
            <i class="bi bi-bell-fill me-2"></i> Mis notificaciones
        </h2>
        @if(session('success'))
            <div class="alert alert-success mb-0 py-2 px-3 shadow-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    @forelse ($notificaciones as $n)
        <div class="card mb-3 border-0 shadow-sm rounded-4">
            <div class="card-body position-relative">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                              style="width: 45px; height: 45px;">
                            <i class="bi bi-info-lg fs-5"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1 fw-semibold text-dark">{{ $n->title }}</h5>
                        <p class="card-text text-muted mb-2">{{ $n->message }}</p>
                        <small class="text-secondary">
                            <i class="bi bi-clock"></i> {{ $n->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>

                <form action="{{ route('notifications.destroy', $n) }}" method="POST"
                      class="position-absolute top-0 end-0 mt-2 me-2">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger rounded-circle" title="Eliminar notificación">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center text-muted mt-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p class="fs-5">No tienes notificaciones por ahora.</p>
        </div>
    @endforelse

    <div class="d-flex justify-content-center mt-4">
        {{ $notificaciones->links() }}
    </div>
</div>

{{-- Iconos de Bootstrap --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection
