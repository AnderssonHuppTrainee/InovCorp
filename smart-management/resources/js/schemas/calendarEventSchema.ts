import { z } from 'zod'

export const calendarEventSchema = z.object({
    event_date: z.string().min(1, 'Data é obrigatória'),

    event_time: z.string().min(1, 'Hora é obrigatória'),

    duration: z
        .number()
        .min(1, 'Duração deve ser maior que zero')
        .or(z.string().transform((val) => parseInt(val))),

    shared_with: z.array(z.union([z.string(), z.number()])).optional().default([]),

    knowledge: z.boolean().default(false),

    entity_id: z.string().transform(val => val || undefined).optional(),

    calendar_event_type_id: z.string().min(1, 'Tipo é obrigatório'),

    calendar_action_id: z.string().min(1, 'Ação é obrigatória'),

    description: z.string().min(1, 'Descrição é obrigatória'),

    status: z.enum(['scheduled', 'completed', 'cancelled']).default('scheduled'),
})

export type CalendarEventFormData = z.infer<typeof calendarEventSchema>



