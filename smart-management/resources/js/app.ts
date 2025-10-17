import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import axios from 'axios';
import { Toaster, toast } from 'vue-sonner';

// Configurar axios para incluir CSRF token automaticamente
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Obter CSRF token do meta tag ou do cookie
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
} else {
    console.error('CSRF token not found');
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
      const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('Toaster', Toaster);
        
        app.mount(el);
           // Debug: verificar se as flash messages estão chegando
        console.log('🔍 Props iniciais:', props.initialPage.props);
        console.log('💬 Flash messages iniciais:', props.initialPage.props.flash); 
     
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
