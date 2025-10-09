import { z } from 'zod'

export const roleSchema = z.object({
    name: z.string().min(1, 'Nome do grupo é obrigatório'),

    permissions: z.array(z.string()).optional().default([]),
})

export type RoleFormData = z.infer<typeof roleSchema>


