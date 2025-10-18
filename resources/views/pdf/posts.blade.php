<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de publicaciones</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 30px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        h3 {
            margin-top: 25px;
            color: #34495e;
        }
        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }
        p {
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <h1>Publicaciones de {{ $user->name }}</h1>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    <hr>

    @foreach ($posts as $post)
        <h3>{{ $post->title }}</h3>
        <p><em>{{ $post->created_at->format('d/m/Y H:i') }}</em></p>
        <p>{{ $post->content }}</p>
        <hr>
    @endforeach
</body>
</html>
