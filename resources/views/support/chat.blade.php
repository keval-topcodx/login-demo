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
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <div class="col-3 border-end d-flex flex-column p-0 bg-light vh-100">
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
                            <li class="list-group-item chat-item active" data-chat="1">
                                <strong>John Doe</strong>
                                <p class="mb-0 text-truncate">Hey! How are you?</p>
                            </li>
                            <li class="list-group-item chat-item" data-chat="2">
                                <strong>Jane Smith</strong>
                                <p class="mb-0 text-truncate">Let’s meet tomorrow.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-9 d-flex flex-column p-0">
                <div class="border-bottom p-3">
                    <h5 id="chatTitle">Select a chat</h5>
                </div>

                <div id="chatMessages" class="flex-grow-1 p-3 overflow-auto" style="background-color: #f7f7f7;">
                    <p class="text-muted">No chat selected</p>
                </div>

                <div class="border-top p-3">
                    <form id="chatForm" class="d-flex gap-1">
                        <input type="text" name="message" class="form-control flex-grow-1" placeholder="Type a message..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="newChatCard" class="card p-5 position-fixed top-50 start-50 translate-middle d-none shadow-lg new-conversation-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Start New Conversation</h5>
            <button id="closeNewChat" class="btn-close"></button>
        </div>

        <div class="mb-3">
            <input id="searchUser" type="text" class="form-control form-control-lg" placeholder="Search user..." autocomplete="off">
        </div>

        <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item list-group-item-action">John Doe</li>
            <li class="list-group-item list-group-item-action">Jane Smith</li>
            <li class="list-group-item list-group-item-action">Mike Ross</li>
        </ul>

        <button class="btn btn-primary w-100 btn-lg rounded-pill mt-auto">Start Chat</button>
    </div>



    @vite(['resources/js/chat.js'])
@endsection
