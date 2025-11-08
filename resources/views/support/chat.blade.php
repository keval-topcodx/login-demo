@extends('layout.app')

@section('title', 'Menu')

@section('content')
    @if(request('message'))
        <div class="alert alert-success">
            {{ request('message') }}
        </div>
    @endif
    @if (session('danger'))
        <div class="alert alert-danger">
            {{ session('danger') }}
        </div>
    @endif
    <div class="container-fluid vh-90">
        <div class="row h-90">
            <div class="col-3 border-end d-flex flex-column p-0 bg-light" style="height: 90vh">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0">Conversations</h5>
                    <button id="newChatBtn" class="btn btn-sm btn-primary">New Chat</button>
                </div>

                <div class="p-3 border-bottom bg-light">
                    <div class="input-group">
                        <input type="text" id="chatSearch" class="form-control border-start-0" placeholder="Search users...">
                    </div>
                </div>


                <div class="flex-grow-1 d-flex flex-column">
                    <div class="chat-type-buttons w-100 d-flex mb-2">
                        <button id="activeBtn" class="btn btn-primary w-50 rounded-0">Active Chats</button>
                        <button id="archiveBtn" class="btn btn-outline-primary w-50 rounded-0">Archived Chats</button>
                    </div>

                    <div class="flex-grow-1 overflow-auto border-bottom" style="min-height: 50%;">
                        <ul class="list-group list-group-flush" id="activeChats">
                            @foreach($usersWithChat as $user)
                                <li class="list-group-item chat-item user-with-chat-profile"
                                    data-user-id="{{$user->id}}"
                                    data-user-firstName="{{$user->first_name}}"
                                    data-user-lastName="{{$user->last_name}}"
                                    data-user-image="{{$user->image_url}}"
                                    data-user-email="{{$user->email}}"
                                >
                                    <div class="d-flex align-items-center gap-3 border-0 bg-transparent user-info hover-bg"
                                         style="min-height: 50px;">
                                        <div class="flex-shrink-0" style="width: 50px; height: 50px;">
                                            <img src="{{$user->image_url}}"
                                                 alt="User Avatar"
                                                 class="rounded-circle w-100 h-100"
                                                 style="object-fit: cover; object-position: center; border: 2px solid #f1f1f1;">
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-0 fw-semibold text-truncate">{{$user->first_name . " " . $user->last_name}}</h6>
                                            <small class="text-muted text-truncate d-block">{{$user->chat->messages()->latest()->first()->message ?? $user->chat->messages()->latest()->first()->attachment_name}}</small>
                                        </div>
                                    </div>
{{--                                    delete --}}
                                    <div class="dropdown">
                                        <button class="dropdown-menu-button btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            &vellip;
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item delete-chats" data-chat-id="{{$user->chat->id}}">Delete</a></li>
                                        </ul>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-9 d-flex flex-column p-0" id="chatWindow">
                <div class="border-bottom p-3">
                    <div id="chatTitle"><h5>Select a chat</h5></div>
                </div>

                <div id="chatMessages" class="flex-grow-1 p-3 overflow-auto" style="background-color: #f7f7f7; height: 500px; border-radius: 10px;">
                    <div class="d-flex align-items-center justify-content-center h-100 chat-selected-info">
                        <p class="chats-info">No chat selected...</p>
                    </div>
                    <!-- Example messages -->
{{--                    <div class="message-row d-flex justify-content-start mb-3">--}}
{{--                        <p class="message-bubble bg-light text-dark">Hey there! 👋</p>--}}
{{--                    </div>--}}

{{--                    <div class="message-row d-flex justify-content-end mb-3">--}}
{{--                        <p class="message-bubble bg-primary text-white">Hello! How are you?</p>--}}
{{--                    </div>--}}
                </div>

                <div class="border-top p-3">
                    <form id="chatForm" class="d-flex gap-1"
                          @if(auth()->user()->hasRole('admin'))
                              data-user="admin"
                          @elseif(auth()->user()->hasRole('agent'))
                              data-user="agent"
                          @endif
                    >
{{--                        <input type="text" name="message" class="form-control flex-grow-1" placeholder="Type a message..." autocomplete="off">--}}
{{--                        <button type="submit" class="btn btn-primary">Send</button>--}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="newChatCard" class="card p-5 position-fixed top-50 start-50 translate-middle shadow-lg new-conversation-card d-none">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Start New Conversation</h5>
            <button id="closeNewChat" class="btn-close"></button>
        </div>

        <div class="mb-3">
            <input id="searchUser" type="text" class="form-control form-control-lg" placeholder="Search user..." autocomplete="off">
        </div>

        <ul class="list-group list-group-flush mb-3 new-chat-list overflow-auto" style="max-height: 300px;">
            @foreach($usersWithoutChat as $user)
                <li class="list-group-item chat-item d-flex align-items-center gap-3 py-2 border-0 border-bottom bg-transparent user-chat-list hover-bg" style="min-height: 70px;" data-user-id="{{$user->id}}">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px;">
                        <img src="{{ $user->image_url }}"
                             alt="User Avatar"
                             class="rounded-circle w-100 h-100"
                             style="object-fit: cover; object-position: center; border: 2px solid #f1f1f1;">
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <h6 class="mb-0 fw-semibold text-truncate">{{ $user->first_name . ' ' . $user->last_name }}</h6>
                        <small class="text-muted text-truncate d-block">{{ $user->email }}</small>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>



    @vite(['resources/js/chat.js'])
@endsection
