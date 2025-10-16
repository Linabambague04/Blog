<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::resource('posts', PostController::class);
});

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [ChatController::class, 'send'])->name('chat.send');


Route::middleware(['auth'])->group(function () {
    // ✅ Listar todas las notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    // ✅ Mostrar una notificación específica (opcional)
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');

    // ✅ Eliminar una notificación (opcional)
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});

Route::post('/posts/{post}/like', [App\Http\Controllers\PostLikeController::class, 'toggleLike'])
    ->name('posts.like')
    ->middleware('auth');





 