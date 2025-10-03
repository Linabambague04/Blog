<!DOCTYPE html>
<html>
<head>
    <title>Agente Conversacional (MCP)</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        .chat-box { border: 1px solid #ccc; padding: 1rem; max-width: 600px; height: 400px; overflow-y: scroll; }
        .message { margin-bottom: 1rem; }
        .user { font-weight: bold; color: #007bff; }
        .bot { font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <h1>Agente Conversacional (MCP)</h1>

    <?php if(session('error')): ?>
        <p style="color:red;"><?php echo e(session('error')); ?></p>
    <?php endif; ?>

    <div class="chat-box" id="chat-box">
        <?php $__currentLoopData = $messages ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="message <?php echo e($msg->role == 'user' ? 'user' : 'bot'); ?>">
                <strong><?php echo e(ucfirst($msg->role)); ?>:</strong> <?php echo e($msg->message); ?>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="POST" action="<?php echo e(route('chat.send')); ?>">
        <?php echo csrf_field(); ?>
        <textarea name="message" rows="4" cols="60" placeholder="Escribe tu mensaje..." required></textarea><br><br>
        <button type="submit">Enviar</button>
    </form>

    <script>
        // Auto scroll al final del chat
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Blog\resources\views/chat.blade.php ENDPATH**/ ?>