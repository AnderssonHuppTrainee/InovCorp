export function useAddressParser() {
  const parseAddress = (address?: string) => {
    if (!address) return { postal_code: '', city: '' }

    const result = { postal_code: '', city: '' }

    // Expressão regular para encontrar código postal
    const postalRegex = /(\d{4}-\d{3})/
    const match = address.match(postalRegex)

    if (match) {
      result.postal_code = match[1]

      // Extrai o texto após o código postal (geralmente a cidade)
      const afterPostal = address.split(match[1])[1]?.trim() || ''

      // Remove pontuação e espaços extra
      const cleaned = afterPostal.replace(/\.$/, '').trim()

      // A cidade geralmente é a última palavra após o postal
      const tokens = cleaned.split(/\s+/)
      if (tokens.length) {
        result.city = tokens[tokens.length - 1].toUpperCase()
      }
    }

    return result
  }

  return { parseAddress }
}