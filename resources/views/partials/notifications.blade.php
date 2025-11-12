@hasanyrole(['admin', 'agent'])
<button type="button" class="btn btn-secondary me-3 text-black position-relative" id="notifications">
    &#128229;
    <span id="unreadCount"
          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger
                         {{ isset($unreadCount) && $unreadCount > 0 ? 'd-block' : 'd-none' }}">
                            @if(isset($unreadCount) && $unreadCount > 0)
            {{ $unreadCount }}
        @endif
                    </span>

</button>

<div id="notificationContainer"
     class="border rounded p-3 bg-light shadow-sm position-absolute end-0 mt-2"
     style="width: 320px; max-height: 300px; overflow-y: auto; display: none; z-index: 1000">



    @if(isset($notifications) && $notifications->count() > 0)
        @foreach($notifications as $notification)
            @php
                $message = $notification->message;

                $hasAttachment = preg_match('/attachment\s*:/i', $message);
                $attachmentName = null;
                $attachmentUrl = null;

                if ($hasAttachment) {
                    preg_match('/attachment\s*:\s*(?:["\']?)([^\s,"\'<>]+)(?:["\']?)/i', $message, $nameMatch);
                    $attachmentName = $nameMatch[1] ?? null;

                    preg_match('/url\s*:\s*(?:["\']?)(\S+?)(?:["\']?)(?=\s|$)/i', $message, $urlMatch);
                    $attachmentUrl = $urlMatch[1] ?? null;

                    if ($attachmentUrl) {
                        $attachmentUrl = rtrim($attachmentUrl, '.,;');
                    }

                    if (!$attachmentUrl) {
                        preg_match('/https?:\/\/[^\s"\'<>]+/i', $message, $anyUrlMatch);
                        $attachmentUrl = $anyUrlMatch[0] ?? $attachmentUrl;
                    }

                    if ($attachmentUrl && !Str::startsWith($attachmentUrl, ['http://', 'https://'])) {

                    }
                }
            @endphp
            <a class="text-decoration-none text-black notification-anchor" >
                <div class="notification-item p-2 mb-2 rounded {{ $notification->read ? 'bg-white' : 'bg-warning-subtle unread' }}"
                     data-notification-id="{{ $notification->id }}"
                     data-user-id="{{ $notification->user_id }}"
                     style="cursor:pointer;">

                    <strong>{{ $notification->user->first_name }} {{ $notification->user->last_name }}</strong><br>

                    @if($hasAttachment && $attachmentUrl)
                        <div class="mt-2 d-flex align-items-center gap-2 border rounded p-2 bg-white">
                            @php
                                $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $attachmentName);
                            @endphp

                            @if($isImage)
                                <div>
                                    <img src="{{ $attachmentUrl }}" alt="{{ $attachmentName }}"
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                </div>
                            @else
                                <div>
                                    <i class="bi bi-paperclip fs-4 text-secondary"></i>
                                </div>
                            @endif

                            <span class="text-truncate small" style="max-width: 200px;">
                                            {{ $attachmentName }}
                                        </span>
                        </div>
                    @else
                        <span class="text-muted small d-block">{{ $message }}</span>
                    @endif

                </div>
            </a>
        @endforeach
    @else
        <div class="text-center text-muted p-3">No notifications.</div>
    @endif
</div>


@endhasanyrole

@unlessrole(['admin', 'agent'])
<button type="button" class="btn btn-secondary me-3 text-black position-relative" id="notifications">
    &#128229;
    <span id="unreadCount"
          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger
                         {{ isset($unreadCount) && $unreadCount > 0 ? 'd-block' : 'd-none' }}">
                            @if(isset($unreadCount) && $unreadCount > 0)
            {{ $unreadCount }}
        @endif
                    </span>

</button>

<div id="notificationContainer"
     class="border rounded p-3 bg-light shadow-sm position-absolute end-0 mt-2"
     style="width: 320px; max-height: 300px; overflow-y: auto; display: none; z-index: 1000">



    @if(isset($notifications) && $notifications->count() > 0)
        @foreach($notifications as $notification)
            @php
                $message = $notification->message;

                $hasAttachment = preg_match('/attachment\s*:/i', $message);
                $attachmentName = null;
                $attachmentUrl = null;

                if ($hasAttachment) {
                    preg_match('/attachment\s*:\s*(?:["\']?)([^\s,"\'<>]+)(?:["\']?)/i', $message, $nameMatch);
                    $attachmentName = $nameMatch[1] ?? null;

                    preg_match('/url\s*:\s*(?:["\']?)(\S+?)(?:["\']?)(?=\s|$)/i', $message, $urlMatch);
                    $attachmentUrl = $urlMatch[1] ?? null;

                    if ($attachmentUrl) {
                        $attachmentUrl = rtrim($attachmentUrl, '.,;');
                    }

                    if (!$attachmentUrl) {
                        preg_match('/https?:\/\/[^\s"\'<>]+/i', $message, $anyUrlMatch);
                        $attachmentUrl = $anyUrlMatch[0] ?? $attachmentUrl;
                    }

                    if ($attachmentUrl && !Str::startsWith($attachmentUrl, ['http://', 'https://'])) {

                    }
                }
            @endphp
            <a class="text-decoration-none text-black notification-anchor" >
                <div class="notification-item p-2 user-notification mb-2 rounded {{ $notification->read ? 'bg-white' : 'bg-warning-subtle unread' }}"
                     data-notification-id="{{ $notification->id }}"
                     data-user-id="{{ $notification->user_id }}"
                     style="cursor:pointer;">

                    <strong>{{ ucfirst($notification->user_type) }}</strong><br>

                    @if($hasAttachment && $attachmentUrl)
                        <div class="mt-2 d-flex align-items-center gap-2 border rounded p-2 bg-white">
                            @php
                                $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $attachmentName);
                            @endphp

                            @if($isImage)
                                <div>
                                    <img src="{{ $attachmentUrl }}" alt="{{ $attachmentName }}"
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                </div>
                            @else
                                <div>
                                    <i class="bi bi-paperclip fs-4 text-secondary"></i>
                                </div>
                            @endif

                            <span class="text-truncate small" style="max-width: 200px;">
                                            {{ $attachmentName }}
                                        </span>
                        </div>
                    @else
                        <span class="text-muted small d-block">{{ $message }}</span>
                    @endif

                </div>
            </a>
        @endforeach
    @else
        <div class="text-center text-muted p-3">No notifications.</div>
    @endif
</div>


@endhasanyrole
