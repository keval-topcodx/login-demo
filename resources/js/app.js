import './bootstrap';
import 'bootstrap';
import Swal from 'sweetalert2';
import $ from 'jquery';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

//echo
window.$ = window.jQuery = $;
window.Swal = Swal;



