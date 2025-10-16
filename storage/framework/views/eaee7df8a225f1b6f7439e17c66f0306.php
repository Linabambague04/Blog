

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">
            <i class="bi bi-bell-fill me-2"></i> Mis notificaciones
        </h2>
        <?php if(session('success')): ?>
            <div class="alert alert-success mb-0 py-2 px-3 shadow-sm">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $notificaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card mb-3 border-0 shadow-sm rounded-4">
            <div class="card-body position-relative">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                              style="width: 45px; height: 45px;">
                            <i class="bi bi-info-lg fs-5"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1 fw-semibold text-dark"><?php echo e($n->title); ?></h5>
                        <p class="card-text text-muted mb-2"><?php echo e($n->message); ?></p>
                        <small class="text-secondary">
                            <i class="bi bi-clock"></i> <?php echo e($n->created_at->diffForHumans()); ?>

                        </small>
                    </div>
                </div>

                <form action="<?php echo e(route('notifications.destroy', $n)); ?>" method="POST"
                      class="position-absolute top-0 end-0 mt-2 me-2">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-outline-danger rounded-circle" title="Eliminar notificación">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center text-muted mt-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p class="fs-5">No tienes notificaciones por ahora.</p>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-center mt-4">
        <?php echo e($notificaciones->links()); ?>

    </div>
</div>


<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Blog\resources\views/notifications/index.blade.php ENDPATH**/ ?>