import { z } from 'zod'

export const digitalArchiveSchema = z.object({
    name: z.string().min(1, 'Nome é obrigatório'),

    description: z.string().optional().or(z.literal('')),

    document_type: z.string().min(1, 'Tipo de documento é obrigatório'),

    archivable_type: z.string().optional().or(z.literal('')),

    archivable_id: z.string().optional().or(z.literal('')),

    is_public: z.boolean().optional().default(false),

    expires_at: z.string().optional().or(z.literal('')),
})

export type DigitalArchiveFormData = z.infer<typeof digitalArchiveSchema>









