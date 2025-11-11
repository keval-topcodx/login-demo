<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (auth()->check()) {
            $notifications = Notification::with('user')
                ->where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->get();

            $unreadCount = $notifications->where('read', false)->count();

            View::share(compact('notifications', 'unreadCount'));
        }
    }
}
