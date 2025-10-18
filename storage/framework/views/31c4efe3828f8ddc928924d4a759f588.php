<?php $__env->startSection('title', 'Lista de Posts'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold">Lista de Publicaciones</h1>
        
        <?php if($recommendation && !empty($recommendation->recommended_items['ideas_nuevos_posts'])): ?>
            <div class="card shadow border-0 mb-4 position-fixed top-50 start-50 translate-middle" 
                 style="z-index: 1050; max-width: 500px; animation: slideIn 0.5s ease-out;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-primary mb-0 fw-bold">
                            <i class="bi bi-lightbulb-fill me-2"></i>Recomendación para ti
                        </h5>
                        <button type="button" class="btn-close" onclick="closeRecommendation()"></button>
                    </div>
                    
                    <div id="recommendationCarousel" class="position-relative">
                        <?php $__currentLoopData = $recommendation->recommended_items['ideas_nuevos_posts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $idea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="recommendation-item <?php echo e($index === 0 ? 'active' : ''); ?>" 
                                 data-index="<?php echo e($index); ?>">
                                <div class="bg-light rounded p-3 mb-3">
                                    <h6 class="text-dark fw-bold mb-2">
                                        <i class="bi bi-stars text-warning me-1"></i><?php echo e($idea['titulo']); ?>

                                    </h6>
                                    <p class="text-muted mb-0 small"><?php echo e($idea['descripcion']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex gap-2">
                            <?php $__currentLoopData = $recommendation->recommended_items['ideas_nuevos_posts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $idea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="carousel-indicator <?php echo e($index === 0 ? 'active' : ''); ?>" 
                                      data-index="<?php echo e($index); ?>"
                                      style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo e($index === 0 ? '#0d6efd' : '#dee2e6'); ?>; cursor: pointer;"
                                      onclick="goToSlide(<?php echo e($index); ?>)"></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <small class="text-muted">
                            <span id="currentSlide">1</span> / <?php echo e(count($recommendation->recommended_items['ideas_nuevos_posts'])); ?>

                        </small>
                    </div>
                </div>
            </div>
            
            <div class="modal-backdrop fade show" id="recommendationBackdrop" 
                 style="z-index: 1040;" onclick="closeRecommendation()"></div>
        <?php endif; ?>

        <a href="<?php echo e(route('posts.create')); ?>" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Post
        </a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
    <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col">
            <div class="card h-100 shadow-sm border-0 hover-lift">
                <a href="<?php echo e(route('posts.show', $post->id)); ?>" class="text-decoration-none text-dark d-block">
                    <?php if($post->image): ?>
                        <div class="position-relative overflow-hidden" style="height: 180px;">
                            <img src="<?php echo e(asset('storage/' . $post->image)); ?>"
                                alt="Imagen del post"
                                class="w-100 h-100"
                                style="object-fit: cover; transition: transform 0.3s ease;">
                        </div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold mb-2"><?php echo e($post->title); ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?php echo e(Str::limit($post->content, 100)); ?></p>

                        <div class="d-flex align-items-center mt-2">
                            <?php if($post->user->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . $post->user->avatar)); ?>"
                                    alt="Avatar del autor"
                                    class="rounded-circle me-2 border"
                                    style="width: 32px; height: 32px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                     style="width: 32px; height: 32px; font-size: 14px;">
                                    <?php echo e(substr($post->user->name, 0, 1)); ?>

                                </div>
                            <?php endif; ?>
                            <small class="text-muted fw-bold"><?php echo e($post->user->name); ?></small>
                        </div>
                    </div>
                </a>

                <div class="px-3 py-2 border-top">
                    <button 
                        class="btn btn-link p-0 border-0 text-decoration-none" 
                        onclick="toggleLike(<?php echo e($post->id); ?>)"
                        style="font-size: 1.3rem;">
                        <i id="heart-icon-<?php echo e($post->id); ?>" 
                           class="bi <?php echo e($post->isLikedBy(Auth::user()) ? 'bi-heart-fill' : 'bi-heart'); ?>"
                           style="color: <?php echo e($post->isLikedBy(Auth::user()) ? '#dc3545' : '#6c757d'); ?>; transition: all 0.2s;"></i>
                    </button>
                    <span id="like-count-<?php echo e($post->id); ?>" class="ms-1 text-muted small fw-bold">
                        <?php echo e($post->likeCount()); ?>

                    </span>
                </div>

                <div class="card-footer bg-white border-top-0 d-flex gap-2 pt-0 pb-3">
                    <?php if(auth()->id() === $post->user_id): ?>
                        <a href="<?php echo e(route('posts.edit', $post->id)); ?>" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>

                        <form action="<?php echo e(route('posts.destroy', $post->id)); ?>" method="POST"
                            onsubmit="return confirm('¿Seguro que deseas eliminar este post?')" class="w-100 m-0 p-0">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('posts.show', $post->id)); ?>" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-eye me-1"></i>Ver
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="alert alert-info text-center shadow-sm border-0">
                <i class="bi bi-info-circle me-2"></i>No hay publicaciones aún.
            </div>
        </div>
    <?php endif; ?>
    </div>

    <div class="mt-4">
        <?php echo e($posts->links()); ?>

    </div>
</div>

<style>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.hover-lift:hover img {
    transform: scale(1.05);
}

.recommendation-item {
    display: none;
    animation: fadeIn 0.5s ease;
}

.recommendation-item.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.carousel-indicator {
    transition: all 0.3s ease;
}

.carousel-indicator:hover {
    transform: scale(1.2);
}
</style>

<script>
let currentIndex = 0;
let carouselInterval;
const SLIDE_DURATION = 4000; // 4 segundos por recomendación

function startCarousel() {
    const items = document.querySelectorAll('.recommendation-item');
    if (items.length <= 1) return;

    carouselInterval = setInterval(() => {
        currentIndex = (currentIndex + 1) % items.length;
        showSlide(currentIndex);
    }, SLIDE_DURATION);
}

function showSlide(index) {
    const items = document.querySelectorAll('.recommendation-item');
    const indicators = document.querySelectorAll('.carousel-indicator');
    
    items.forEach((item, i) => {
        item.classList.toggle('active', i === index);
    });
    
    indicators.forEach((indicator, i) => {
        indicator.style.background = i === index ? '#0d6efd' : '#dee2e6';
    });
    
    document.getElementById('currentSlide').textContent = index + 1;
}

function goToSlide(index) {
    clearInterval(carouselInterval);
    currentIndex = index;
    showSlide(index);
    startCarousel();
}

function closeRecommendation() {
    clearInterval(carouselInterval);
    const backdrop = document.getElementById('recommendationBackdrop');
    const card = backdrop.previousElementSibling;
    
    if (backdrop) backdrop.remove();
    if (card) card.remove();
}

// Iniciar el carrusel cuando cargue la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.recommendation-item')) {
        startCarousel();
        
        // Cerrar automáticamente después de mostrar todas las recomendaciones
        const totalItems = document.querySelectorAll('.recommendation-item').length;
        setTimeout(() => {
            closeRecommendation();
        }, SLIDE_DURATION * totalItems + 1000);
    }
});

async function toggleLike(postId) {
    const response = await fetch(`/posts/${postId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    });

    const data = await response.json();
    const icon = document.getElementById(`heart-icon-${postId}`);
    const count = document.getElementById(`like-count-${postId}`);
    const button = icon.parentElement;


    button.style.transform = 'scale(1.3)';
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 200);

    if (data.status === 'liked') {
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
        icon.style.color = '#dc3545';
    } else {
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
        icon.style.color = '#6c757d';
    }

    count.textContent = data.count;
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Blog\resources\views/posts/index.blade.php ENDPATH**/ ?>