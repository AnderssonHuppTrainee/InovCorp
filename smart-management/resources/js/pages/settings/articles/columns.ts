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

export interface Article {
    id: number
    reference: string
    name: string
    description: string | null
    price: number
    photo: string | null
    tax_rate: {
        id: number
        name: string
        rate: number
    }
    status: 'active' | 'inactive'
}

export const columns: ColumnDef<Article>[] = [
    {
        accessorKey: 'reference',
        header: 'Referência',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium font-mono' }, row.getValue('reference'))
        },
    },
    {
        accessorKey: 'photo',
        header: 'Foto',
        cell: ({ row }) => {
            const photo = row.getValue('photo') as string | null
            if (!photo) return h('div', { class: 'text-muted-foreground text-xs' }, '-')
            
            return h('img', {
                src: `/storage/${photo}`,
                alt: row.original.name,
                class: 'h-10 w-10 rounded object-cover',
            })
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
        accessorKey: 'description',
        header: 'Descrição',
        cell: ({ row }) => {
            const desc = row.getValue('description') as string | null
            return h('div', { class: 'text-sm text-muted-foreground truncate max-w-xs' }, desc || '-')
        },
    },
    {
        accessorKey: 'price',
        header: 'Preço',
        cell: ({ row }) => {
            const price = parseFloat(row.getValue('price'))
            const taxRate = row.original.tax_rate
            const priceWithTax = price * (1 + taxRate.rate / 100)
            
            return h('div', { class: 'text-sm' }, [
                h('div', { class: 'font-medium' }, 
                    new Intl.NumberFormat('pt-PT', {
                        style: 'currency',
                        currency: 'EUR',
                    }).format(priceWithTax)
                ),
                h('div', { class: 'text-xs text-muted-foreground' }, 
                    `s/ IVA: ${new Intl.NumberFormat('pt-PT', {
                        style: 'currency',
                        currency: 'EUR',
                    }).format(price)}`
                ),
            ])
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const article = row.original

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
                                                router.get(`/articles/${article.id}`)
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
                                                router.get(`/articles/${article.id}/edit`)
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
                                                if (confirm(`Eliminar artigo "${article.name}"?`)) {
                                                    router.delete(`/articles/${article.id}`, {
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



