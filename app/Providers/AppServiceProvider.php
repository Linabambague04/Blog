<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usa Bootstrap para los links de paginación
        Paginator::useBootstrap();

        // Carga notificaciones para el navbar
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $navbarNotifications = Notification::where('user_id', Auth::id())
                    ->orWhereNull('user_id')
                    ->latest()
                    ->take(5)
                    ->get();

                $navbarNotifCount = Notification::where('user_id', Auth::id())
                    ->orWhereNull('user_id')
                    ->count();

                $view->with(compact('navbarNotifications', 'navbarNotifCount'));
            }
        });
    }
}
