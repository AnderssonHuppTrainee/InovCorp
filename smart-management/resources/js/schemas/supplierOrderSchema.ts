import { z } from 'zod'

export const supplierOrderSchema = z.object({
    supplier_id: z.number().min(1, 'Fornecedor é obrigatório'),
    order_date: z.string().min(1, 'Data da encomenda é obrigatória'),
    status: z.enum(['draft', 'closed']),
})

export type SupplierOrderFormData = z.infer<typeof supplierOrderSchema>

