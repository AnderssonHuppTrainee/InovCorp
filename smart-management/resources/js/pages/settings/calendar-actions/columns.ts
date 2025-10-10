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
import { router } from '@inertiajs/vue3'

export type CalendarAction = {
    id: number
    name: string
    description: string | null
    is_active: boolean
    created_at: string
}

export const columns: ColumnDef<CalendarAction>[] = [
    {
        accessorKey: 'name',
        header: 'Nome',
    },
    {
        accessorKey: 'description',
        header: 'Descrição',
        cell: ({ row }) => {
            const description = row.original.description
            return h(
                'div',
                { class: 'max-w-md truncate' },
                description || h('span', { class: 'text-muted-foreground' }, '-'),
            )
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
                () => (isActive ? 'Ativa' : 'Inativa'),
            )
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const action = row.original

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
                                            onClick: () => router.visit(`/calendar-actions/${action.id}`),
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
                                            onClick: () =>
                                                router.visit(`/calendar-actions/${action.id}/edit`),
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
                                                if (confirm('Tem certeza que deseja eliminar esta ação?')) {
                                                    router.delete(`/calendar-actions/${action.id}`)
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


