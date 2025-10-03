<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h1 class="mb-4 text-primary">Editar Post</h1>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('posts.update', $post->id)); ?>" method="POST" enctype="multipart/form-data" class="card shadow-sm p-4 border-0">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Título -->
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" id="title" class="form-control" 
                   value="<?php echo e(old('title', $post->title)); ?>" required>
        </div>

        <!-- Contenido -->
        <div class="mb-3">
            <label for="content" class="form-label">Contenido</label>
            <textarea name="content" id="content" class="form-control" rows="5" required><?php echo e(old('content', $post->content)); ?></textarea>
        </div>

        <!-- Imagen actual -->
        <?php if($post->image): ?>
            <div class="mb-3">
                <label class="form-label">Imagen actual:</label><br>
                <img src="<?php echo e(asset('storage/' . $post->image)); ?>" 
                     alt="Imagen actual del post"
                     class="img-thumbnail rounded"
                     style="max-width: 200px; height: auto;">
            </div>
        <?php endif; ?>

        <!-- Nueva imagen -->
        <div class="mb-3">
            <label for="image" class="form-label">Cambiar imagen (opcional)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>

        <!-- Vista previa de nueva imagen -->
        <div class="mb-3" id="preview-container" style="display: none;">
            <label class="form-label">Vista previa:</label><br>
            <img id="preview-image" class="img-thumbnail rounded" style="max-width: 200px;">
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-between mt-4">
            <a href="<?php echo e(route('posts.index')); ?>" class="btn btn-secondary">
                ← Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                💾 Actualizar
            </button>
        </div>
    </form>
</div>

<!-- Script de preview -->
<script>
    document.getElementById('image').addEventListener('change', function (event) {
        const input = event.target;
        const preview = document.getElementById('preview-image');
        const container = document.getElementById('preview-container');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            container.style.display = 'none';
            preview.src = '';
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Blog\resources\views/posts/edit.blade.php ENDPATH**/ ?>