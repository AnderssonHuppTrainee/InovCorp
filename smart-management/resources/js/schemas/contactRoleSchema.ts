import { z } from 'zod'

export const contactRoleSchema = z.object({
    name: z.string().min(1, 'Nome é obrigatório').max(255),
    description: z.string().max(1000).optional().nullable(),
    is_active: z.boolean().default(true),
})

export type ContactRoleFormData = z.infer<typeof contactRoleSchema>


