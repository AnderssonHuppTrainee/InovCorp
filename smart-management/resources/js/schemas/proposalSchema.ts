import { z } from 'zod'

const proposalItemSchema = z.object({
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
    cost_price: z
        .number()
        .min(0, 'Preço de custo deve ser maior ou igual a zero')
        .optional()
        .or(z.string().transform((val) => (val ? parseFloat(val) : undefined)))
        .or(z.literal('')),
    notes: z.string().optional(),
})

export const proposalSchema = z.object({
    proposal_date: z.string().min(1, 'Data da proposta é obrigatória'),

    client_id: z.string().min(1, 'Cliente é obrigatório'),

    validity_date: z.string().min(1, 'Data de validade é obrigatória'),

    status: z.enum(['draft', 'closed']).default('draft'),

    items: z
        .array(proposalItemSchema)
        .min(1, 'A proposta deve ter pelo menos um item'),
})

export type ProposalFormData = z.infer<typeof proposalSchema>
export type ProposalItemFormData = z.infer<typeof proposalItemSchema>


