<?php $__env->startSection('content'); ?>
<div class="container py-4">

    <div class="card shadow-sm border-0">
        <?php if($post->image): ?>
            <img src="<?php echo e(asset('storage/' . $post->image)); ?>"
                 alt="Imagen del post"
                 class="card-img-top rounded-top"
                 style="max-height: 400px; object-fit: cover;">
        <?php endif; ?>

        <div class="card-body">
            <!-- Título -->
            <h1 class="card-title text-primary"><?php echo e($post->title); ?></h1>

            <!-- Autor y fecha -->
            <p class="text-muted mb-3">
                Publicado por <strong><?php echo e($post->user->name ?? 'Usuario desconocido'); ?></strong>
                el <?php echo e($post->created_at->format('d/m/Y')); ?>

            </p>

            <!-- Contenido -->
            <div class="mb-4 text-secondary" style="white-space: pre-line;">
                <?php echo e($post->content); ?>

            </div>

            <!-- Botones -->
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('posts.index')); ?>" class="btn btn-outline-secondary">
                    ← Volver
                </a>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $post)): ?>
                    <a href="<?php echo e(route('posts.edit', $post)); ?>" class="btn btn-outline-primary">
                        ✏️ Editar
                    </a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $post)): ?>
                    <form action="<?php echo e(route('posts.destroy', $post)); ?>" method="POST" class="d-inline"
                          onsubmit="return confirm('¿Seguro que deseas eliminar este post?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-outline-danger">
                            🗑️ Eliminar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Blog\resources\views/posts/show.blade.php ENDPATH**/ ?>