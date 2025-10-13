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
import { Eye, MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next'
import { Link, router } from '@inertiajs/vue3'

export type Country = {
    id: number
    name: string
    code: string
    phone_code: string | null
    is_active: boolean
    created_at: string
}

export const columns: ColumnDef<Country>[] = [
    {
        accessorKey: 'name',
        header: 'Nome',
    },
    {
        accessorKey: 'code',
        header: 'Código',
        cell: ({ row }) => {
            return h('div', { class: 'font-mono font-semibold' }, row.original.code)
        },
    },
    {
        accessorKey: 'phone_code',
        header: 'Código Telefónico',
        cell: ({ row }) => {
            return h('div', { class: 'font-mono' }, row.original.phone_code || '-')
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Estado',
        cell: ({ row }) => {
            const isActive = row.original.is_active
            return h(
                Badge,
                {
                    variant: isActive ? 'default' : 'secondary',
                },
                () => (isActive ? 'Ativo' : 'Inativo'),
            )
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const country = row.original

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
                                            onClick: () => router.visit(`/countries/${country.id}`),
                                        },
                                        {
                                            default: () => [
                                                h(Eye, { class: 'mr-2 h-4 w-4' }),
                                                h('span', {}, 'Ver'),
                                            ],
                                        },
                                    ),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            onClick: () => router.visit(`/countries/${country.id}/edit`),
                                        },
                                        {
                                            default: () => [
                                                h(Pencil, { class: 'mr-2 h-4 w-4' }),
                                                h('span', {}, 'Editar'),
                                            ],
                                        },
                                    ),
                                    h(DropdownMenuSeparator),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            class: 'text-red-600',
                                            onClick: () => {
                                                if (confirm('Tem certeza que deseja eliminar este país?')) {
                                                    router.delete(`/countries/${country.id}`)
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



