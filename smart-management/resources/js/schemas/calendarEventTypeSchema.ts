import { z } from 'zod'

export const calendarEventTypeSchema = z.object({
    name: z.string().min(1, 'Nome é obrigatório').max(255),
    color: z
        .string()
        .min(1, 'Cor é obrigatória')
        .regex(/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/, 'Formato de cor inválido (ex: #FF0000)'),
    is_active: z.boolean().default(true),
})

export type CalendarEventTypeFormData = z.infer<typeof calendarEventTypeSchema>



