import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

console.log('Configurando Echo com:');
console.log('Key:', import.meta.env.VITE_PUSHER_APP_KEY);
console.log('Cluster:', import.meta.env.VITE_PUSHER_APP_CLUSTER);

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'b90ca2c0b12a1168523a',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'eu',
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    withCredentials: true
});

console.log('Echo configurado:', window.Echo);
