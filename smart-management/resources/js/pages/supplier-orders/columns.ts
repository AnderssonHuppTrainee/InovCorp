import type { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Eye, MoreHorizontal, Trash2 } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

export type SupplierOrder = {
    id: number
    number: string
    order_date: string
    supplier: {
        id: number
        name: string
    }
    order?: {
        id: number
        number: string
    }
    total_amount: number
    status: string
    created_at: string
}

const statusMap: Record<string, { label: string; variant: any }> = {
    draft: { label: 'Rascunho', variant: 'secondary' },
    closed: { label: 'Fechada', variant: 'default' },
}

export const columns: ColumnDef<SupplierOrder>[] = [
    {
        accessorKey: 'order_date',
        header: 'Data',
        cell: ({ row }) => {
            const date = new Date(row.original.order_date)
            return h('div', {}, date.toLocaleDateString('pt-PT'))
        },
    },
    {
        accessorKey: 'number',
        header: 'Número',
        cell: ({ row }) => {
            return h('div', { class: 'font-mono font-semibold' }, row.original.number)
        },
    },
    {
        accessorKey: 'supplier.name',
        header: 'Fornecedor',
        cell: ({ row }) => {
            return h('div', {}, row.original.supplier?.name || '-')
        },
    },
    {
        accessorKey: 'order.number',
        header: 'Encomenda Cliente',
        cell: ({ row }) => {
            const orderNumber = row.original.order?.number
            return h('div', { class: 'font-mono text-sm' }, orderNumber || '-')
        },
    },
    {
        accessorKey: 'total_amount',
        header: 'Valor Total',
        cell: ({ row }) => {
            const value = row.original.total_amount
            const amount = typeof value === 'number' ? value : parseFloat(value ?? '0')
            const validAmount = isNaN(amount) ? 0 : amount
            return h('div', { class: 'font-semibold' }, `€${validAmount.toFixed(2)}`)
        },
    },
    {
        accessorKey: 'status',
        header: 'Estado',
        cell: ({ row }) => {
            const status = row.original.status
            const statusInfo = statusMap[status] || { label: status, variant: 'secondary' }
            return h(
                Badge,
                {
                    variant: statusInfo.variant,
                },
                () => statusInfo.label,
            )
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const supplierOrder = row.original

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
                                        {
                                            variant: 'ghost',
                                            class: 'h-8 w-8 p-0',
                                        },
                                        {
                                            default: () => [
                                                h('span', { class: 'sr-only' }, 'Open menu'),
                                                h(MoreHorizontal, { class: 'h-4 w-4' }),
                                            ],
                                        },
                                    ),
                            },
                        ),
                        h(
                            DropdownMenuContent,
                            { align: 'end' },
                            {
                                default: () => [
                                    h(DropdownMenuLabel, {}, () => 'Ações'),
                                    h(DropdownMenuSeparator),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            onClick: () =>
                                                router.visit(`/supplier-orders/${supplierOrder.id}`),
                                        },
                                        {
                                            default: () => [
                                                h(Eye, { class: 'mr-2 h-4 w-4' }),
                                                h('span', {}, 'Ver'),
                                            ],
                                        },
                                    ),
                                    h(DropdownMenuSeparator),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            class: 'text-red-600',
                                            onClick: () => {
                                                if (
                                                    confirm(
                                                        'Tem certeza que deseja eliminar esta encomenda de fornecedor?',
                                                    )
                                                ) {
                                                    router.delete(`/supplier-orders/${supplierOrder.id}`)
                                                }
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(Trash2, { class: 'mr-2 h-4 w-4' }),
                                                h('span', {}, 'Eliminar'),
                                            ],
                                        },
                                    ),
                                ],
                            },
                        ),
                    ],
                },
            )
        },
    },
]

