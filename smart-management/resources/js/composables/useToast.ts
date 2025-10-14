import { toast } from 'vue-sonner'

/**
 * Composable para facilitar o uso de notificações toast
 * Baseado no Sonner (vue-sonner)
 * 
 * @example
 * const { showSuccess, showError, showInfo } = useToast()
 * 
 * showSuccess('Cliente criado com sucesso!')
 * showError('Erro ao eliminar fornecedor')
 * showInfo('A processar...')
 */
export function useToast() {
    /**
     * Mostra notificação de sucesso
     */
    const showSuccess = (message: string, description?: string) => {
        toast.success(message, {
            description,
            duration: 4000,
        })
    }

    /**
     * Mostra notificação de erro
     */
    const showError = (message: string, description?: string) => {
        toast.error(message, {
            description,
            duration: 5000,
        })
    }

    /**
     * Mostra notificação informativa
     */
    const showInfo = (message: string, description?: string) => {
        toast.info(message, {
            description,
            duration: 4000,
        })
    }

    /**
     * Mostra notificação de aviso
     */
    const showWarning = (message: string, description?: string) => {
        toast.warning(message, {
            description,
            duration: 4000,
        })
    }

    /**
     * Mostra notificação de loading
     * Retorna ID para poder dismissar depois
     */
    const showLoading = (message: string, description?: string) => {
        return toast.loading(message, {
            description,
        })
    }

    /**
     * Mostra notificação com promessa
     * Útil para ações assíncronas
     */
    const showPromise = <T,>(
        promise: Promise<T>,
        messages: {
            loading: string
            success: string | ((data: T) => string)
            error: string | ((error: Error) => string)
        }
    ) => {
        return toast.promise(promise, messages)
    }

    /**
     * Dismissar uma notificação específica
     */
    const dismiss = (id?: string | number) => {
        toast.dismiss(id)
    }

    /**
     * Dismissar todas as notificações
     */
    const dismissAll = () => {
        toast.dismiss()
    }

    return {
        showSuccess,
        showError,
        showInfo,
        showWarning,
        showLoading,
        showPromise,
        dismiss,
        dismissAll,
        // Exportar toast original para casos avançados
        toast,
    }
}

