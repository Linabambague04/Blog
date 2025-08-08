

<?php $__env->startSection('title', 'Lista de Posts'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary">Lista de Publicaciones</h1>
        <a href="<?php echo e(route('posts.create')); ?>" class="btn btn-success">+ Nuevo Post</a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <?php if($post->image): ?>
                        <img src="<?php echo e(asset('storage/' . $post->image)); ?>" 
                             alt="Imagen del post"
                             class="card-img-top rounded-top" 
                             style="height: 180px; object-fit: cover;">
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-dark"><?php echo e($post->title); ?></h5>
                        <p class="card-text text-muted"><?php echo e(Str::limit($post->content, 100)); ?></p>

                        <div class="d-flex align-items-center my-2">
                            <?php if($post->user->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . $post->user->avatar)); ?>" 
                                     alt="Avatar del autor"
                                     class="rounded-circle me-2"
                                     style="width: 32px; height: 32px; object-fit: cover;">
                            <?php endif; ?>
                            <small class="text-muted"><strong><?php echo e($post->user->name); ?></strong></small>
                        </div>

                        <div class="mt-auto d-flex gap-2">
                            <?php if(auth()->id() === $post->user_id): ?>
                                <a href="<?php echo e(route('posts.edit', $post->id)); ?>" class="btn btn-outline-primary btn-sm w-100">
                                    Editar
                                </a>

                                <form action="<?php echo e(route('posts.destroy', $post->id)); ?>" method="POST" 
                                      onsubmit="return confirm('¿Seguro que deseas eliminar este post?')" class="w-100">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">Eliminar</button>
                                </form>
                            <?php else: ?>
                                <a href="<?php echo e(route('posts.show', $post->id)); ?>" class="btn btn-outline-secondary btn-sm w-100">Ver</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col">
                <div class="alert alert-info text-center w-100">No hay publicaciones aún.</div>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <?php echo e($posts->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Blog\resources\views/posts/index.blade.php ENDPATH**/ ?>