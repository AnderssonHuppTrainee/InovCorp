import { z } from 'zod'

export const supplierInvoiceSchema = z.object({
    invoice_date: z.string().min(1, 'Data da fatura é obrigatória'),

    due_date: z.string().min(1, 'Data de vencimento é obrigatória'),

    supplier_id: z.coerce.string().min(1, 'Fornecedor é obrigatório'),

    supplier_order_id: z
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

    status: z.enum(['pending_payment', 'paid']).default('pending_payment'),

    send_email: z.boolean().optional().default(false),
})

export type SupplierInvoiceFormData = z.infer<typeof supplierInvoiceSchema>















