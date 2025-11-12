<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
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
        View::composer('*', function ($view) {
            $user = Auth::user();

            if($user->hasAnyRole(['admin', 'agent'])) {
                $notifications = Notification::with('user')
                    ->where('user_type', '=', 'user')
                    ->orderByDesc('created_at')
                    ->get();

                $unreadCount = $notifications->where('read', false)->count();
                $view->with([
                    'authUser' => $user,
                    'notifications' => $notifications,
                    'unreadCount' => $unreadCount,
                ]);
            } else {
                $notifications = $user->notifications()
                    ->whereIn('user_type', ['admin', 'agent'])
                    ->with('user')
                    ->latest()
                    ->take(10)
                    ->get();

                $unreadCount = $user->notifications()
                    ->whereIn('user_type', ['admin', 'agent'])
                    ->where('read', false)
                    ->count();

                $view->with([
                    'authUser' => $user,
                    'notifications' => $notifications,
                    'unreadCount' => $unreadCount,
                ]);

            }



            if ($user) {
                // Fetch notifications for this user
//                $notifications = $user->notifications()
//                    ->with('user')
//                    ->latest()
//                    ->take(10)
//                    ->get();

                // Count unread notifications
//                $unreadCount = $user->notifications()->where('read', false)->count();

                // Share with all views (e.g. layout.blade.php)
//                $view->with([
//                    'authUser' => $user,
//                    'notifications' => $notifications,
//                    'unreadCount' => $unreadCount,
//                ]);
            }
        });

    }
}
