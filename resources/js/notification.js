import './app';
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    Echo.private(`support`)
    .listen('.message.sent', (e) => {
        console.log("incoming message");
        displayNotifications();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#notificationPanel, #notifications').length) {
            $('#notificationPanel').addClass('d-none');
        }
    });

    $(document).on("click", "#notifications", function(e) {
        e.stopPropagation();
        $('#notificationPanel').toggleClass('d-none');
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
                   notificationItem.removeClass("unread");
                   let countElement = $("#unreadCount");
                   let unreadCount = countElement.text() || 0;
                   if (unreadCount > 0) {
                       countElement.text(unreadCount - 1);
                   }
                   window.location.href = `/chat?chatId=${response.chat.id}`;
               }
           }
       });
    });
    displayNotifications();
    function displayNotifications() {
        $.ajax({
            url: '/get-all-notifications',
            type: 'POST',
            data: { data: '' },
            success: function (response) {
                if (response.success) {
                    let html = '';


                    response.notifications.forEach(function (notification) {
                        let msg = notification.message;
                        let attachmentHtml = '';
                        let textHtml = '';


                        if (msg.startsWith('Attachment:')) {
                            const match = msg.match(/Attachment:\s*(.+?)\s*Url:\s*(.+)/);

                            if (match) {
                                const name = match[1].trim();
                                const url = match[2].trim();

                                const ext = name.split('.').pop().toLowerCase();
                                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);

                                if (isImage) {
                                    attachmentHtml = `
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="${url}" alt="${name}" class="rounded-circle border notification-image" width="35" height="35" style="object-fit:cover;">
                                        <span class="small text-muted">${name}</span>
                                    </div>
                                `;
                                } else {
                                    attachmentHtml = `
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-paperclip text-secondary"></i>
                                        <a href="${url}" target="_blank" class="small text-muted text-decoration-none">${name}</a>
                                    </div>
                                `;
                                }
                            }
                        } else {
                            textHtml = `<span class="text-muted small">${msg}</span>`;
                        }


                        html += `
                            <div class="notification-item ${notification.read ? '' : 'unread'}" data-notification-id="${notification.id}" data-user-id="${notification.user_id}">
                                <strong>${notification.user.first_name} ${notification.user.last_name}</strong><br>
                                ${attachmentHtml || textHtml}
                            </div>
                    `;
                    });


                    $('.notification-list').html(html);

                    $('#unreadCount').text(response.unread_count > 0 ? response.unread_count : '').toggle(response.unread_count > 0);
                } else {

                    $('.notification-list').html(`
                    <div class="text-center text-muted p-3">No notifications.</div>
                `);
                    $('#unreadCount').text('').hide();
                }
            },
            error: function () {
                $('.notification-list').html(`
                <div class="text-danger text-center p-3">Error loading notifications.</div>`);
            }
        });
    }



});
