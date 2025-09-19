import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY || 'b90ca2c0b12a1168523a';
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'eu';

console.log('🔧 Configurando Echo com:');
console.log('Key:', pusherKey);
console.log('Cluster:', pusherCluster);

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    withCredentials: true,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
    },
});

// Log de conexão
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('✅ Pusher conectado com sucesso!');
    console.log('🔍 Estado da conexão:', window.Echo.connector.pusher.connection.state);
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('❌ Pusher desconectado');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('❌ Erro na conexão Pusher:', error);
});

// Log quando uma mensagem é recebida
window.Echo.connector.pusher.connection.bind('message', (data) => {
    console.log('📨 Mensagem recebida do Pusher:', data);
});

// Log de todos os eventos do Pusher para debug
window.Echo.connector.pusher.bind_global((eventName, data) => {
    console.log('🌍 Evento global recebido:', eventName, data);
});

console.log('🎉 Echo configurado:', window.Echo);