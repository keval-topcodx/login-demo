<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $usersWithChat = User::has('chat')->with('chat')->get();
        $usersWithoutChat = User::doesntHave('chat')->get();



        return view('support.chat', ['users' => $users, 'usersWithChat' => $usersWithChat, 'usersWithoutChat' => $usersWithoutChat]);
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

    public function startNewChat(Request $request)
    {
        $id = $request->input("id");

        $user = User::with('media')->find($id);
        if($user) {
            return response()->json([
               'success' => true,
               'user' => $user
            ]);
        } else {
            return response()->json([
               'success' => false,
               'message' => 'There was an error.'
            ]);
        }
    }
    public function sendUserMessage(Request $request)
    {
        $validated = $request->validated([
           'message' => 'nullable|string',
           'attachments.*' => 'file|max:2048',
        ]);
        $message = $validated['message'];
        $user = auth()->user();
        $chat = $user->chat()->firstOrCreate();

        $chatMessages = [];

        if ($message) {
            $chatMessages[] = $chat->messages()->create([
                'user_type' => 'admin',
                'message' => $message
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $url = asset('storage/' . $path);
                $name = $file->getClientOriginalName();

                $chatMessages[] = $chat->messages()->create([
                    'user_type' => 'admin',
                    'attachment_name' => $name,
                    'attachment_url' => $url,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'chatData' => $chatMessages
        ]);

    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'nullable|string',
            'attachments.*' => 'file|max:2048',
            'sender' => 'required',
            'userId' => 'required_if:sender,admin'
        ]);
        $message = $validated['message'];

        $sender = $validated['sender'];
        if($sender == 'admin') {
            $userId = $validated['userId'];
        } else if($sender == 'user') {
            $userId = auth()->user()->id;
        }
        if($userId) {
            $user = User::find($userId);

            $chat = Chat::firstOrCreate([
                'user_id' => $user->id,
            ]);

//        repeat
            $chatMessage = [];
            if ($message) {
                $chatMessage[] = $chat->messages()->create([
                    'user_type' => $sender,
                    'message' => $message
                ]);
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    $url = asset('storage/' . $path);
                    $name = $file->getClientOriginalName();

                    $chatMessage[] = $chat->messages()->create([
                        'user_type' => 'admin',
                        'attachment_name' => $name,
                        'attachment_url' => $url,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'chatData' => $chatMessage
            ]);
        } else {
            return response()->json([
               'success' => false,
               'message' => 'there was an error.'
            ]);
        }
    }

    public function loadMessages(Request $request)
    {
        $action = $request->input('action');
        if($action == 'forSupport') {
            $userId = $request->input('id');
        } else if($action == 'forUser') {
            $userId = auth()->user()->id;
        }
        $user = User::with('chat.messages')->find($userId);

        $chat = $user->chat;
        if ($chat) {
            $messages = $chat->messages;
            return response()->json([
               'success' => true,
                'messages' => $messages
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'There was an error getting chat.'
            ]);
        }


    }
}
