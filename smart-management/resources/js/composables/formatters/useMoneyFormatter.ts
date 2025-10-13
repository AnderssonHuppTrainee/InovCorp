/**
 * Composable para formatação consistente de valores monetários
 * 
 * @example
 * ```typescript
 * const { format, formatSimple } = useMoneyFormatter()
 * 
 * // Em columns.ts:
 * cell: ({ row }) => h('div', {}, format(row.getValue('total_amount')))
 * ```
 */

export function useMoneyFormatter(
    options: {
        currency?: string
        locale?: string
    } = {},
) {
    const currency = options.currency ?? 'EUR'
    const locale = options.locale ?? 'pt-PT'

    /**
     * Formata valor como moeda usando Intl.NumberFormat
     * Garante tratamento seguro de NaN e null
     */
    const format = (value: number | string | null | undefined): string => {
        const numValue =
            typeof value === 'number' ? value : parseFloat(value ?? '0')

        const validValue = isNaN(numValue) ? 0 : numValue

        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(validValue)
    }

    /**
     * Formatação simples com símbolo € e toFixed(2)
     * Mais rápido que Intl mas menos flexível
     */
    const formatSimple = (
        value: number | string | null | undefined,
    ): string => {
        const numValue =
            typeof value === 'number' ? value : parseFloat(value ?? '0')

        const validValue = isNaN(numValue) ? 0 : numValue
        return `€${validValue.toFixed(2)}`
    }

    /**
     * Converte string formatada de volta para número
     */
    const parse = (formatted: string): number => {
        const cleaned = formatted.replace(/[^0-9,.-]/g, '').replace(',', '.')
        return parseFloat(cleaned) || 0
    }

    /**
     * Valida se um valor é um número válido
     */
    const isValid = (value: any): boolean => {
        if (value === null || value === undefined) return false
        const num = typeof value === 'number' ? value : parseFloat(value)
        return !isNaN(num) && isFinite(num)
    }

    return {
        format,
        formatSimple,
        parse,
        isValid,
    }
}

