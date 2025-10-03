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
                class: 'flex p-4 rounded-lg shadow-md',
            });
        } else if (flash?.info) {
            toast.info(flash.info, {
                style: { background: '#3b82f6', color: '#fff' },
                class: 'flex p-4 rounded-lg shadow-md',
            });
        }

    });
    
    watch(
        () => page.props.flash as FlashMessages,
        (flash) => {
            if (!flash) return;
            if (flash.success) {
                toast.success(flash.success, {
                    style: { background: '#22c55e', color: '#fff' },
                    class: 'flex p-4 rounded-lg shadow-md',
                });
            } else if (flash.error) {
                toast.error(flash.error, {
                    style: { background: '#ef4444', color: '#fff' },
                    class: 'flex p-4 rounded-lg shadow-md',
                });
            } else if (flash.warning) {
                toast.warning(flash.warning, {
                    style: { background: '#facc15', color: '#000' },
                    class: 'flex p-4 rounded-lg shadow-md',
                });
            } else if (flash.info) {
                toast.info(flash.info, {
                    style: { background: '#3b82f6', color: '#fff' },
                    class: 'flex p-4 rounded-lg shadow-md',
                });
            }
        },
        { deep: true }
    );

}