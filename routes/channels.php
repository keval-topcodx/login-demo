<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('notification.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('support', function ($user) {
    return $user->getRoleNames()->contains(function ($role) {
        return in_array($role, ['admin', 'agent']);
    });
});

Broadcast::channel('notification', function ($user) {
    return $user->getRoleNames()->contains(function ($role) {
        return in_array($role, ['admin', 'agent']);
    });
});



//Broadcast::channel('support.{userId}', function ($user, $userId) {
//    return $user->getRoleNames()->contains(function ($role) {
//        return in_array($role, ['admin', 'agent']);
//    });
//});
