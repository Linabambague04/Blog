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
    <h1>Publicaciones de <?php echo e($user->name); ?></h1>
    <p><strong>Email:</strong> <?php echo e($user->email); ?></p>
    <p><strong>Generado:</strong> <?php echo e(now()->format('d/m/Y H:i')); ?></p>
    <hr>

    <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h3><?php echo e($post->title); ?></h3>
        <p><em><?php echo e($post->created_at->format('d/m/Y H:i')); ?></em></p>
        <p><?php echo e($post->content); ?></p>
        <hr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Blog\resources\views/pdf/posts.blade.php ENDPATH**/ ?>