<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Blog</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html, body { height: 100%; }
        body {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }
        main { flex: 1; }

        .navbar-custom { background-color: #3c4fd8; }
        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand {
            color: #fff;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .navbar-custom .nav-link:hover {
            color: #ffdc5d;
        }

        .footer-custom {
            background-color: #3c4fd8;
            color: #e2e8f0;
            text-align: center;
            padding: 1.5rem 0;
        }
        .footer-custom p { margin: 0; font-size: 0.875rem; }
        .footer-custom a { color: #fff; text-decoration: none; }
        .footer-custom a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">Mi Blog</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Registrarse</a></li>
                @else
                    <li class="nav-item me-3">
                        <a class="nav-link" href="{{ route('posts.index') }}">Publicaciones</a>
                    </li>

                    <li class="nav-item d-flex align-items-center me-3">
                        {{-- Avatar o letra --}}
                        @if (Auth::user()->avatar && file_exists(public_path('storage/' . Auth::user()->avatar)))
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                 alt="Avatar"
                                 class="rounded-circle me-2"
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <span class="rounded-circle bg-secondary d-inline-block text-white text-center me-2"
                                  style="width: 40px; height: 40px; line-height: 40px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        @endif

                        {{-- Nombre del usuario con link a editar --}}
                        <a href="{{ route('profile.edit') }}" class="text-white text-decoration-none fw-semibold">
                            {{ Auth::user()->name }}
                        </a>
                    </li>

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light text-danger">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>


<main class="container py-4">
    @yield('content')
</main>

<footer class="footer-custom">
    <p>&copy; {{ date('Y') }} Mi Blog. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
