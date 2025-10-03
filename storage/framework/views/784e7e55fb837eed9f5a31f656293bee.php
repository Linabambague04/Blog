<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h2 class="mb-4">Editar Perfil</h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Nombre -->
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="<?php echo e(old('name', $user->name)); ?>" required>
        </div>

        <?php if($user->avatar): ?>
            <div class="mb-3">
                <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" 
                     alt="Avatar"
                     class="rounded-circle" 
                     style="width: 80px; height: 80px; object-fit: cover;">
            </div>
        <?php endif; ?> 

        <div class="mb-3">
            <label for="avatar" class="form-label">Cambiar avatar</label>
            <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="<?php echo e(route('posts.index')); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Blog\resources\views/auth/edit.blade.php ENDPATH**/ ?>