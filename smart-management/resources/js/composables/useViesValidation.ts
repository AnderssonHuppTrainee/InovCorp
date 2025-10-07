import { ref, computed } from 'vue'

export function useViesValidation(form) {
  const loading = ref(false)
  const vatResult = ref(null)

  const validateVat = async () => {
    if (!form.tax_number) return
    loading.value = true
    vatResult.value = null

    try {
      const response = await fetch(route('entities.vies-check'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ vat_number: form.tax_number }),
      })
      const result = await response.json()
      vatResult.value = result

      if (result.valid) {
        if (result.name && !form.name) form.name = result.name
        if (result.address && !form.address) form.address = result.address
      }
    } catch (e) {
      vatResult.value = { valid: false, error: 'Erro de conexão' }
    } finally {
      loading.value = false
    }
  }

  const vatClass = computed(() => (vatResult.value?.valid ? 'border-green-500' : ''))

  return { validateVat, vatResult, loading, vatClass }
}


