import { z } from 'zod'

export const taxRateSchema = z.object({
    name: z.string().min(1, 'Nome é obrigatório'),

    rate: z
        .number()
        .min(0, 'Taxa deve ser maior ou igual a zero')
        .max(100, 'Taxa não pode ser superior a 100%')
        .or(z.string().transform((val) => parseFloat(val))),

    is_active: z.boolean().optional().default(true),
})

export type TaxRateFormData = z.infer<typeof taxRateSchema>



