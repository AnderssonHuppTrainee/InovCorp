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

export interface User {
    id: number
    name: string
    email: string
    mobile: string | null
    is_active: boolean
    roles: Array<{ id: number; name: string }>
}

export const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: 'Nome',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('name'))
        },
    },
    {
        accessorKey: 'email',
        header: 'Email',
        cell: ({ row }) => {
            return h('div', { class: 'text-sm' }, row.getValue('email'))
        },
    },
    {
        accessorKey: 'mobile',
        header: 'Telemóvel',
        cell: ({ row }) => {
            const mobile = row.getValue('mobile') as string | null
            return h('div', { class: 'text-sm' }, mobile || '-')
        },
    },
    {
        accessorKey: 'roles',
        header: 'Grupo de Permissões',
        cell: ({ row }) => {
            const roles = row.getValue('roles') as User['roles']
            if (!roles || roles.length === 0) {
                return h('div', { class: 'text-sm text-muted-foreground' }, 'Sem grupo')
            }
            
            return h('div', { class: 'flex flex-wrap gap-1' }, roles.map(role =>
                h(Badge, { variant: 'outline', class: 'text-xs' }, { default: () => role.name })
            ))
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
                    default: () => (isActive ? 'Ativo' : 'Inativo'),
                }
            )
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const user = row.original

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
                                                router.get(`/users/${user.id}`)
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
                                                router.get(`/users/${user.id}/edit`)
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
                                                        `Eliminar utilizador "${user.name}"?`
                                                    )
                                                ) {
                                                    router.delete(`/users/${user.id}`, {
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




