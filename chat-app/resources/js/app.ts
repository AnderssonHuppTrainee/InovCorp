import '../css/app.css';
import './lib/echo';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import axios from 'axios';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// Configurar axios para funcionar com Herd
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.withCredentials = true;
// Usar a URL atual do navegador 
axios.defaults.baseURL = window.location.origin;


function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}


const token = getCSRFToken();
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    console.log('Token CSRF configurado:', token);
} else {
    console.error('Token CSRF não encontrado no meta tag');
}


axios.interceptors.request.use(
    (config) => {
        const csrfToken = getCSRFToken();
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para lidar com erros 419 (CSRF token mismatch)
axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 419) {
            console.log('Erro 419 - Token CSRF expirado, tentando obter novo token...');
            try {
                // Fazer uma requisição para obter novo token via API
                const response = await axios.get('/api/csrf-token');
                const newToken = response.data.csrf_token;
                if (newToken) {
                    console.log('Novo token CSRF obtido:', newToken);
                    // Atualizar o meta tag
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', newToken);
                    }
                    // Atualizar o header da requisição original
                    error.config.headers['X-CSRF-TOKEN'] = newToken;
                    // Tentar novamente a requisição original
                    return axios.request(error.config);
                }
            } catch (retryError) {
                console.error('Erro ao obter novo token CSRF:', retryError);
            }
        }
        return Promise.reject(error);
    }
);

export { axios };
export default axios;