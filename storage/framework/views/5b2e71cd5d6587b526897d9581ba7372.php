

<?php $__env->startSection('content'); ?>
<div class="bg-white">

    <!-- Hero Section -->
    <section class="hero-section text-white text-center">
        <div class="container position-relative z-1">
            <h1 class="display-4 fw-bold mb-4">
                Bienvenido a <span class="text-warning">Mi Blog</span>
            </h1>
            <p class="lead mb-5">
                Comparte tus ideas, explora publicaciones y crea tu propio espacio en línea de forma sencilla y moderna.
            </p>

            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('posts.create')); ?>" class="btn btn-warning btn-lg fw-bold">
                    Crear nueva publicación
                </a>
            <?php endif; ?>
        </div>

        <!-- Decoración inferior -->
        <div class="position-absolute bottom-0 start-0 w-100 hero-deco"></div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row text-center g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="p-4 shadow-sm h-100 rounded hover-shadow">
                        <div class="fs-1 text-primary mb-3">
                            <i class="bi bi-pen"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Publica fácilmente</h5>
                        <p class="text-muted">
                            Escribe artículos y compártelos con otros usuarios sin complicaciones.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="p-4 shadow-sm h-100 rounded hover-shadow">
                        <div class="fs-1 text-primary mb-3">
                            <i class="bi bi-camera"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Sube imágenes</h5>
                        <p class="text-muted">
                            Haz tus publicaciones visualmente atractivas con imágenes relevantes.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="p-4 shadow-sm h-100 rounded hover-shadow">
                        <div class="fs-1 text-primary mb-3">
                            <i class="bi bi-person"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Perfil personalizado</h5>
                        <p class="text-muted">
                            Agrega tu avatar, edita tu información y personaliza tu experiencia.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-light text-center">
        <div class="container">
            <h2 class="fw-bold mb-3 text-dark">¿Listo para empezar?</h2>
            <p class="text-muted mb-4">
                Crea tu cuenta o inicia sesión para comenzar a compartir tus ideas.
            </p>

            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('posts.create')); ?>" class="btn btn-primary btn-lg">
                    Crear nueva publicación
                </a>
            <?php endif; ?>
        </div>
    </section>

</div>

<style>
    .hero-section {
        background: linear-gradient(to right, #4f46e5, #3b82f6);
        padding: 100px 20px;
        position: relative;
        overflow: hidden;
    }

    .hero-deco {
        height: 100px;
        background-color: white;
        border-top-left-radius: 2rem;
        border-top-right-radius: 2rem;
        box-shadow: 0 -5px 10px rgba(0, 0, 0, 0.1);
    }

    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        transform: scale(1.03);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .z-1 {
        z-index: 1;
    }

    .text-warning {
        color: #ffdc5d !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Blog\resources\views/home.blade.php ENDPATH**/ ?>