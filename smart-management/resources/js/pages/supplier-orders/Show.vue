<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Detalhes da Encomenda de Fornecedor"
                :description="`Encomenda #${supplierOrder.number}`"
            >
                <div class="flex gap-2">
                    <Button variant="outline" @click="handleBack">
                        <ArrowLeftIcon class="mr-2 h-4 w-4" />
                        Voltar
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2Icon class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 md:grid-cols-3">
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Informações da Encomenda</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-muted-foreground">Número</div>
                                <div class="mt-1 font-mono text-sm font-semibold">
                                    {{ supplierOrder.number }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-muted-foreground">
                                    Data da Encomenda
                                </div>
                                <div class="mt-1 text-sm">{{ formatDate(supplierOrder.order_date) }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-muted-foreground">Fornecedor</div>
                                <div class="mt-1 text-sm">{{ supplierOrder.supplier?.name }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-muted-foreground">
                                    Encomenda Cliente
                                </div>
                                <div class="mt-1 font-mono text-sm">
                                    {{ supplierOrder.order?.number || '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-muted-foreground">Valor Total</div>
                                <div class="mt-1 text-lg font-bold">
                                    €{{ (supplierOrder.total_amount || 0).toFixed(2) }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-muted-foreground">Estado</div>
                                <div class="mt-1">
                                    <Badge :variant="getStatusVariant(supplierOrder.status)">
                                        {{ getStatusLabel(supplierOrder.status) }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Informações Adicionais</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <div class="text-sm font-medium text-muted-foreground">
                                Faturas Associadas
                            </div>
                            <div class="mt-1 text-2xl font-bold">
                                {{ supplierOrder.invoices?.length || 0 }}
                            </div>
                        </div>

                        <div class="pt-4">
                            <div class="text-sm font-medium text-muted-foreground">Criada em</div>
                            <div class="mt-1 text-sm">
                                {{ formatDate(supplierOrder.created_at) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-muted-foreground">
                                Atualizada em
                            </div>
                            <div class="mt-1 text-sm">
                                {{ formatDate(supplierOrder.updated_at) }}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Artigos da Encomenda Original -->
            <Card v-if="supplierOrder.order?.items">
                <CardHeader>
                    <CardTitle>Artigos da Encomenda</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b">
                                <tr class="text-sm text-muted-foreground">
                                    <th class="pb-3 text-left font-medium">Referência</th>
                                    <th class="pb-3 text-left font-medium">Artigo</th>
                                    <th class="pb-3 text-right font-medium">Quantidade</th>
                                    <th class="pb-3 text-right font-medium">Preço Unit.</th>
                                    <th class="pb-3 text-right font-medium">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in supplierOrder.order.items"
                                    :key="item.id"
                                    class="border-b last:border-0"
                                >
                                    <td class="py-3 font-mono text-sm">
                                        {{ item.article?.reference }}
                                    </td>
                                    <td class="py-3 text-sm">{{ item.article?.name }}</td>
                                    <td class="py-3 text-right text-sm">{{ item.quantity }}</td>
                                    <td class="py-3 text-right text-sm">
                                        €{{ item.unit_price.toFixed(2) }}
                                    </td>
                                    <td class="py-3 text-right font-semibold">
                                        €{{ (item.quantity * item.unit_price).toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import PageHeader from '@/components/PageHeader.vue'
import { ArrowLeftIcon, Trash2Icon } from 'lucide-vue-next'
import supplierOrders from '@/routes/supplier-orders'

interface Props {
    supplierOrder: {
        id: number
        number: string
        order_date: string
        supplier: {
            id: number
            name: string
        }
        order?: {
            id: number
            number: string
            items?: Array<{
                id: number
                article: {
                    reference: string
                    name: string
                }
                quantity: number
                unit_price: number
            }>
        }
        total_amount: number
        status: string
        invoices?: Array<any>
        created_at: string
        updated_at: string
    }
}

const props = defineProps<Props>()

const statusMap: Record<string, { label: string; variant: any }> = {
    draft: { label: 'Rascunho', variant: 'secondary' },
    closed: { label: 'Fechada', variant: 'default' },
}

const getStatusLabel = (status: string) => {
    return statusMap[status]?.label || status
}

const getStatusVariant = (status: string) => {
    return statusMap[status]?.variant || 'secondary'
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const handleBack = () => {
    router.visit(supplierOrders.index().url)
}

const handleDelete = () => {
    if (confirm('Tem certeza que deseja eliminar esta encomenda de fornecedor?')) {
        router.delete(supplierOrders.destroy({ supplierOrder: props.supplierOrder.id }).url)
    }
}
</script>

