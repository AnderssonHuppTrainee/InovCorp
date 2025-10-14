import { router, usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { useToast } from './useToast'

/**
 * Composable para integrar Flash Messages do Laravel com Toast (Sonner)
 * 
 * Monitora automaticamente as flash messages do Laravel e as exibe como toast.
 * 
 * @example
 * // No setup de um layout ou componente principal
 * useFlashMessages()
 * 
 * // No backend Laravel (qualquer controller)
 * return redirect()->with('success', 'Cliente criado com sucesso!')
 * return back()->with('error', 'Erro ao eliminar fornecedor')
 * return redirect()->with('info', 'Processamento em andamento...')
 * return back()->with('warning', 'Atenção: Esta ação não pode ser revertida')
 */
export function useFlashMessages() {
    const page = usePage()
    const { showSuccess, showError, showInfo, showWarning } = useToast()

    /**
     * Watch para flash messages
     * Exibe automaticamente as mensagens quando mudam
     */
    watch(
        () => page.props.flash,
        (flash: any) => {
            if (!flash) return

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

    /**
     * Também pode escutar eventos do Inertia para mais controle
     */
    router.on('success', (event) => {
        // Pode adicionar lógica adicional aqui se necessário
    })

    router.on('error', (event) => {
        // Pode adicionar lógica adicional aqui se necessário
    })
}

