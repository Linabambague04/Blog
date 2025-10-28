

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h2 class="mb-4">Editar Perfil</h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <!-- FORMULARIO EDITAR PERFIL -->
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

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="<?php echo e(route('posts.index')); ?>" class="btn btn-secondary">Cancelar</a>
    </form>

    <!-- FORMULARIO ELIMINAR PERFIL -->
<hr class="my-4">
<h4>Eliminar cuenta</h4>
<p class="text-danger">Esta acción no se puede deshacer. Todos tus datos serán eliminados.</p>

<form action="<?php echo e(route('profile.destroy')); ?>" method="POST" style="max-width: 400px;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>

    <div class="mb-3">
        <label for="password" class="form-label">Confirma tu contraseña</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="alert alert-danger"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <button type="submit" class="btn btn-danger">Eliminar mi cuenta</button>
</form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Blog\resources\views/auth/edit.blade.php ENDPATH**/ ?>