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
    PackageIcon,
} from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import { useMoneyFormatter } from '@/composables/formatters/useMoneyFormatter'
import { useDateFormatter } from '@/composables/formatters/useDateFormatter'

const { format: formatMoney } = useMoneyFormatter()
const { formatDate } = useDateFormatter()

export interface Order {
    id: number
    number: string
    order_date: string
    delivery_date: string | null
    client: {
        id: number
        name: string
    }
    total_amount: number
    status: 'draft' | 'closed'
}

export const columns: ColumnDef<Order>[] = [
    {
        accessorKey: 'order_date',
        header: 'Data',
        cell: ({ row }) => {
            return h('div', {}, formatDate(row.getValue('order_date')))
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
        accessorKey: 'delivery_date',
        header: 'Entrega',
        cell: ({ row }) => {
            return h('div', { class: 'text-muted-foreground' }, formatDate(row.getValue('delivery_date')))
        },
    },
    {
        accessorKey: 'client',
        header: 'Cliente',
        cell: ({ row }) => {
            const client = row.getValue('client') as Order['client']
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
            const order = row.original

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
                                                router.get(`/orders/${order.id}`)
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
                                                router.get(`/orders/${order.id}/edit`)
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
                                                window.open(`/orders/${order.id}/pdf`, '_blank')
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
                                                        `Converter encomenda "${order.number}" para Encomendas de Fornecedores?\n\nSerá criada uma encomenda para cada fornecedor.`
                                                    )
                                                ) {
                                                    router.post(
                                                        `/orders/${order.id}/convert-to-supplier-orders`,
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
                                                h(PackageIcon, { class: 'mr-2 h-4 w-4' }),
                                                'Enc. Fornecedores',
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
                                                        `Tem certeza que deseja eliminar a encomenda "${order.number}"?`
                                                    )
                                                ) {
                                                    router.delete(`/orders/${order.id}`, {
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


