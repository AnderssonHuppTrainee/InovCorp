import { z } from 'zod'

export const articleSchema = z.object({
    reference: z.string().min(1, 'Referência é obrigatória'),

    name: z.string().min(1, 'Nome é obrigatório'),

    description: z.string().optional().or(z.literal('')),

    price: z
        .number()
        .min(0, 'Preço deve ser maior ou igual a zero')
        .or(z.string().transform((val) => parseFloat(val))),

    tax_rate_id: z.string().min(1, 'IVA é obrigatório'),

    observations: z.string().optional().or(z.literal('')),

    status: z.enum(['active', 'inactive']).default('active'),
})

export type ArticleFormData = z.infer<typeof articleSchema>

