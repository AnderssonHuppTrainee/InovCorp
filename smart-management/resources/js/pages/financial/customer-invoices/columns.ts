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
    CreditCard,
} from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import { useMoneyFormatter } from '@/composables/formatters/useMoneyFormatter'
import { useDateFormatter } from '@/composables/formatters/useDateFormatter'

const { format: formatMoney } = useMoneyFormatter()
const { formatDate } = useDateFormatter()

export interface CustomerInvoice {
    id: number
    number: string
    invoice_date: string
    due_date: string
    customer: {
        id: number
        name: string
    }
    order: {
        id: number
        number: string
    } | null
    total_amount: number
    paid_amount: number
    balance: number
    status: 'draft' | 'sent' | 'partially_paid' | 'paid' | 'overdue' | 'cancelled'
}

const statusLabels: Record<CustomerInvoice['status'], string> = {
    draft: 'Rascunho',
    sent: 'Enviada',
    partially_paid: 'Parcialmente Paga',
    paid: 'Paga',
    overdue: 'Vencida',
    cancelled: 'Cancelada',
}

const statusVariants: Record<CustomerInvoice['status'], 'default' | 'secondary' | 'destructive' | 'outline'> = {
    draft: 'secondary',
    sent: 'outline',
    partially_paid: 'default',
    paid: 'default',
    overdue: 'destructive',
    cancelled: 'secondary',
}

export const columns: ColumnDef<CustomerInvoice>[] = [
    {
        accessorKey: 'invoice_date',
        header: 'Data',
        cell: ({ row }) => {
            return h('div', {}, formatDate(row.getValue('invoice_date')))
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
        accessorKey: 'customer',
        header: 'Cliente',
        cell: ({ row }) => {
            const customer = row.getValue('customer') as CustomerInvoice['customer']
            return h('div', {}, customer?.name || '-')
        },
    },
    {
        accessorKey: 'due_date',
        header: 'Vencimento',
        cell: ({ row }) => {
            const date = new Date(row.getValue('due_date'))
            const today = new Date()
            const isOverdue = date < today && row.original.status !== 'paid'
            
            return h(
                'div',
                { class: isOverdue ? 'text-destructive font-medium' : '' },
                formatDate(row.getValue('due_date'))
            )
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
        accessorKey: 'balance',
        header: 'Saldo',
        cell: ({ row }) => {
            const amount = parseFloat(row.getValue('balance') ?? '0')
            const validAmount = isNaN(amount) ? 0 : amount
            return h(
                'div',
                {
                    class: `font-medium ${validAmount > 0 ? 'text-destructive' : 'text-green-600'}`,
                },
                formatMoney(row.getValue('balance'))
            )
        },
    },
    {
        accessorKey: 'status',
        header: 'Estado',
        cell: ({ row }) => {
            const status = row.getValue('status') as CustomerInvoice['status']
            return h(
                Badge,
                {
                    variant: statusVariants[status],
                },
                {
                    default: () => statusLabels[status],
                }
            )
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const invoice = row.original

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
                                                router.get(`/customer-invoices/${invoice.id}`)
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
                                                router.get(`/customer-invoices/${invoice.id}/edit`)
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(Pencil, { class: 'mr-2 h-4 w-4' }),
                                                'Editar',
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
                                                        `Eliminar fatura "${invoice.number}"?`
                                                    )
                                                ) {
                                                    router.delete(`/customer-invoices/${invoice.id}`, {
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
                                ].filter(Boolean),
                            }
                        ),
                    ],
                }
            )
        },
    },
]





