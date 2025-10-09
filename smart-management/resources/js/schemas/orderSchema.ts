import { z } from 'zod'

const orderItemSchema = z.object({
    article_id: z.string().min(1, 'Artigo é obrigatório'),
    supplier_id: z.string().optional().or(z.literal('')),
    quantity: z
        .number()
        .min(0.01, 'Quantidade deve ser maior que zero')
        .or(z.string().transform((val) => parseFloat(val))),
    unit_price: z
        .number()
        .min(0, 'Preço unitário deve ser maior ou igual a zero')
        .or(z.string().transform((val) => parseFloat(val))),
    notes: z.string().optional(),
})

export const orderSchema = z.object({
    order_date: z.string().min(1, 'Data da encomenda é obrigatória'),

    client_id: z.string().min(1, 'Cliente é obrigatório'),

    delivery_date: z.string().optional().or(z.literal('')),

    status: z.enum(['draft', 'closed']).default('draft'),

    items: z
        .array(orderItemSchema)
        .min(1, 'A encomenda deve ter pelo menos um item'),
})

export type OrderFormData = z.infer<typeof orderSchema>
export type OrderItemFormData = z.infer<typeof orderItemSchema>


