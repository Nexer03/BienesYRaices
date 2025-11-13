import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1';
const pusherHost = import.meta.env.VITE_PUSHER_HOST ?? `ws-${pusherCluster}.pusher.com`;

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        wsHost: pusherHost,
        wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
        wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        cluster: pusherCluster,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        },
    });
}
