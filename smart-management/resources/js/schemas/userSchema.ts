import { z } from 'zod'

export const userSchema = z.object({
    name: z.string().min(1, 'Nome é obrigatório'),

    email: z.string().email('Email inválido').min(1, 'Email é obrigatório'),

    mobile: z.string().optional().or(z.literal('')),

    password: z.string().min(8, 'Password deve ter pelo menos 8 caracteres').optional().or(z.literal('')),

    password_confirmation: z.string().optional().or(z.literal('')),

    roles: z.array(z.string()).optional().default([]),

    is_active: z.boolean().optional().default(true),
}).refine(
    (data) => {
        if (data.password && data.password !== '') {
            return data.password === data.password_confirmation
        }
        return true
    },
    {
        message: 'As passwords não coincidem',
        path: ['password_confirmation'],
    }
)

export type UserFormData = z.infer<typeof userSchema>






