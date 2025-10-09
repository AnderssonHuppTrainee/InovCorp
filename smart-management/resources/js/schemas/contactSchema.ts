import { z } from 'zod'

export const contactSchema = z.object({
    entity_id: z.string().min(1, 'Entidade é obrigatória'),

    first_name: z
        .string()
        .min(1, 'Nome é obrigatório')
        .min(2, 'Nome deve ter pelo menos 2 caracteres'),

    last_name: z
        .string()
        .min(1, 'Apelido é obrigatório')
        .min(2, 'Apelido deve ter pelo menos 2 caracteres'),

    contact_role_id: z.string().min(1, 'Função é obrigatória'),

    phone: z.string().optional().or(z.literal('')),

    mobile: z.string().optional().or(z.literal('')),

    email: z.string().email('Email deve ser válido').optional().or(z.literal('')),

    gdpr_consent: z.boolean().optional().default(false),

    observations: z.string().optional(),

    status: z.enum(['active', 'inactive']).default('active'),
})

export type ContactFormData = z.infer<typeof contactSchema>


