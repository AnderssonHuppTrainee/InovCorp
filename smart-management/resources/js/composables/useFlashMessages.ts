import { router, usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { useToast } from './useToast'


export function useFlashMessages() {
    const page = usePage()
    const { showSuccess, showError, showInfo, showWarning } = useToast()
    
    watch(
        () => page.props.flash,
        (flash: any) => {
            if (!flash) return

            if (flash.success) {
                showSuccess(flash.success)
            }

            if (flash.error) {
                showError(flash.error)
            }

            if (flash.info) {
                showInfo(flash.info)
            }

            if (flash.warning) {
                showWarning(flash.warning)
            }
        },
        { deep: true, immediate: true }
    )

    router.on('success', (event) => {
       
    })

    router.on('error', (event) => {
       
    })
}

