import { toast } from 'vue-sonner';
import { usePage } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';

interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

export function useFlashMessages() {
    const page = usePage();
    
    onMounted(() => {
        console.log('🔍 useFlashMessages montado');
        console.log('📄 Props atuais:', page.props);
        console.log('💬 Flash messages:', page.props.flash);
        
        // Verificar flash messages imediatamente
        const flash = page.props.flash as FlashMessages;
       if (flash?.success) {
        toast.success(flash.success, {
            style: { background: '#22c55e', color: '#fff' },
            class: 'flex p-4 rounded-lg shadow-md',
        });
        } else if (flash?.error) {
        toast.error(flash.error, {
            style: { background: '#ef4444', color: '#fff' },
           class: 'flex p-4 rounded-lg shadow-md',
        });
        } else if (flash?.warning) {
        toast.warning(flash.warning, {
            style: { background: '#facc15', color: '#000' },
            class: 'rounded-lg shadow-md',
        });
        } else if (flash?.info) {
        toast.info(flash.info, {
            style: { background: '#3b82f6', color: '#fff' },
            class: 'rounded-lg shadow-md',
        });
        }

    });
    
    watch(
        () => page.props.flash,
        (flash) => {
            console.log('Flash messages mudaram:', flash);
            const flashMessages = flash as FlashMessages;
            if (flashMessages?.success) {
                console.log('Mostrando toast de sucesso:', flashMessages.success);
                toast.success(flashMessages.success);
            } else if (flashMessages?.error) {
                console.log('Mostrando toast de erro:', flashMessages.error);
                toast.error(flashMessages.error);
            } else if (flashMessages?.warning) {
                console.log('Mostrando toast de aviso:', flashMessages.warning);
                toast.warning(flashMessages.warning);
            } else if (flashMessages?.info) {
                console.log('ℹMostrando toast de info:', flashMessages.info);
                toast.info(flashMessages.info);
            }
        }
    );
}