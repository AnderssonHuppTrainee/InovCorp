import { toast } from 'vue-sonner'

export function useToast() {
  const baseClass =
    'flex items-center gap-2 px-4 py-3 rounded-lg shadow-md font-medium text-sm'

  const variants = {
    success: 'bg-green-600 text-white',
    error: 'bg-red-600 text-white',
    info: 'bg-blue-600 text-white',
    warning: 'bg-yellow-400 text-black',
  }

  const showSuccess = (message: string, description?: string) => {
    toast.success(message, {
      unstyled: true,
      description,
      duration: 4000,
      class: `${baseClass} ${variants.success}`,
    })
  }

  const showError = (message: string, description?: string) => {
    toast.error(message, {
      unstyled: true,
      description,
      duration: 5000,
      class: `${baseClass} ${variants.error}`,
    })
  }

  const showInfo = (message: string, description?: string) => {
    toast(message, {
      unstyled: true,
      description,
      duration: 4000,
      class: `${baseClass} ${variants.info}`,
    })
  }

  const showWarning = (message: string, description?: string) => {
    toast.warning(message, {
      unstyled: true,
      description,
      duration: 4000,
      class: `${baseClass} ${variants.warning}`,
    })
  }

  const showLoading = (message: string, description?: string) =>
    toast.loading(message, { description, class: `${baseClass} bg-gray-800 text-white` })

  const showPromise = <T,>(
    promise: Promise<T>,
    messages: {
      loading: string
      success: string | ((data: T) => string)
      error: string | ((error: Error) => string)
    }
  ) => toast.promise(promise, messages)

  const dismiss = (id?: string | number) => toast.dismiss(id)
  const dismissAll = () => toast.dismiss()

  return {
    showSuccess,
    showError,
    showInfo,
    showWarning,
    showLoading,
    showPromise,
    dismiss,
    dismissAll,
    toast,
  }
}
