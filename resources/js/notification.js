import './app';
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    let authUserId = $("#userDropdown").data('user-id');
    if(authUserId) {
        Echo.private('notification.' + authUserId)
            .listen('.notification-received', (e) => {
                let html = '';
                const badge = $("#unreadCount");
                let currentCount = parseInt(badge.text()) || 0;
                let newNotifications = Array.isArray(e.notification) ? e.notification.length : 1;
                let updatedCount = currentCount + newNotifications;
                badge.text(updatedCount);
                if (updatedCount > 0) {
                    badge.removeClass("d-none").addClass("d-block");
                } else {
                    badge.removeClass("d-block").addClass("d-none");
                }

                e.notification.forEach(msg => {
                    let messageText = msg.message ?? '';
                    let attachmentHtml = '';
                    let textHtml = '';


                    const match = messageText.match(/Attachment:\s*(.+?)\s*Url:\s*(.+)/i);

                    if (match) {
                        const name = match[1].trim();
                        const url = match[2].trim();

                        const ext = name.split('.').pop().toLowerCase();
                        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);

                        if (isImage) {
                            attachmentHtml = `
                        <div class="d-flex align-items-center gap-2">
                            <img src="${url}" alt="${name}"
                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <span class="text-truncate small" style="max-width: 200px;">${name}</span>
                        </div>
                    `;
                        } else {
                            attachmentHtml = `
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-paperclip fs-5 text-secondary"></i>
                            <span class="text-truncate small" style="max-width: 200px;">${name}</span>
                        </div>
                    `;
                        }
                    } else {

                        textHtml = `<span class="text-muted small d-block">${messageText}</span>`;
                    }


                    const user = msg.user ?? e.sender ?? {};
                    const fullName = `${e.user_type.charAt(0).toUpperCase() + e.user_type.slice(1)}`;

                    html += `
                <a class="text-decoration-none text-black notification-anchor">
                    <div class="notification-item user-notification p-2 mb-2 rounded bg-warning-subtle unread"
                         data-notification-id="${msg.id}"
                         data-user-id="${msg.user_id}"
                         style="cursor:pointer;">
                        <strong>${fullName || 'Unknown User'}</strong><br>
                        ${attachmentHtml || textHtml}
                    </div>
                </a>
            `;
                });
                $("#notificationContainer").prepend(html);
            });
    }
    Echo.private(`notification`)
        .listen('.notification-received', (e) => {
            let html = '';
            const badge = $("#unreadCount");
            let currentCount = parseInt(badge.text()) || 0;
            let newNotifications = Array.isArray(e.notification) ? e.notification.length : 1;
            let updatedCount = currentCount + newNotifications;
            badge.text(updatedCount);
            if (updatedCount > 0) {
                badge.removeClass("d-none").addClass("d-block");
            } else {
                badge.removeClass("d-block").addClass("d-none");
            }

            e.notification.forEach(msg => {
                let messageText = msg.message ?? '';
                let attachmentHtml = '';
                let textHtml = '';


                const match = messageText.match(/Attachment:\s*(.+?)\s*Url:\s*(.+)/i);

                if (match) {
                    const name = match[1].trim();
                    const url = match[2].trim();

                    const ext = name.split('.').pop().toLowerCase();
                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);

                    if (isImage) {
                        attachmentHtml = `
                        <div class="d-flex align-items-center gap-2">
                            <img src="${url}" alt="${name}"
                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <span class="text-truncate small" style="max-width: 200px;">${name}</span>
                        </div>
                    `;
                    } else {
                        attachmentHtml = `
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-paperclip fs-5 text-secondary"></i>
                            <span class="text-truncate small" style="max-width: 200px;">${name}</span>
                        </div>
                    `;
                    }
                } else {

                    textHtml = `<span class="text-muted small d-block">${messageText}</span>`;
                }


                const user = msg.user ?? e.sender ?? {};
                const fullName = `${user.first_name ?? ''} ${user.last_name ?? ''}`.trim();

                html += `
                <a class="text-decoration-none text-black notification-anchor">
                    <div class="notification-item p-2 mb-2 rounded bg-warning-subtle unread"
                         data-notification-id="${msg.id}"
                         data-user-id="${msg.user_id}"
                         style="cursor:pointer;">
                        <strong>${fullName || 'Unknown User'}</strong><br>
                        ${attachmentHtml || textHtml}
                    </div>
                </a>
            `;
            });
            $("#notificationContainer").prepend(html);
        });

    $(document).on('click', '.notification-link', function (e) {
        e.preventDefault();
        console.log("clicked");
        // const chatId = $(this).data('chat-id');
        // if (chatId) {
        //     window.location.href = `/chat?chatId=${chatId}`;
        // }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#notificationContainer, #notifications').length) {
            $("#notificationContainer").hide();
        }
    });

    $(document).on("click", "#notifications", function(e) {
        e.stopPropagation();
        $("#notificationContainer").show();
    });

    $(document).on("click", ".notification-item", function() {
        let notificationItem = $(this);
        let notificationId = $(this).data('notification-id');
       $.ajax({
           url: '/mark-notification-as-read',
           type: 'POST',
           data: {id : notificationId},
           success: function (response) {
               if(response.success) {
                   notificationItem.removeClass("unread bg-warning-subtle");
                   notificationItem.addClass("bg-white");
                   let countElement = $("#unreadCount");
                   let unreadCount = countElement.text() || 0;
                   if (unreadCount > 0) {
                       countElement.text(unreadCount - 1);
                   }
                   if(!notificationItem.hasClass("user-notification")) {
                       window.location.href = `/chat?userId=${response.chat.user_id}`;
                   }

               }
           }
       });
    });

});
