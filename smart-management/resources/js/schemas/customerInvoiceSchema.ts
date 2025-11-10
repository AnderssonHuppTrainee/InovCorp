import { z } from 'zod'

export const customerInvoiceSchema = z.object({
    invoice_date: z.string().min(1, 'Data da fatura é obrigatória'),

    due_date: z.string().min(1, 'Data de vencimento é obrigatória'),

    customer_id: z.coerce.string().min(1, 'Cliente é obrigatório'),

    order_id: z
        .union([
            z.coerce.string(),
            z.literal('none'),
            z.literal(''),
        ])
        .optional()
        .transform((val) => {
            if (!val || val === 'none' || val === '') return '';
            return String(val);
        }),

    total_amount: z
        .number()
        .min(0, 'Valor total deve ser maior ou igual a zero')
        .or(z.string().transform((val) => parseFloat(val))),

    paid_amount: z
        .number()
        .min(0, 'Valor pago deve ser maior ou igual a zero')
        .optional()
        .or(z.string().transform((val) => parseFloat(val))),

    notes: z.string().optional().or(z.literal('')),

    status: z.enum(['draft', 'sent', 'partially_paid', 'paid', 'overdue', 'cancelled']).default('draft'),
})

export type CustomerInvoiceFormData = z.infer<typeof customerInvoiceSchema>

export const paymentSchema = z.object({
    amount: z
        .number()
        .min(0.01, 'Valor do pagamento deve ser maior que zero'),

    payment_date: z.string().min(1, 'Data do pagamento é obrigatória'),

    notes: z.string().optional().or(z.literal('')),
})

export type PaymentFormData = z.infer<typeof paymentSchema>















