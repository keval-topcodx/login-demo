import './app';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    let userId = $("#chatBox").data('user-id') ?? $("#chatWindow").find(".user-info").data('user-id') ?? null;

    Echo.private('user.' + userId)
        .listen('.message.sent', (e) => {
            e.message.forEach(msg => {
                if(msg.user_type == 'admin') {
                    addCardMessage(msg.message, msg.id, 'left');
                } else if (msg.user_type == 'user') {
                    addMessage(msg.message, msg.id, 'left');
                }
            });
        });


    loadActiveChats();
    $("#chatBox").hide();
    $("#chatButton").on("click", function () {
        $("#chatBox").toggle();
        $(".chat-messages").empty();
        $.ajax({
            url: '/load-messages',
            type: 'POST',
            data: {action: 'forUser'},
            success:function (response) {
                if(response.success) {
                    response.messages.forEach(function (message) {
                        const side = message['user_type'] === 'user' ? 'right' : 'left';
                        if(message['message']) {
                            addCardMessage(message['message'], message['id'], side);
                        } else if (message['attachment_name'] && message['attachment_url']) {
                            addCardAttachment(message['attachment_name'], message['attachment_url'], message['id'] , side);
                        }
                    });
                }
            }
        });
    });
    $("#closeChat").on("click", function () {
        $("#chatBox").hide();
    });

    $('#newChatBtn').on('click', function() {
        $('#newChatCard').removeClass('d-none');
    });

    $('#closeNewChat').on('click', function() {
        $('#newChatCard').addClass('d-none');
    });

    $("#activeBtn").on("click", function() {
        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
        $('#archiveBtn').removeClass('btn-primary active').addClass('btn-outline-primary');

        $('#activeChats').show();
        $('#archivedChats').hide();
        loadActiveChats();
    });
    function loadActiveChats() {
        $.ajax({
            url: '/load-active-chats',
            type: 'POST',
            data: {active: ''},
            success: function(response) {
                $("#activeChats").html('');
                if(response.success) {
                    response.users.forEach(function (user) {
                        $("#activeChats").append(getActiveChatHtml(user));
                    });
                }
            }
        })
    }
    function getActiveChatHtml(user) {
        const imageUrl = user.media?.[0]?.original_url ?? '/images/no-image.png';
        const latestMessage = user.chat?.messages?.[0]?.message
            ?? user.chat?.messages?.[0]?.attachment_name
            ?? '';

        return `
        <li class="list-group-item chat-item user-with-chat-profile"
            data-user-id="${user.id}"
            data-user-firstName="${user.first_name}"
            data-user-lastName="${user.last_name}"
            data-user-image="${imageUrl}"
            data-user-email="${user.email}"
        >
            <div class="d-flex align-items-center gap-3 border-0 bg-transparent user-info hover-bg"
                 style="min-height: 50px;">
                <div class="flex-shrink-0" style="width: 50px; height: 50px;">
                    <img src="${imageUrl}"
                         alt="User Avatar"
                         class="rounded-circle w-100 h-100"
                         style="object-fit: cover; object-position: center; border: 2px solid #f1f1f1;">
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="mb-0 fw-semibold text-truncate">${user.first_name + " " + user.last_name}</h6>
                    <small class="text-muted text-truncate d-block">
                    ${latestMessage}
                    </small>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropdown-menu-button btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    &vellip;
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item delete-chats" data-chat-id="${user.chat.id}">Delete</a></li>
                    <li><a class="dropdown-item archive-chat" data-chat-id="${user.chat.id}">Archive Chat</a> </li>
                </ul>
            </div>
        </li>
        `;
    }

    $("#chatSearch").on("input", function () {
       let searchValue = $(this).val();
       let searchFrom;
        if ($('#activeBtn').hasClass('active')) {
            searchFrom = 'active';
        } else if ($('#archiveBtn').hasClass('active')) {
            searchFrom = 'archived';
        }

        $.ajax({
            url: '/chat-search',
            type: 'POST',
            data: {value: searchValue, from: searchFrom},
            success: function (response) {
                if(response.success) {
                    if(searchFrom === 'active') {
                        $("#activeChats").html('');
                        response.users.forEach(function (user) {
                            $("#activeChats").append(getActiveChatHtml(user));
                        })
                    } else if (searchFrom === 'archived') {
                        $("#archivedChats").html('');
                        response.users.forEach(function (user) {
                            $("#archivedChats").append(getArchiveChatHtml(user));                        })
                    }
                }

            }
        });
    });

    $("#archiveBtn").on("click", function() {
        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
        $('#activeBtn').removeClass('btn-primary active').addClass('btn-outline-primary');

        $('#activeChats').hide();
        $('#archivedChats').show();
        loadArchiveChats();
    });

    function loadArchiveChats() {
        $.ajax({
            url: '/load-archived-chats',
            type: 'POST',
            data: {archive: ''},
            success: function (response) {
                $("#archivedChats").html('');
                if(response.success) {
                    response.users.forEach(function (user) {
                        $("#archivedChats").append(getArchiveChatHtml(user));
                    });
                }
            }
        });
    }
    function getArchiveChatHtml(user) {
        const imageUrl = user.media?.[0]?.original_url ?? '/images/no-image.png';
        const latestMessage = user.chat?.messages?.[0]?.message
            ?? user.chat?.messages?.[0]?.attachment_name
            ?? '';

        return `
        <li class="list-group-item chat-item user-with-chat-profile"
            data-user-id="${user.id}"
            data-user-firstName="${user.first_name}"
            data-user-lastName="${user.last_name}"
            data-user-image="${imageUrl}"
            data-user-email="${user.email}"
        >
            <div class="d-flex align-items-center gap-3 border-0 bg-transparent user-info hover-bg"
                 style="min-height: 50px;">
                <div class="flex-shrink-0" style="width: 50px; height: 50px;">
                    <img src="${imageUrl}"
                         alt="User Avatar"
                         class="rounded-circle w-100 h-100"
                         style="object-fit: cover; object-position: center; border: 2px solid #f1f1f1;">
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="mb-0 fw-semibold text-truncate">${user.first_name} ${user.last_name}</h6>
                    <small class="text-muted text-truncate d-block">${latestMessage}</small>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropdown-menu-button btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    &vellip;
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item delete-chats" data-chat-id="${user.chat.id}">Delete</a></li>
                    <li><a class="dropdown-item unarchive-chat" data-chat-id="${user.chat.id}">Unarchive Chat</a></li>
                </ul>
            </div>
        </li>
    `;
    }


    $(document).on("click", ".unarchive-chat", function (e) {
        e.preventDefault();
        let chatId = $(this).data('chat-id');
        const chatDiv = $(this).closest(".user-with-chat-profile");

        Swal.fire({
            title: 'Are you sure?',
            text: "Unarchive chat with customer?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("result confirmed");
                $.ajax({
                    url: `/unarchive-chat`,
                    type: 'POST',
                    data: {id: chatId},
                    success: function (response) {
                        if(response.success) {
                            Swal.fire(
                                'Unarchived!',
                                'Chat with customer has been unarchived.',
                                'success'
                            )
                            chatDiv.remove();
                        } else {
                            Swal.fire(
                                'ERROR!',
                                'There was an error unarchiving chat.',
                                'error'
                            )
                        }
                    }
                });
            }
        });
    });

    $(document).on("click", ".archive-chat", function (e) {
        e.preventDefault();
        let chatId = $(this).data('chat-id');
        const chatDiv = $(this).closest(".user-with-chat-profile");

        Swal.fire({
            title: 'Are you sure?',
            text: "Archive chat with customer?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("result confirmed");
                $.ajax({
                    url: `/archive-chat`,
                    type: 'POST',
                    data: {id: chatId},
                    success: function (response) {
                        if(response.success) {
                            Swal.fire(
                                'Archived!',
                                'Chat with customer has been archived.',
                                'success'
                            )
                            chatDiv.remove();
                        } else {
                            Swal.fire(
                                'ERROR!',
                                'There was an error archiving chat.',
                                'error'
                            )
                        }
                    }
                });

            }
        });
    });

    $(document).on("click", ".delete-chats", function (e) {
        e.preventDefault();
        let chatId = $(this).data('chat-id');
        const chatDiv = $(this).closest(".user-with-chat-profile");

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("result confirmed");
                $.ajax({
                    url: `/chat/${chatId}`,
                    type: 'DELETE',
                    data: {delete: ''},
                    success: function (response) {
                        if(response.success) {
                            Swal.fire(
                                'Deleted!',
                                'Your message has been deleted.',
                                'success'
                            )
                            chatDiv.remove();
                        } else {
                            Swal.fire(
                                'ERROR!',
                                'There was an error deleting message.',
                                'error'
                            )
                        }
                    }
                });

            }
        });
    });

    $(document).on('click', '.delete-message', function (e) {
        e.preventDefault();
        let messageId = $(this).data('chat-message-id');
        const messageDiv = $(this).closest('.chat-message');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                   url: '/delete-message',
                   type: 'POST',
                   data: {id: messageId},
                   success: function (response) {
                       if(response.success) {
                           Swal.fire(
                               'Deleted!',
                               'Your message has been deleted.',
                               'success'
                           )
                           messageDiv.remove();
                       } else {
                           Swal.fire(
                               'ERROR!',
                               'There was an error deleting message.',
                               'error'
                           )
                       }
                   }
                });

            }
        })
    });

    $(document).on('click', '.edit-message', function (e) {
        e.preventDefault();
        let messageId = $(this).data('chat-message-id');
        let messageMenu = $(this).closest(".message-menu");
        let container = $(this).closest(".chat-message");
        messageMenu.addClass("d-none");
        container.html(`
            <div class="edit-message-container">
                <input type="text" name="edited-message" class="form-control form-control-md edit-message-input" value="${container.data('message')}">
                <div class="d-flex justify-content-end gap-2 mt-1">
                    <button class="btn btn-sm btn-primary save-edit">Save</button>
                    <button class="btn btn-sm btn-secondary cancel-edit">Cancel</button>
                </div>
            </div>
        `);
    });

    $(document).on('click', '.save-edit', function () {
        let container = $(this).closest('.chat-message');
        let messageId = container.data('message-id');
        let newMessage = container.find('.edit-message-input').val();

        $.ajax({
            url: `/edit-message`,
            type: 'POST',
            data: {
                message: newMessage,
                id: messageId,
            },
            success: function (response) {
                if (response.success) {
                    container.html(`
                        <div class="message-bubble justify-content-end p-2 px-3 rounded-3 shadow-sm">
                            ${response.message.message}
                        </div>
                        <div class="message-menu position-absolute me-2 d-none">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    &vellip;
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item edit-message" data-chat-message-id="${response.message.id}">Edit</a></li>
                                    <li><a class="dropdown-item delete-message" data-chat-message-id="${response.message.id}">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                `);
                    container.data('message', response.message);
                } else {
                    Swal.fire('Error!', 'Could not update message.', 'error');
                }
            },
            error: function () {
                Swal.fire('Error!', 'AJAX request failed.', 'error');
            }
        });
    });

    $(document).on('click', '.cancel-edit', function () {
        let container = $(this).closest('.chat-message');
        let id = container.data('message-id');
        let oldMessage = container.data('message');
        container.html(`
            <div class="messageBubble bg-primary text-white p-2 px-3 rounded-3 shadow-sm">
                ${oldMessage}
            </div>
            <div class="message-menu position-absolute me-2 d-none">
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
                         aria-expanded="false">
                        &vellip;
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item edit-message" data-chat-message-id="${id}">Edit</a></li>
                        <li><a class="dropdown-item delete-message" data-chat-message-id="${id}">Delete</a></li>
                    </ul>
                </div>
            </div>
        `);
    });



    $('#activeChats').show();
    $('#archivedChats').hide();


    $(document).on("input", "#searchUser", function() {
        let searchValue = $(this).val().trim();
        $(".new-chat-list").empty();
        $.ajax({
            url: '/search-user',
            type: 'POST',
            data: {search: searchValue},
            success: function(response) {

                if(response.success) {
                    response.users.forEach(function (user) {

                        let imageUrl = (user.media && user.media.length > 0)
                            ? user.media[0].original_url
                            : "/images/no-image.png";

                        $(".new-chat-list").append(`
                            <li class="list-group-item chat-item d-flex align-items-center gap-3 py-2 border-0 border-bottom bg-transparent user-chat-list hover-bg"
                              style="min-height: 70px;"
                              data-user-id="${user.id}">
                                <div class="flex-shrink-0" style="width: 50px; height: 50px;">
                                    <img src="${imageUrl}"
                                         alt="User Avatar"
                                         class="rounded-circle w-100 h-100"
                                         style="object-fit: cover; object-position: center; border: 2px solid #f1f1f1;">
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-0 fw-semibold text-truncate">${user.first_name + " " + user.last_name}</h6>
                                    <small class="text-muted text-truncate d-block">${user.email}</small>
                                </div>
                            </li>
                        `);
                    })
                } else {
                    $(".new-chat-list").append(`${response.message}`)
                }
            }

        })
    });
    $(document).on("click", ".user-with-chat-profile", function () {
        let userId = $(this).data('user-id');
        let userFirstName = $(this).data('user-firstname');
        let userLastName = $(this).data('user-lastname');
        let userImage = $(this).data('user-image');
        let userEmail = $(this).data('user-email');
        let userRole = $(this).parents("#chatPage").data('user');
        let chatWindow = $("#chatWindow");

        $.ajax({
           url: '/load-messages',
           type: 'POST',
           data: {id: userId, action: 'forSupport'},
           success: function (response) {

               chatWindow.data('user-id', userId);
               addChatTitle(userId, userImage, userFirstName, userLastName, userEmail);
               $('#chatMessages').empty();
               response.messages.forEach(function (message) {
                   const side = message['user_type'] === userRole ? 'right' : 'left';

                   if(message['message']) {
                       addMessage(message['message'], message.id , side);
                   } else if (message['attachment_name'] && message['attachment_url']) {
                       addAttachment(message['attachment_name'], message['attachment_url'], message.id , side);
                   }
               });

               addSendMessageForm();

           }
        });
    });

    $(document).on("click", ".user-chat-list", function() {
        let userId = $(this).data('user-id');

        $.ajax({
            url: '/start-new-chat',
            type: 'POST',
            data: {id: userId},
            success: function (response) {
                if(response.success) {
                    let user = response.user;

                    let imageUrl = (user.media && user.media.length > 0)
                        ? user.media[0].original_url
                        : "/images/no-image.png";


                    $("#newChatCard").addClass('d-none');
                    addChatTitle(user.id, imageUrl, user.first_name, user.last_name, user.email);

                    addSendMessageForm();
                    $("#chatMessages").html(`
                    <div class="d-flex align-items-center justify-content-center h-100 chat-selected-info">
                        <p class="chats-info">No messages yet...</p>
                    </div>
                    `);

                    $(".chats-info").text('No messages yet....');
                }
            }

        });
    });
    $(document).on("click", "#attachBtn", function () {
        $("#chatAttachment").click();
    });

    $(document).on("change", "#chatAttachment", function () {
        if (this.files.length > 0) {
            console.log("Files selected:", Array.from(this.files).map(f => f.name));
        }
    });

    $("#userChatForm").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('sender', 'user');

        $.ajax({
           url: '/send-message',
           type: 'POST',
           data: formData,
           processData: false,
           contentType: false,
           success: function(response) {
               $("#userChatForm")[0].reset();
               if(response.success) {
                   if(response.success) {
                       response.chatData.forEach(function (chat) {
                           if(chat.message) {
                               addCardMessage(chat.message, chat.id, 'right');
                           }
                           if(chat.attachment_name && chat.attachment_url) {
                               addCardAttachment(chat.attachment_name, chat.attachment_url, chat.id, 'right');
                           }
                       });
                   }
               }

           }
        });
    });

    $("#chatForm").on("submit", function (e) {
        e.preventDefault();
        let userId = $(this).parents("#chatWindow").data('user-id');
        let formData = new FormData(this);
        let role = $(this).data('user');

        formData.append("userId", userId);
        formData.append("sender", role);

        $.ajax({
            url: '/send-message',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#chatForm")[0].reset();
                $(".chat-selected-info").remove();
                if(response.success) {
                    loadActiveChats();
                    loadArchiveChats();
                    response.chatData.forEach(function (chat) {
                       if(chat.message) {
                           addMessage(chat.message, chat.id);
                       }
                       if(chat.attachment_name && chat.attachment_url) {
                           addAttachment(chat.attachment_name, chat.attachment_url, chat.id);
                       }
                    });
                } else {
                    console.log(response.message);
                }
            }
        });

    });

    function addSendMessageForm() {

        $("#chatForm").html(`
            <input type="text" name="message" class="form-control border-0 flex-grow-1" placeholder="Type a message..." autocomplete="off">
            <input type="file" id="chatAttachment" name="attachments[]" class="d-none" multiple>

            <button type="button" id="attachBtn" class="btn btn-light rounded-circle px-3 py-2 d-flex align-items-center justify-content-center">
                &#128206;
            </button>

            <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center px-3 py-2">
                <span style='font-size:20px;'>&#10148;</span>
            </button>
        `);
    }
    function addChatTitle(userId, userImage, userFirstName, userLastName, userEmail) {
        let chatTitle = $("#chatTitle");

        chatTitle.parents("#chatWindow").data('user-id', userId);

        chatTitle.html(`
        <div class="d-flex align-items-center gap-3 border-0 bg-transparent user-info hover-bg"
            style="min-height: 50px;"
            data-user-id="${userId}">
                <div class="flex-shrink-0" style="width: 50px; height: 50px;">
                    <img src="${userImage}"
                         alt="User Avatar"
                         class="rounded-circle w-100 h-100"
                         style="object-fit: cover; object-position: center; border: 2px solid #f1f1f1;">
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="mb-0 fw-semibold text-truncate">${userFirstName + " " + userLastName}</h6>
                    <small class="text-muted text-truncate d-block">${userEmail}</small>
                </div>
            </div>
        `);
    }

    function addCardMessage(message, id, side = 'left') {
        let chat = $(".chat-messages");
        let justifyClass = side === 'right' ? 'justify-content-end' : 'justify-content-start';
        let bubbleClass = side === 'right' ? 'bg-primary text-light' : 'bg-light text-dark';
        let html = '';

        if(side === 'right') {
            html = `
            <div class="chat-message d-flex mb-2 ${justifyClass}" data-message="${message}" data-message-id="${id}">
                <div class="message-bubble ${bubbleClass} p-2 px-3 rounded-3 shadow-sm" style="max-width: 80%;">
                    ${message}
                </div>
                <div class="message-menu position-absolute me-2 d-none">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            &vellip;
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item edit-message" data-chat-message-id="${id}">Edit</a></li>
                            <li><a class="dropdown-item delete-message" data-chat-message-id="${id}">Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            `;
        } else {
            html = `
            <div class="chat-message d-flex mb-2 ${justifyClass}">
                <div class="message-bubble ${bubbleClass} p-2 px-3 rounded-3 shadow-sm" style="max-width: 80%;">
                    ${message}
                </div>
            </div>
            `;
        }

        chat.append(html);

        chat.scrollTop(chat[0].scrollHeight);
    }

    function addCardAttachment(name, url, id, side = 'left') {
        let chat = $('.chat-messages');
        let justifyClass = side === 'right' ? 'justify-content-end' : 'justify-content-start';
        let bubbleClass = side === 'right' ? 'bg-primary text-white' : 'bg-light text-dark';

        let fileExtension = name.split('.').pop().toLowerCase();

        let attachmentHtml = '';

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
            attachmentHtml = `
                            <img src="${url}" alt="${name}" class="img-fluid rounded shadow-sm" style="max-width: 200px;">
                               <figcaption class="text-muted">${name}</figcaption>
                            `;
        } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
            attachmentHtml = `<video src="${url}" controls class="rounded shadow-sm" style="max-width: 250px;"></video>`;
        } else if (['mp3', 'wav'].includes(fileExtension)) {
            attachmentHtml = `<audio controls class="w-100"><source src="${url}" type="audio/${fileExtension}">Your browser does not support audio.</audio>`;
        } else {
            attachmentHtml = `
            <a href="${url}" target="_blank" class="d-inline-flex align-items-center text-decoration-none text-black">
                <i class="bi bi-paperclip me-2"></i> ${name}
            </a>
        `;

        }
        let messageHtml = '';

        if(side === "right") {
            messageHtml = `
            <div class="message-row d-flex ${justifyClass} mb-3 chat-message">
                <div class="message-bubble ${bubbleClass} p-2">${attachmentHtml}</div>
                <div class="message-menu position-absolute me-2 d-none">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
                             aria-expanded="false">
                            &vellip;
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item delete-message" data-chat-message-id="${id}">Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
        } else {
            messageHtml = `
            <div class="message-row d-flex ${justifyClass} mb-3 chat-message">
                <div class="message-bubble ${bubbleClass} p-2">${attachmentHtml}</div>
            </div>
        `;
        }


        chat.append(messageHtml);
        chat.scrollTop(chat[0].scrollHeight);
    }


    function addMessage(message, id, side = 'right') {
        let chat = $('#chatMessages');
        let justifyClass = side === 'right' ? 'justify-content-end' : 'justify-content-start';
        let bubbleClass = side === 'right' ? 'bg-primary text-white' : 'bg-light text-dark';
        let messageHtml = '';
        if(side === 'right') {
            messageHtml = `
            <div class="chat-message d-flex mb-2 ${justifyClass}" data-message="${message}" data-message-id="${id}">
                <div class="messageBubble ${bubbleClass} p-2 px-3 rounded-3 shadow-sm">
                    ${message}
                </div>
                <div class="message-menu position-absolute me-2 d-none">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
                             aria-expanded="false">
                            &vellip;
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item edit-message" data-chat-message-id="${id}">Edit</a></li>
                            <li><a class="dropdown-item delete-message" data-chat-message-id="${id}">Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
        } else {
            messageHtml = `
            <div class="chat-message d-flex mb-2 ${justifyClass}">
                <div class="messageBubble ${bubbleClass} p-2 px-3 rounded-3 shadow-sm">
                    ${message}
                </div>
            </div>
        `;
        }



        chat.append(messageHtml);
        chat.scrollTop(chat[0].scrollHeight);
    }

    function addAttachment(name, url, id, side = 'right') {
        let chat = $('#chatMessages');
        let justifyClass = side === 'right' ? 'justify-content-end' : 'justify-content-start';
        let bubbleClass = side === 'right' ? 'bg-primary text-white' : 'bg-light text-dark';

        let fileExtension = name.split('.').pop().toLowerCase();

        let attachmentHtml = '';

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
            attachmentHtml = `<img src="${url}" alt="${name}" class="img-fluid rounded shadow-sm" style="max-width: 200px;"><figcaption>${name}</figcaption>`;
        } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
            attachmentHtml = `<video src="${url}" controls class="rounded shadow-sm" style="max-width: 250px;"></video>`;
        } else if (['mp3', 'wav'].includes(fileExtension)) {
            attachmentHtml = `<audio controls class="w-100"><source src="${url}" type="audio/${fileExtension}">Your browser does not support audio.</audio>`;
        } else {
            attachmentHtml = `
            <a href="${url}" target="_blank" class="d-inline-flex align-items-center text-decoration-none text-white">
                <i class="bi bi-paperclip me-2"></i> ${name}
            </a>
        `;

        }
        let messageHtml = '';
        if(side === 'right') {
            messageHtml = `
            <div class="message-row d-flex ${justifyClass} mb-3 chat-message">
                <div class="message-bubble ${bubbleClass} p-2">${attachmentHtml}</div>
                <div class="message-menu position-absolute me-2 d-none">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
                             aria-expanded="false">
                            &vellip;
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item delete-message" data-chat-message-id="${id}">Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
        } else {
            messageHtml = `
            <div class="message-row d-flex ${justifyClass} mb-3 chat-message">
                <div class="message-bubble ${bubbleClass} p-2">${attachmentHtml}</div>
            </div>
        `;
        }


        chat.append(messageHtml);
        chat.scrollTop(chat[0].scrollHeight);
    }
});
