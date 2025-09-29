import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import { Toaster, toast } from 'vue-sonner';

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
        
        /* Verificar flash messages na carga inicial
        const flash = props.initialPage.props.flash;
        if (flash?.success) {
            console.log('✅ Flash success encontrado:', flash.success);
            toast.success(flash.success);
        } else if (flash?.error) {
            console.log('❌ Flash error encontrado:', flash.error);
            toast.error(flash.error);
        }*/
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();