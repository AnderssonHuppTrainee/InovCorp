import { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'
import { Button } from '@/components/ui/button'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { MoreHorizontal, Pencil, Trash2, Eye } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import route from '@/routes/entities'

export interface Entity {
    id: number
    tax_number: string
    name: string
    phone: string | null
    mobile: string | null
    website: string | null
    email: string | null
    status: 'active' | 'inactive'
    types: string[]
}

export const columns: ColumnDef<Entity>[] = [
    {
        accessorKey: 'tax_number',
        header: 'NIF',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('tax_number'))
        },
    },
    {
        accessorKey: 'name',
        header: 'Nome',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('name'))
        },
    },
    {
        accessorKey: 'phone',
        header: 'Telefone',
        cell: ({ row }) => {
            const phone = row.getValue('phone') as string | null
            return h('div', { class: 'text-muted-foreground' }, phone || '-')
        },
    },
    {
        accessorKey: 'mobile',
        header: 'Telemóvel',
        cell: ({ row }) => {
            const mobile = row.getValue('mobile') as string | null
            return h('div', { class: 'text-muted-foreground' }, mobile || '-')
        },
    },
    {
        accessorKey: 'website',
        header: 'Website',
        cell: ({ row }) => {
            const website = row.getValue('website') as string | null
            if (!website) return h('div', { class: 'text-muted-foreground' }, '-')
            return h(
                'a',
                {
                    href: website,
                    target: '_blank',
                    rel: 'noopener noreferrer',
                    class: 'text-primary hover:underline',
                },
                website
            )
        },
    },
    {
        accessorKey: 'email',
        header: 'Email',
        cell: ({ row }) => {
            const email = row.getValue('email') as string | null
            if (!email) return h('div', { class: 'text-muted-foreground' }, '-')
            return h(
                'a',
                {
                    href: `mailto:${email}`,
                    class: 'text-primary hover:underline',
                },
                email
            )
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const entity = row.original
            const type = entity.types[0] || 'client'

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
                                                router.get(route.show({ id: entity.id }).url)
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
                                                router.get(route.edit({ id: entity.id }).url)
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
                                                        `Tem certeza que deseja eliminar "${entity.name}"?`
                                                    )
                                                ) {
                                                    router.delete(
                                                        route.destroy({ id: entity.id }).url,
                                                        {
                                                            preserveScroll: true,
                                                        }
                                                    )
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


