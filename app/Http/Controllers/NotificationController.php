<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getAllNotifications(Request $request)
    {
        $notifications = Notification::with('user')->orderByDesc('created_at')->get();
        if($notifications) {
            $unreadCount = $notifications->where('read', false)->count();
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function markNotificationAsRead(Request $request)
    {
        $id = $request->input("id");
       $notification = Notification::find($id);
       if($notification) {
           if(!$notification->read) {
               $notification->update(['read' => true]);
           }
           $userId = $notification->user_id;
           $user = User::find($userId);
           $chat = $user->chat;
           return response()->json([
               'success' => true,
               'chat' => $chat
           ]);
       }

    }

}
