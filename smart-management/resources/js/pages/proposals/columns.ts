import { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import {
    MoreHorizontal,
    Pencil,
    Trash2,
    Eye,
    FileText,
    ShoppingCart,
} from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import { useMoneyFormatter } from '@/composables/formatters/useMoneyFormatter'
import { useDateFormatter } from '@/composables/formatters/useDateFormatter'

const { format: formatMoney } = useMoneyFormatter()
const { formatDate } = useDateFormatter()

export interface Proposal {
    id: number
    number: string
    proposal_date: string
    validity_date: string
    client_id: number;
    client?: {
        id: number
        name: string
    }
    total_amount: number
    status: 'draft' | 'closed'
    created_at: string;
    updated_at: string;
}

export const columns: ColumnDef<Proposal>[] = [
    {
        accessorKey: 'proposal_date',
        header: 'Data',
        cell: ({ row }) => {
            return h('div', {}, formatDate(row.getValue('proposal_date')))
        },
    },
    {
        accessorKey: 'number',
        header: 'Número',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('number'))
        },
    },
    {
        accessorKey: 'validity_date',
        header: 'Validade',
        cell: ({ row }) => {
            return h('div', {}, formatDate(row.getValue('validity_date')))
        },
    },
    {
        accessorKey: 'client',
        header: 'Cliente',
        cell: ({ row }) => {
            const client = row.getValue('client') as Proposal['client']
            return h('div', {}, client?.name || '-')
        },
    },
    {
        accessorKey: 'total_amount',
        header: 'Valor Total',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, formatMoney(row.getValue('total_amount')))
        },
    },
    {
        accessorKey: 'status',
        header: 'Estado',
        cell: ({ row }) => {
            const status = row.getValue('status') as string
            return h(
                Badge,
                {
                    variant: status === 'closed' ? 'default' : 'secondary',
                },
                {
                    default: () => (status === 'draft' ? 'Rascunho' : 'Fechado'),
                }
            )
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const proposal = row.original

            return h(
                DropdownMenu,
                {},
                {
                    default: () => [
                        h(
                            DropdownMenuTrigger,
                            { asChild: true },
                            {
                                default: () =>
                                    h(
                                        Button,
                                        { variant: 'ghost', class: 'h-8 w-8 p-0' },
                                        {
                                            default: () => [
                                                h('span', { class: 'sr-only' }, 'Abrir menu'),
                                                h(MoreHorizontal, { class: 'h-4 w-4' }),
                                            ],
                                        }
                                    ),
                            }
                        ),
                        h(
                            DropdownMenuContent,
                            { align: 'end' },
                            {
                                default: () => [
                                    h(DropdownMenuLabel, {}, { default: () => 'Ações' }),
                                    h(DropdownMenuSeparator),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            onClick: () => {
                                                router.get(`/proposals/${proposal.id}`)
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(Eye, { class: 'mr-2 h-4 w-4' }),
                                                'Ver detalhes',
                                            ],
                                        }
                                    ),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            onClick: () => {
                                                router.get(`/proposals/${proposal.id}/edit`)
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(Pencil, { class: 'mr-2 h-4 w-4' }),
                                                'Editar',
                                            ],
                                        }
                                    ),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            onClick: () => {
                                                window.open(
                                                    `/proposals/${proposal.id}/pdf`,
                                                    '_blank'
                                                )
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(FileText, { class: 'mr-2 h-4 w-4' }),
                                                'Download PDF',
                                            ],
                                        }
                                    ),
                                    h(DropdownMenuSeparator),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            onClick: () => {
                                                if (
                                                    confirm(
                                                        `Converter proposta "${proposal.number}" em encomenda?`
                                                    )
                                                ) {
                                                    router.post(
                                                        `/proposals/${proposal.id}/convert-to-order`,
                                                        {},
                                                        {
                                                            preserveScroll: true,
                                                        }
                                                    )
                                                }
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(ShoppingCart, { class: 'mr-2 h-4 w-4' }),
                                                'Converter em Encomenda',
                                            ],
                                        }
                                    ),
                                    h(DropdownMenuSeparator),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            class: 'text-destructive focus:text-destructive',
                                            onClick: () => {
                                                if (
                                                    confirm(
                                                        `Tem certeza que deseja eliminar a proposta "${proposal.number}"?`
                                                    )
                                                ) {
                                                    router.delete(`/proposals/${proposal.id}`, {
                                                        preserveScroll: true,
                                                    })
                                                }
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(Trash2, { class: 'mr-2 h-4 w-4' }),
                                                'Eliminar',
                                            ],
                                        }
                                    ),
                                ],
                            }
                        ),
                    ],
                }
            )
        },
    },
]


