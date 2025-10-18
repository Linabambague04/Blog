<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Blog</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Iconos de Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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

                    <li class="nav-item me-3">
                        <a href="{{ route('posts.generarPDF') }}" 
                        class="btn btn-warning btn-sm fw-semibold text-dark">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i> Descargar informe
                        </a>
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

                    {{-- 🔔 Notificaciones --}}
                    <li class="nav-item dropdown me-3">
                        
                        {{-- El ícono de la campana que abre el menú --}}
                        <a class="nav-link position-relative text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                            </svg>
                            
                            {{-- Badge con el contador de notificaciones sin leer --}}
                            @if(!empty($navbarNotifCount) && $navbarNotifCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.6em;">
                                    {{ $navbarNotifCount }}
                                    <span class="visually-hidden">notificaciones sin leer</span>
                                </span>
                            @endif
                        </a>
                        
                        {{-- El menú desplegable con la lista de notificaciones --}}
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 350px;">
                            
                            {{-- Cabecera del menú --}}
                            <li><h6 class="dropdown-header">Notificaciones</h6></li>
                            <li><hr class="dropdown-divider mt-0"></li>

                            {{-- Itera sobre las notificaciones --}}
                            @forelse($navbarNotifications ?? [] as $n)
                                <li>
                                    {{-- Se usa una etiqueta <a> para que toda la notificación sea clickeable --}}
                                    {{-- El estilo cambia si la notificación no ha sido leída (fw-bold) --}}
                                    <a class="dropdown-item d-flex align-items-start py-2 @if(!$n->read_at) fw-bold @endif" href="{{ $n->data['url'] ?? '#' }}">
                                        
                                        {{-- Icono representativo de la notificación (ej. info, advertencia, usuario, etc.) --}}
                                        <div class="flex-shrink-0 me-3 mt-1">
                                            <i class="{{ $n->data['icon'] ?? 'bi bi-info-circle-fill text-primary' }}"></i>
                                        </div>
                                        
                                        {{-- Contenido de la notificación --}}
                                        <div class="flex-grow-1">
                                            <p class="mb-0 small">{{ $n->title }}</p>
                                            <p class="mb-1 small text-muted">{{ $n->message }}</p>
                                            <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-0"></li>
                            @empty
                                {{-- Mensaje cuando no hay notificaciones --}}
                                <li>
                                    <div class="dropdown-item text-muted text-center py-3">
                                        <i class="bi bi-check2-circle d-block fs-3 mb-2"></i>
                                        <span>No tienes notificaciones nuevas</span>
                                    </div>
                                </li>
                            @endforelse
                            
                            {{-- Pie del menú con enlace a "Ver todas" --}}
                            <li>
                                <a href="{{ route('notifications.index') }}" class="dropdown-item text-center text-primary small py-2">
                                    Ver todas las notificaciones
                                </a>
                            </li>
                        </ul>
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
<button id="chatToggleBtn" class="btn btn-primary rounded-circle position-fixed shadow-lg"
        style="bottom: 25px; right: 25px; width: 60px; height: 60px; z-index: 1050;">
    <i class="bi bi-chat-dots-fill fs-4"></i>
</button>

<!-- Ventana de chat -->
<div id="chatWindow" class="card shadow-lg position-fixed" 
     style="bottom: 95px; right: 25px; width: 360px; max-width: calc(100vw - 50px); 
            border-radius: 15px; display: none; z-index: 1050;">
    
    <!-- Header -->
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-robot fs-5"></i>
            <span class="fw-semibold">Asistente Virtual</span>
        </div>
        <button type="button" class="btn-close btn-close-white" id="closeChatBtn"></button>
    </div>
    
    <!-- Mensajes -->
    <div id="chatMessages" class="card-body bg-light" 
         style="height: 400px; overflow-y: auto;">
        <div class="alert alert-light text-center small mb-0">
            👋 ¡Hola! ¿En qué puedo ayudarte?
        </div>
    </div>
    
    <!-- Input -->
    <div class="card-footer bg-white p-3">
        <form id="chatForm" class="d-flex gap-2">
            @csrf
            <input type="text" id="chatInput" class="form-control" 
                   placeholder="Escribe un mensaje..." required>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </div>
</div>

<style>
    /* Botón flotante */
    #chatToggleBtn {
        transition: transform 0.2s;
    }
    
    #chatToggleBtn:hover {
        transform: scale(1.1);
    }
    
    /* Ventana de chat */
    #chatWindow {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    
    #chatWindow.show {
        display: block !important;
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Scroll personalizado */
    #chatMessages::-webkit-scrollbar {
        width: 6px;
    }
    
    #chatMessages::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }
    
    /* Mensajes usuario */
    .msg-user {
        background: #0d6efd;
        color: white;
        padding: 10px 15px;
        border-radius: 18px 18px 4px 18px;
        max-width: 75%;
        word-wrap: break-word;
        display: inline-block;
        font-size: 14px;
    }
    
    /* Mensajes bot */
    .msg-bot {
        background: white;
        color: #333;
        padding: 10px 15px;
        border-radius: 18px 18px 18px 4px;
        max-width: 75%;
        word-wrap: break-word;
        display: inline-block;
        font-size: 14px;
        border: 1px solid #e0e0e0;
    }
    
    /* Animación de mensajes */
    .msg-animate {
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Indicador de escritura */
    .typing {
        padding: 10px 15px;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 18px;
        display: inline-flex;
        gap: 4px;
    }
    
    .typing span {
        width: 8px;
        height: 8px;
        background: #999;
        border-radius: 50%;
        animation: bounce 1.4s infinite;
    }
    
    .typing span:nth-child(2) { animation-delay: 0.2s; }
    .typing span:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes bounce {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-10px); }
    }
</style>

<script>
    const chatToggleBtn = document.getElementById('chatToggleBtn');
    const chatWindow = document.getElementById('chatWindow');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    // Abrir/cerrar chat
    chatToggleBtn.addEventListener('click', () => {
        if (chatWindow.classList.contains('show')) {
            chatWindow.classList.remove('show');
        } else {
            chatWindow.style.display = 'block';
            setTimeout(() => chatWindow.classList.add('show'), 10);
            chatInput.focus();
        }
    });

    closeChatBtn.addEventListener('click', () => {
        chatWindow.classList.remove('show');
    });

    // Enviar mensaje
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = chatInput.value.trim();
        if (!message) return;

        // Mensaje usuario
        addMessage(message, 'user');
        chatInput.value = '';

        // Indicador de escritura
        const typing = addTyping();

        try {
            const response = await fetch("{{ route('chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            typing.remove();
            
            // Respuesta bot
            addMessage(data.reply || 'Sin respuesta', 'bot');
            
        } catch (error) {
            typing.remove();
            addMessage('Error al enviar mensaje', 'bot');
        }
    });

    // Agregar mensaje
    function addMessage(text, type) {
        const div = document.createElement('div');
        div.className = `mb-3 msg-animate ${type === 'user' ? 'text-end' : 'text-start'}`;
        
        const span = document.createElement('span');
        span.className = type === 'user' ? 'msg-user' : 'msg-bot';
        span.textContent = text;
        
        div.appendChild(span);
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        return div;
    }

    // Agregar indicador de escritura
    function addTyping() {
        const div = document.createElement('div');
        div.className = 'mb-3 text-start';
        div.innerHTML = `
            <div class="typing">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return div;
    }
</script>   
</body>
</html>
