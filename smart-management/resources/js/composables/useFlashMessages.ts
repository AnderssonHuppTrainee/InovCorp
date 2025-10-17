// composables/useFlashMessages.ts
import { usePage } from '@inertiajs/vue3'
import { onMounted, watch } from 'vue'
import { useToast } from './useToast'

interface FlashMessages {
  success?: string
  error?: string
  warning?: string
  info?: string
}

export function useFlashMessages() {
  const page = usePage()
  const { showSuccess, showError, showWarning, showInfo } = useToast()

  const handleFlash = (flash: FlashMessages | undefined) => {
    if (!flash) return

    if (flash.success) showSuccess(flash.success)
    if (flash.error) showError(flash.error)
    if (flash.warning) showWarning(flash.warning)
    if (flash.info) showInfo(flash.info)
  }

  // Executa quando a página é montada
  onMounted(() => handleFlash(page.props.flash as FlashMessages))

  // Observa alterações nas mensagens flash do Inertia
  watch(
    () => page.props.flash as FlashMessages,
    (flash) => handleFlash(flash),
    { deep: true }
  )
}
