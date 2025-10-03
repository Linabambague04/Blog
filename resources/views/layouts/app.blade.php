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
                        <a class="nav-link" href="{{ route('posts.index') }}">Mis Publicaciones</a>
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

<!-- Botón flotante del chat -->
<button id="chatToggleBtn" class="btn btn-primary rounded-circle position-fixed"
        style="bottom: 20px; right: 20px; width: 60px; height: 60px; z-index: 1050;">
    💬
</button>

<!-- Ventana de chat -->
<div id="chatWindow" class="card shadow position-fixed" style="bottom: 90px; right: 20px; width: 350px; display: none; z-index: 1050;">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Tu Agente</span>
        <button type="button" class="btn-close btn-close-white btn-sm" id="closeChatBtn"></button>
    </div>
    <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="chatMessages">
        <div class="text-muted small">Hola, ¿en qué puedo ayudarte?</div>
    </div>
    <div class="card-footer">
        <form id="chatForm">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control" id="chatInput" placeholder="Escribe..." required>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>
</div>
<script>
    const chatToggleBtn = document.getElementById('chatToggleBtn');
    const chatWindow = document.getElementById('chatWindow');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    // Mostrar u ocultar ventana de chat
    chatToggleBtn.addEventListener('click', () => {
        chatWindow.style.display = chatWindow.style.display === 'none' ? 'block' : 'none';
    });

    closeChatBtn.addEventListener('click', () => {
        chatWindow.style.display = 'none';
    });

    // Manejar envío del mensaje al backend
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const message = chatInput.value.trim();
        if (!message) return;

        // Mostrar mensaje del usuario
        chatMessages.innerHTML += `<div class="text-end mb-2"><span class="d-inline-block bg-primary text-white p-2 rounded" style="max-width: 80%; word-wrap: break-word; white-space: pre-wrap;">${message}</span></div>`;
        // Mostrar indicador de escritura del bot
        const typingIndicator = document.createElement('div');
        typingIndicator.classList.add('text-start', 'mb-2');
        typingIndicator.innerHTML = `
            <span class="typing-loader bg-secondary text-white p-2 rounded d-inline-block" style="max-width: 80%;">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </span>
        `;
        chatMessages.appendChild(typingIndicator);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        chatInput.value = '';

        // Enviar mensaje al servidor
        const response = await fetch("{{ route('chat.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        });

        const data = await response.json();

        const botReply = data.reply || 'Sin respuesta del bot.';


        chatMessages.innerHTML += `<div class="text-start mb-2"><span class="d-inline-block bg-secondary text-white p-2 rounded" style="max-width: 80%; word-wrap: break-word; white-space: pre-wrap;">${botReply}</span></div>`;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    });
</script>

</body>
</html>
