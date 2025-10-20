import { z } from 'zod'

export const bankAccountSchema = z.object({
    name: z.string().min(1, 'Nome é obrigatório'),

    account_number: z.string().min(1, 'Número da conta é obrigatório'),

    iban: z.string().optional().or(z.literal('')),

    swift: z.string().optional().or(z.literal('')),

    bank_name: z.string().min(1, 'Nome do banco é obrigatório'),

    balance: z
        .number()
        .min(0, 'Saldo deve ser maior ou igual a zero')
        .optional()
        .default(0),

    currency: z.string().min(1, 'Moeda é obrigatória').default('EUR'),

    is_active: z.boolean().optional().default(true),
})

export type BankAccountFormData = z.infer<typeof bankAccountSchema>









