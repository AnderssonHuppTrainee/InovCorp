import { z } from 'zod'

export const countrySchema = z.object({
    name: z.string().min(1, 'Nome é obrigatório').max(255),
    code: z
        .string()
        .min(2, 'Código deve ter 2 caracteres')
        .max(2, 'Código deve ter 2 caracteres')
        .toUpperCase(),
    phone_code: z.string().max(10).optional().nullable(),
    is_active: z.boolean().default(true),
})

export type CountryFormData = z.infer<typeof countrySchema>



