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

export interface Contact {
    id: number
    number: string
    first_name: string
    last_name: string
    entity: {
        id: number
        name: string
    }
    role: {
        id: number
        name: string
    }
    phone: string | null
    mobile: string | null
    email: string | null
    status: 'active' | 'inactive'
}

export const columns: ColumnDef<Contact>[] = [
    {
        accessorKey: 'first_name',
        header: 'Nome',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('first_name'))
        },
    },
    {
        accessorKey: 'last_name',
        header: 'Apelido',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('last_name'))
        },
    },
    {
        accessorKey: 'role',
        header: 'Função',
        cell: ({ row }) => {
            const role = row.getValue('role') as Contact['role']
            return h('div', {}, role?.name || '-')
        },
    },
    {
        accessorKey: 'entity',
        header: 'Entidade',
        cell: ({ row }) => {
            const entity = row.getValue('entity') as Contact['entity']
            return h('div', {}, entity?.name || '-')
        },
    },
    {
        accessorKey: 'phone',
        header: 'Telefone',
        cell: ({ row }) => {
            const phone = row.getValue('phone') as string | null
            if (!phone) return h('div', { class: 'text-muted-foreground' }, '-')
            return h(
                'a',
                {
                    href: `tel:${phone}`,
                    class: 'text-primary hover:underline',
                },
                phone
            )
        },
    },
    {
        accessorKey: 'mobile',
        header: 'Telemóvel',
        cell: ({ row }) => {
            const mobile = row.getValue('mobile') as string | null
            if (!mobile) return h('div', { class: 'text-muted-foreground' }, '-')
            return h(
                'a',
                {
                    href: `tel:${mobile}`,
                    class: 'text-primary hover:underline',
                },
                mobile
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
            const contact = row.original

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
                                                router.get(`/contacts/${contact.id}`)
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
                                                router.get(`/contacts/${contact.id}/edit`)
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
                                                        `Tem certeza que deseja eliminar "${contact.first_name} ${contact.last_name}"?`
                                                    )
                                                ) {
                                                    router.delete(`/contacts/${contact.id}`, {
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


