// resources/js/bootstrap.js

import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

// 🔍 Leemos las variables de entorno de Vite
const pusherKey     = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'us2';
const pusherHost    = import.meta.env.VITE_PUSHER_HOST || null;
const pusherPort    = Number(import.meta.env.VITE_PUSHER_PORT || 443);
const pusherScheme  = import.meta.env.VITE_PUSHER_SCHEME || 'https';

console.log('[BOOTSTRAP] VITE_PUSHER_APP_KEY:', pusherKey);
console.log('[BOOTSTRAP] VITE_PUSHER_APP_CLUSTER:', pusherCluster);
console.log('[BOOTSTRAP] VITE_PUSHER_HOST:', pusherHost, 'PORT:', pusherPort, 'SCHEME:', pusherScheme);

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        // Para Pusher Cloud, con websockets
        wsHost: pusherHost || `ws-${pusherCluster}.pusher.com`,
        wsPort: pusherPort || 80,
        wssPort: pusherPort || 443,
        forceTLS: pusherScheme === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
            },
        },
    });

    console.log('[BOOTSTRAP] Echo creado correctamente:', window.Echo);
} else {
    console.warn('[BOOTSTRAP] NO hay VITE_PUSHER_APP_KEY, Echo NO se inicializó');
}

