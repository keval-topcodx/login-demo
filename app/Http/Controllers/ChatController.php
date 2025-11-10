<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessages;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        $usersWithoutChat = User::doesntHave('chat')
            ->where('id', '!=', auth()->id())
            ->get();

        $usersWithArchivedChat = User::where('id', '!=', auth()->id())
            ->whereHas('chat', function ($query) {
                $query->where('archived', 1);
            })
            ->whereHas('chat.messages')
            ->with(['chat.messages' => function ($query) {
                $query->orderByDesc('updated_at');
            }])
            ->orderByDesc(
                ChatMessages::select('chat_messages.updated_at')
                    ->join('chats', 'chat_messages.chat_id', '=', 'chats.id')
                    ->whereColumn('chats.user_id', 'users.id')
                    ->orderByDesc('chat_messages.updated_at')
                    ->limit(1)
            )
            ->get();

        $usersWithActiveChat = User::where('id', '!=', auth()->id())
            ->whereHas('chat', function ($query) {
                $query->where('archived', 0);
            })
            ->whereHas('chat.messages')
            ->with(['chat.messages' => function ($query) {
                $query->orderByDesc('updated_at');
            }])
            ->orderByDesc(
                ChatMessages::select('chat_messages.updated_at')
                    ->join('chats', 'chat_messages.chat_id', '=', 'chats.id')
                    ->whereColumn('chats.user_id', 'users.id')
                    ->orderByDesc('chat_messages.updated_at')
                    ->limit(1)
            )
            ->get();

        return view('support.chat', ['users' => $users, 'usersWithArchivedChat' => $usersWithArchivedChat, 'usersWithActiveChats' => $usersWithActiveChat ,  'usersWithoutChat' => $usersWithoutChat]);
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
    public function destroy(Chat $chat)
    {

        if(!$chat) {
            return response()->json([
               'success' => false,
               'message' => 'There was an error.'
            ]);
        }
        $chat->load('messages');

        foreach ($chat->messages as $message) {
            if($message->attachment_url) {
                $relativePath = str_replace(asset('storage') . '/', '', $message->attachment_url);

                Storage::disk('public')->delete($relativePath);
            }
            $message->delete();
        }

        $chat->delete();
        return response()->json(['success'=> true]);
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

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'nullable|string',
            'attachments.*' => 'file|max:2048',
            'sender' => 'required',
            'userId' => 'required_if:sender,admin|required_if:sender,agent',
        ]);
        $message = $validated['message'];

        $sender = $validated['sender'];
        if($sender == 'admin' || $sender == 'agent') {
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
                        'user_type' => $sender,
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

    public function deleteMessage(Request $request)
    {
        $id = $request->input('id');
        $chatMessage = ChatMessages::find($id);
        if (!$chatMessage) {
            return response()->json(['success' => false, 'message' => 'Message not found']);
        }
        if ($chatMessage->attachment_url) {
            $relativePath = str_replace(asset('storage') . '/', '', $chatMessage->attachment_url);
            Storage::disk('public')->delete($relativePath);
        }
        $chatMessage->delete();
        return response()->json(['success' => true]);

    }

    public function editMessage(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:chat_messages,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = ChatMessages::find($request->id);

        $message->update([
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function archiveChat(Request $request)
    {
        $id = $request->input('id');

        $chat = Chat::find($id);

        if($chat) {
            $chat->update([
               'archived' => 1
            ]);
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
               'success' => false
            ]);
        }
    }

    public function unArchiveChat(Request $request)
    {
        $id = $request->input('id');

        $chat = Chat::find($id);

        if($chat) {
            $chat->update([
                'archived' => 0
            ]);
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function loadArchivedChats(Request $request)
    {
        $usersWithArchivedChat = User::whereHas('chat', function ($query) {
                $query->where('archived', 1);
            })
            ->whereHas('chat.messages')
            ->with(['chat.messages' => function ($query) {
                $query->orderByDesc('updated_at');
            }, 'media'])
            ->orderByDesc(
                ChatMessages::select('chat_messages.updated_at')
                    ->join('chats', 'chat_messages.chat_id', '=', 'chats.id')
                    ->whereColumn('chats.user_id', 'users.id')
                    ->orderByDesc('chat_messages.updated_at')
                    ->limit(1)
            )
            ->get();

        return response()->json([
            'success' => true,
            'users' => $usersWithArchivedChat
        ]);
    }

    public function loadActiveChats(Request $request)
    {
        $usersWithActiveChat = User::where('id', '!=', auth()->id())
            ->whereHas('chat', function ($query) {
                $query->where('archived', 0);
            })
            ->whereHas('chat.messages')
            ->with(['chat.messages' => function ($query) {
                $query->orderByDesc('updated_at');
            }, 'media'])
            ->orderByDesc(
                ChatMessages::select('chat_messages.updated_at')
                    ->join('chats', 'chat_messages.chat_id', '=', 'chats.id')
                    ->whereColumn('chats.user_id', 'users.id')
                    ->orderByDesc('chat_messages.updated_at')
                    ->limit(1)
            )
            ->get();

        return response()->json([
            'success' => true,
            'users' => $usersWithActiveChat
        ]);
    }

    public function chatSearch(Request $request) {
        $validated = $request->validate([
            'value' => ['nullable', 'string'],
            'from' => ['required', 'in:active,archived'],
        ]);

        if($validated) {
            $value = $validated['value'];
            $from = $validated['from'];

            if($from == 'active') {
                $users = User::whereRaw("CONCAT(first_name, last_name) LIKE ?", ["%{$value}%"])
                    ->whereHas('chat', function ($query) {
                        $query->where('archived', 0);
                    })
                    ->whereHas('chat.messages')
                    ->with(['chat.messages' => function ($query) {
                        $query->orderByDesc('updated_at');
                    }, 'media'])
                    ->orderByDesc(
                        ChatMessages::select('chat_messages.updated_at')
                            ->join('chats', 'chat_messages.chat_id', '=', 'chats.id')
                            ->whereColumn('chats.user_id', 'users.id')
                            ->orderByDesc('chat_messages.updated_at')
                            ->limit(1)
                    )
                    ->get();

                return response()->json([
                    'success' => true,
                    'users' => $users
                ]);
            } elseif ($from == 'archived') {
                $users = User::whereRaw("CONCAT(first_name, last_name) LIKE ?", ["%{$value}%"])
                    ->whereHas('chat', function ($query) {
                        $query->where('archived', 1);
                    })
                    ->whereHas('chat.messages')
                    ->with(['chat.messages' => function ($query) {
                        $query->orderByDesc('updated_at');
                    }, 'media'])
                    ->orderByDesc(
                        ChatMessages::select('chat_messages.updated_at')
                            ->join('chats', 'chat_messages.chat_id', '=', 'chats.id')
                            ->whereColumn('chats.user_id', 'users.id')
                            ->orderByDesc('chat_messages.updated_at')
                            ->limit(1)
                    )
                    ->get();
                return response()->json([
                    'success' => true,
                    'users' => $users
                ]);
            }

            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
           'success' => false
        ]);
    }

}
