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
    Mail,
    FileText,
} from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

export interface SupplierInvoice {
    id: number
    number: string
    invoice_date: string
    due_date: string
    supplier: {
        id: number
        name: string
    }
    supplier_order: {
        id: number
        number: string
    } | null
    total_amount: number
    document_path: string | null
    payment_proof_path: string | null
    status: 'pending_payment' | 'paid'
}

export const columns: ColumnDef<SupplierInvoice>[] = [
    {
        accessorKey: 'invoice_date',
        header: 'Data',
        cell: ({ row }) => {
            const date = new Date(row.getValue('invoice_date'))
            return h('div', {}, date.toLocaleDateString('pt-PT'))
        },
    },
    {
        accessorKey: 'number',
        header: 'Número',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.getValue('number'))
        },
    },
    {
        accessorKey: 'supplier',
        header: 'Fornecedor',
        cell: ({ row }) => {
            const supplier = row.getValue('supplier') as SupplierInvoice['supplier']
            return h('div', {}, supplier?.name || '-')
        },
    },
    {
        accessorKey: 'supplier_order',
        header: 'Encomenda',
        cell: ({ row }) => {
            const order = row.getValue('supplier_order') as SupplierInvoice['supplier_order']
            return h('div', {}, order?.number || '-')
        },
    },
    {
        accessorKey: 'document_path',
        header: 'Documento',
        cell: ({ row }) => {
            const hasDoc = row.getValue('document_path')
            if (!hasDoc) return h('div', { class: 'text-muted-foreground' }, '-')
            
            return h(
                Button,
                {
                    variant: 'ghost',
                    size: 'sm',
                    onClick: () => {
                        window.open(
                            `/supplier-invoices/${row.original.id}/download-document`,
                            '_blank'
                        )
                    },
                },
                {
                    default: () => [h(FileText, { class: 'h-4 w-4' })],
                }
            )
        },
    },
    {
        accessorKey: 'total_amount',
        header: 'Valor Total',
        cell: ({ row }) => {
            const amount = parseFloat(row.getValue('total_amount'))
            const formatted = new Intl.NumberFormat('pt-PT', {
                style: 'currency',
                currency: 'EUR',
            }).format(amount)
            return h('div', { class: 'font-medium' }, formatted)
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
                    variant: status === 'paid' ? 'default' : 'secondary',
                },
                {
                    default: () =>
                        status === 'pending_payment' ? 'Pendente' : 'Paga',
                }
            )
        },
    },
    {
        id: 'actions',
        cell: ({ row }) => {
            const invoice = row.original

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
                                                router.get(`/supplier-invoices/${invoice.id}`)
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
                                                router.get(`/supplier-invoices/${invoice.id}/edit`)
                                            },
                                        },
                                        {
                                            default: () => [
                                                h(Pencil, { class: 'mr-2 h-4 w-4' }),
                                                'Editar',
                                            ],
                                        }
                                    ),
                                    invoice.document_path
                                        ? h(
                                              DropdownMenuItem,
                                              {
                                                  onClick: () => {
                                                      window.open(
                                                          `/supplier-invoices/${invoice.id}/download-document`,
                                                          '_blank'
                                                      )
                                                  },
                                              },
                                              {
                                                  default: () => [
                                                      h(Download, { class: 'mr-2 h-4 w-4' }),
                                                      'Download Documento',
                                                  ],
                                              }
                                          )
                                        : null,
                                    invoice.payment_proof_path
                                        ? h(
                                              DropdownMenuItem,
                                              {
                                                  onClick: () => {
                                                      window.open(
                                                          `/supplier-invoices/${invoice.id}/download-payment-proof`,
                                                          '_blank'
                                                      )
                                                  },
                                              },
                                              {
                                                  default: () => [
                                                      h(Download, { class: 'mr-2 h-4 w-4' }),
                                                      'Download Comprovativo',
                                                  ],
                                              }
                                          )
                                        : null,
                                    invoice.status === 'paid' && invoice.payment_proof_path
                                        ? h(DropdownMenuSeparator)
                                        : null,
                                    invoice.status === 'paid' && invoice.payment_proof_path
                                        ? h(
                                              DropdownMenuItem,
                                              {
                                                  onClick: () => {
                                                      if (
                                                          confirm(
                                                              `Enviar comprovativo ao fornecedor ${invoice.supplier.name}?`
                                                          )
                                                      ) {
                                                          router.post(
                                                              `/supplier-invoices/${invoice.id}/send-payment-proof`,
                                                              {},
                                                              { preserveScroll: true }
                                                          )
                                                      }
                                                  },
                                              },
                                              {
                                                  default: () => [
                                                      h(Mail, { class: 'mr-2 h-4 w-4' }),
                                                      'Enviar Comprovativo',
                                                  ],
                                              }
                                          )
                                        : null,
                                    h(DropdownMenuSeparator),
                                    h(
                                        DropdownMenuItem,
                                        {
                                            class: 'text-destructive focus:text-destructive',
                                            onClick: () => {
                                                if (
                                                    confirm(
                                                        `Eliminar fatura "${invoice.number}"?`
                                                    )
                                                ) {
                                                    router.delete(`/supplier-invoices/${invoice.id}`, {
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
                                ].filter(Boolean),
                            }
                        ),
                    ],
                }
            )
        },
    },
]




