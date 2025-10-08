
import { ref } from 'vue'
import axios from 'axios'
import { useAddressParser } from './useAddressParser'

interface VatValidationResult {
  valid: boolean
  name?: string
  address?: string
  postal_code?: string
  city?: string
  error?: string | null
}

export function useViesValidation() {
  const vatLoading = ref(false)
  const vatValid = ref(false)
  const vatResult = ref<VatValidationResult | null>(null)
  const error = ref<string | null>(null)
  const { parseAddress } = useAddressParser()

  const validateVat = async (vatNumber: string): Promise<VatValidationResult | null> => {
    if (!vatNumber) return null

    vatLoading.value = true
    vatValid.value = false
    vatResult.value = null
    error.value = null

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

      const response = await axios.post(
        '/entities/vies-check',
        { vat_number: vatNumber },
        {
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
          },
        },
      )

      const data = response.data

      if (data.valid) {
        vatValid.value = true
        const parsed = parseAddress(data.address)
        vatResult.value = { valid: true, ...data, ...parsed }
        return vatResult.value
      } else {
        vatValid.value = false
        error.value = data.error || 'Número VAT inválido'
        vatResult.value = { valid: false, error: error.value }
      }
    } catch (err: any) {
      error.value = err.response?.data?.error || 'Erro ao validar o VAT.'
      vatResult.value = { valid: false, error: error.value }
    } finally {
      vatLoading.value = false
    }

    return vatResult.value
  }

  return { vatLoading, vatValid, vatResult, error, validateVat }
}
