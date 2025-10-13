/**
 * Composable para formatação consistente de datas
 * 
 * @example
 * ```typescript
 * const { formatDate, formatDateTime } = useDateFormatter()
 * 
 * // Em columns.ts:
 * cell: ({ row }) => h('div', {}, formatDate(row.getValue('order_date')))
 * ```
 */

export function useDateFormatter(locale: string = 'pt-PT') {
    /**
     * Formata data usando Intl.DateTimeFormat
     * Retorna '-' se a data for null/inválida
     */
    const formatDate = (
        date: string | Date | null,
        options: Intl.DateTimeFormatOptions = { dateStyle: 'short' },
    ): string => {
        if (!date) return '-'
        try {
            return new Intl.DateTimeFormat(locale, options).format(
                new Date(date),
            )
        } catch {
            return '-'
        }
    }

    /**
     * Formata data e hora completos
     */
    const formatDateTime = (date: string | Date | null): string => {
        return formatDate(date, { dateStyle: 'short', timeStyle: 'short' })
    }

    /**
     * Formata data por extenso
     */
    const formatLongDate = (date: string | Date | null): string => {
        return formatDate(date, { dateStyle: 'long' })
    }

    /**
     * Formata data relativa (hoje, amanhã, há 2 dias, etc)
     */
    const formatRelative = (date: string | Date | null): string => {
        if (!date) return '-'
        
        try {
            const rtf = new Intl.RelativeTimeFormat(locale, { numeric: 'auto' })
            const now = new Date()
            const then = new Date(date)
            const diffInDays = Math.floor(
                (then.getTime() - now.getTime()) / (1000 * 60 * 60 * 24),
            )

            if (Math.abs(diffInDays) < 1) return 'hoje'
            if (Math.abs(diffInDays) < 7) return rtf.format(diffInDays, 'day')
            if (Math.abs(diffInDays) < 30)
                return rtf.format(Math.floor(diffInDays / 7), 'week')
            return formatDate(date)
        } catch {
            return formatDate(date)
        }
    }

    /**
     * Normaliza data para formato YYYY-MM-DD
     * Útil para DatePicker e backend
     */
    const normalizeToYMD = (date: string): string => {
        // "2025-10-13T00:00:00.000000Z" → "2025-10-13"
        if (/^\d{4}-\d{2}-\d{2}$/.test(date)) return date
        if (date.includes('T')) return date.split('T')[0]
        return date
    }

    /**
     * Converte string para objeto Date
     * Retorna null se inválido
     */
    const parseDate = (dateString: string): Date | null => {
        try {
            const date = new Date(dateString)
            return isNaN(date.getTime()) ? null : date
        } catch {
            return null
        }
    }

    return {
        formatDate,
        formatDateTime,
        formatLongDate,
        formatRelative,
        normalizeToYMD,
        parseDate,
    }
}

