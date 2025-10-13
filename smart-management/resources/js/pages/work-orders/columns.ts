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
import { MoreHorizontal, Pencil, Trash2, Eye } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

export interface WorkOrder {
    id: number
    number: string
    title: string
    client: {
        id: number
        name: string
    }
    assigned_user: {
        id: number
        name: string
    }
    priority: 'low' | 'medium' | 'high' | 'urgent'
    start_date: string | null
    end_date: string | null
    status: 'pending' | 'in_progress' | 'completed' | 'cancelled'
}

const priorityVariants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    low: 'secondary',
    medium: 'outline',
    high: 'default',
    urgent: 'destructive',
}

const priorityLabels: Record<string, string> = {
    low: 'Baixa',
    medium: 'Média',
    high: 'Alta',
    urgent: 'Urgente',
}

const statusVariants: Record<string, 'default' | 'secondary' | 'outline' | 'destructive'> = {
    pending: 'secondary',
    in_progress: 'default',
    completed: 'outline',
    cancelled: 'destructive',
}

const statusLabels: Record<string, string> = {
    pending: 'Pendente',
    in_progress: 'Em Progresso',
    completed: 'Concluído',
    cancelled: 'Cancelado',
}

export const columns: ColumnDef<WorkOrder>[] = [
    {
        accessorKey: 'number',
        header: 'Número',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('number'))
        },
    },
    {
        accessorKey: 'title',
        header: 'Título',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('title'))
        },
    },
    {
        accessorKey: 'client',
        header: 'Cliente',
        cell: ({ row }) => {
            const client = row.getValue('client') as WorkOrder['client']
            return h('div', {}, client?.name || '-')
        },
    },
    {
        accessorKey: 'assigned_user',
        header: 'Atribuído a',
        cell: ({ row }) => {
            const user = row.getValue('assigned_user') as WorkOrder['assigned_user']
            return h('div', {}, user?.name || '-')
        },
    },
    {
        accessorKey: 'priority',
        header: 'Prioridade',
        cell: ({ row }) => {
            const priority = row.getValue('priority') as string
            return h(
                Badge,
                {
                    variant: priorityVariants[priority] || 'outline',
                },
                {
                    default: () => priorityLabels[priority] || priority,
                }
            )
        },
    },
    {
        accessorKey: 'start_date',
        header: 'Início',
        cell: ({ row }) => {
            const date = row.getValue('start_date') as string | null
            if (!date) return h('div', { class: 'text-muted-foreground' }, '-')
            return h('div', {}, new Date(date).toLocaleDateString('pt-PT'))
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
                    variant: statusVariants[status] || 'outline',
                },
                {
                    default: () => statusLabels[status] || status,
                }
            )
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const workOrder = row.original

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
                                                router.get(`/work-orders/${workOrder.id}`)
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
                                                router.get(`/work-orders/${workOrder.id}/edit`)
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
                                                        `Tem certeza que deseja eliminar "${workOrder.title}"?`
                                                    )
                                                ) {
                                                    router.delete(`/work-orders/${workOrder.id}`, {
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





