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
} from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

export interface BankAccount {
    id: number
    name: string
    account_number: string
    iban: string | null
    swift: string | null
    bank_name: string
    balance: number
    currency: string
    is_active: boolean
}

export const columns: ColumnDef<BankAccount>[] = [
    {
        accessorKey: 'name',
        header: 'Nome',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('name'))
        },
    },
    {
        accessorKey: 'bank_name',
        header: 'Banco',
        cell: ({ row }) => {
            return h('div', {}, row.getValue('bank_name'))
        },
    },
    {
        accessorKey: 'account_number',
        header: 'Número da Conta',
        cell: ({ row }) => {
            return h('div', { class: 'font-mono text-sm' }, row.getValue('account_number'))
        },
    },
    {
        accessorKey: 'iban',
        header: 'IBAN',
        cell: ({ row }) => {
            const iban = row.getValue('iban') as string | null
            return h('div', { class: 'font-mono text-sm' }, iban || '-')
        },
    },
    {
        accessorKey: 'balance',
        header: 'Saldo',
        cell: ({ row }) => {
            const amount = parseFloat(row.getValue('balance') ?? '0')
            const validAmount = isNaN(amount) ? 0 : amount
            const currency = row.original.currency
            const formatted = new Intl.NumberFormat('pt-PT', {
                style: 'currency',
                currency: currency || 'EUR',
            }).format(validAmount)
            return h(
                'div',
                {
                    class: `font-medium ${validAmount < 0 ? 'text-destructive' : ''}`,
                },
                formatted
            )
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Estado',
        cell: ({ row }) => {
            const isActive = row.getValue('is_active') as boolean
            return h(
                Badge,
                {
                    variant: isActive ? 'default' : 'secondary',
                },
                {
                    default: () => (isActive ? 'Ativa' : 'Inativa'),
                }
            )
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const account = row.original

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
                                                router.get(`/bank-accounts/${account.id}`)
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
                                                router.get(`/bank-accounts/${account.id}/edit`)
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
                                                        `Eliminar conta "${account.name}"?`
                                                    )
                                                ) {
                                                    router.delete(`/bank-accounts/${account.id}`, {
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





