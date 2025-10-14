import { router, usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { useToast } from './useToast'


export function useFlashMessages() {
    const page = usePage()
    const { showSuccess, showError, showInfo, showWarning } = useToast()
    watch(
        () => page.props.flash,
        (flash: any) => {
           
            if (!flash) {
                
                return
            }

            // Success
            if (flash.success) {
               
                showSuccess(flash.success)
            }

            // Error
            if (flash.error) {
                
                showError(flash.error)
            }

            // Info
            if (flash.info) {
                
                showInfo(flash.info)
            }

            // Warning
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

