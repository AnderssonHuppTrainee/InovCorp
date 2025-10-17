import { z } from 'zod'

export const workOrderSchema = z.object({
    title: z
        .string()
        .min(1, 'Título é obrigatório')
        .min(3, 'Título deve ter pelo menos 3 caracteres'),

    description: z.string().optional(),

    client_id: z.string().min(1, 'Cliente é obrigatório'),

    assigned_to: z.string().min(1, 'Atribuir a um utilizador é obrigatório'),

    priority: z.enum(['low', 'medium', 'high', 'urgent']).default('medium'),

    start_date: z.string().optional().or(z.literal('')),

    end_date: z.string().optional().or(z.literal('')),

    status: z
        .enum(['pending', 'in_progress', 'completed', 'cancelled'])
        .default('pending'),
})

export type WorkOrderFormData = z.infer<typeof workOrderSchema>






