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
    Download,
    FileText,
    FileImage,
    File,
} from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

export interface DigitalArchive {
    id: number
    name: string
    file_name: string
    file_path: string
    mime_type: string
    file_size: number
    description: string | null
    document_type: string
    archivable_type: string | null
    archivable_id: number | null
    uploaded_by: number
    is_public: boolean
    expires_at: string | null
    created_at: string
    uploaded_by_user: {
        id: number
        name: string
    }
}

const getFileIcon = (mimeType: string) => {
    if (mimeType.startsWith('image/')) return FileImage
    if (mimeType.includes('pdf')) return FileText
    return File
}

export const columns: ColumnDef<DigitalArchive>[] = [
    {
        accessorKey: 'name',
        header: 'Nome',
        cell: ({ row }) => {
            const Icon = getFileIcon(row.original.mime_type)
            return h('div', { class: 'flex items-center gap-2' }, [
                h(Icon, { class: 'h-4 w-4 text-muted-foreground' }),
                h('span', { class: 'font-medium' }, row.getValue('name')),
            ])
        },
    },
    {
        accessorKey: 'file_name',
        header: 'Ficheiro',
        cell: ({ row }) => {
            return h('div', { class: 'font-mono text-sm' }, row.getValue('file_name'))
        },
    },
    {
        accessorKey: 'document_type',
        header: 'Tipo',
        cell: ({ row }) => {
            return h(Badge, { variant: 'outline' }, {
                default: () => row.getValue('document_type'),
            })
        },
    },
    {
        accessorKey: 'file_size',
        header: 'Tamanho',
        cell: ({ row }) => {
            const size = row.getValue('file_size') as number
            const units = ['B', 'KB', 'MB', 'GB']
            let bytes = size
            let i = 0
            
            while (bytes > 1024 && i < units.length - 1) {
                bytes /= 1024
                i++
            }
            
            return h('div', { class: 'text-sm' }, `${bytes.toFixed(2)} ${units[i]}`)
        },
    },
    {
        accessorKey: 'uploaded_by_user',
        header: 'Enviado por',
        cell: ({ row }) => {
            const user = row.getValue('uploaded_by_user') as DigitalArchive['uploaded_by_user']
            return h('div', { class: 'text-sm' }, user?.name || '-')
        },
    },
    {
        accessorKey: 'is_public',
        header: 'Visibilidade',
        cell: ({ row }) => {
            const isPublic = row.getValue('is_public') as boolean
            return h(
                Badge,
                {
                    variant: isPublic ? 'default' : 'secondary',
                },
                {
                    default: () => (isPublic ? 'Público' : 'Privado'),
                }
            )
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Data',
        cell: ({ row }) => {
            const date = new Date(row.getValue('created_at'))
            return h('div', { class: 'text-sm' }, date.toLocaleDateString('pt-PT'))
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const archive = row.original

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
                                                router.get(`/digital-archive/${archive.id}`)
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
                                                window.open(
                                                    `/digital-archive/${archive.id}/download`,
                                                    '_blank'
                                                )
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(Download, { class: 'mr-2 h-4 w-4' }),
                                                'Download',
                                            ],
                                        }
                                    ),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            onClick: () => {
                                                router.get(`/digital-archive/${archive.id}/edit`)
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
                                                        `Eliminar ficheiro "${archive.name}"?`
                                                    )
                                                ) {
                                                    router.delete(`/digital-archive/${archive.id}`, {
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

















