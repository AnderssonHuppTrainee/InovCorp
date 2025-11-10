<template>
    <ShowWrapper
        :title="`Encomenda ${order.number}`"
        :description="`Cliente: ${order.client?.name}`"
        :edit-url="`/orders/${order.id}/edit`"
        :delete-url="`/orders/${order.id}`"
        :back-url="'/orders'"
        :item-name="`Encomenda: #${order.number}`"
    >
        <template #button>
            <Button variant="outline" @click="downloadPdf">
                <FileTextIcon class="mr-2 h-4 w-4" />
                PDF
            </Button>

            <Button
                v-if="order.status === 'closed' && hasSuppliers"
                @click="handleConvertToSupplierOrders"
            >
                <PackageIcon class="mr-2 h-4 w-4" />
                Enc. Fornecedores
            </Button>
        </template>

        <template #main-content>
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Detalhes da Encomenda</CardTitle>
                            <Badge
                                :variant="
                                    order.status === 'closed'
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    order.status === 'draft'
                                        ? 'Rascunho'
                                        : 'Fechado'
                                }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-muted-foreground">
                                Número
                            </div>
                            <div class="col-span-2 font-medium">
                                {{ order.number }}
                            </div>
                        </div>
                        <Separator />
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-muted-foreground">
                                Data
                            </div>
                            <div class="col-span-2">
                                {{ formatDate(order.order_date) }}
                            </div>
                        </div>
                        <Separator />
                        <div
                            class="grid grid-cols-3 gap-4"
                            v-if="order.delivery_date"
                        >
                            <div class="text-sm text-muted-foreground">
                                Entrega
                            </div>
                            <div class="col-span-2">
                                {{ formatDate(order.delivery_date) }}
                            </div>
                        </div>
                        <Separator v-if="order.delivery_date" />
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-muted-foreground">
                                Cliente
                            </div>
                            <div class="col-span-2">
                                <a
                                    v-if="order.client"
                                    :href="`/entities/${order.client.id}`"
                                    class="text-primary hover:underline"
                                >
                                    {{ order.client.name }}
                                </a>
                            </div>
                        </div>
                        <Separator />
                        <div
                            class="grid grid-cols-3 gap-4"
                            v-if="order.proposal"
                        >
                            <div class="text-sm text-muted-foreground">
                                Proposta
                            </div>
                            <div class="col-span-2">
                                <a
                                    :href="`/proposals/${order.proposal.id}`"
                                    class="text-primary hover:underline"
                                >
                                    Proposta {{ order.proposal.number }}
                                </a>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Artigos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="rounded-lg border p-4"
                            >
                                <div
                                    class="mb-2 flex items-start justify-between"
                                >
                                    <div>
                                        <div class="font-medium">
                                            {{ item.article?.reference }} -
                                            {{ item.article?.name }}
                                        </div>
                                        <div
                                            v-if="item.supplier"
                                            class="text-sm text-muted-foreground"
                                        >
                                            Fornecedor: {{ item.supplier.name }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium">
                                            {{
                                                formatCurrency(
                                                    item.quantity *
                                                        item.unit_price,
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-muted-foreground"
                                            >Qtd:</span
                                        >
                                        {{ item.quantity }}
                                    </div>
                                    <div>
                                        <span class="text-muted-foreground"
                                            >Preço:</span
                                        >
                                        {{ formatCurrency(item.unit_price) }}
                                    </div>
                                </div>

                                <div
                                    v-if="item.notes"
                                    class="mt-2 text-sm text-muted-foreground"
                                >
                                    {{ item.notes }}
                                </div>
                            </div>
                        </div>

                        <Separator class="my-4" />
                        <div class="text-right">
                            <div class="text-lg">
                                <span class="text-muted-foreground"
                                    >Total:</span
                                >
                                <span class="ml-4 text-2xl font-bold">{{
                                    formatCurrency(order.total_amount)
                                }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Encomendas de Fornecedores Geradas -->
                <Card
                    v-if="
                        order.supplier_orders &&
                        order.supplier_orders.length > 0
                    "
                >
                    <CardHeader>
                        <CardTitle
                            >Encomendas de Fornecedores Geradas</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div
                                v-for="supplierOrder in order.supplier_orders"
                                :key="supplierOrder.id"
                                class="flex items-center justify-between rounded-lg border p-3"
                            >
                                <div>
                                    <div class="font-medium">
                                        {{ supplierOrder.supplier.name }}
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        Nº {{ supplierOrder.number }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">
                                        {{
                                            formatCurrency(
                                                supplierOrder.total_amount,
                                            )
                                        }}
                                    </div>
                                    <Badge
                                        variant="secondary"
                                        class="text-xs"
                                        >{{ supplierOrder.status }}</Badge
                                    >
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </template>
        <template #sidebar>
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Informações</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-muted-foreground">
                                Estado
                            </div>
                            <Badge
                                :variant="
                                    order.status === 'closed'
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    order.status === 'draft'
                                        ? 'Rascunho'
                                        : 'Fechado'
                                }}
                            </Badge>
                        </div>
                        <Separator />
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-muted-foreground">
                                Total de Itens
                            </div>
                            <div class="font-medium">
                                {{ order.items?.length || 0 }}
                            </div>
                        </div>
                        <Separator />
                        <div class="space-y-1">
                            <div class="text-sm text-muted-foreground">
                                Criado em
                            </div>
                            <div class="text-sm">
                                {{ formatDateTime(order.created_at) }}
                            </div>
                        </div>
                        <Separator />
                        <div class="space-y-1">
                            <div class="text-sm text-muted-foreground">
                                Atualizado
                            </div>
                            <div class="text-sm">
                                {{ formatDateTime(order.updated_at) }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Ações</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <Button
                            variant="outline"
                            class="w-full justify-start"
                            @click="downloadPdf"
                        >
                            <FileTextIcon class="mr-2 h-4 w-4" />
                            Download PDF
                        </Button>
                        <Button
                            v-if="order.status === 'closed' && hasSuppliers"
                            variant="outline"
                            class="w-full justify-start"
                            @click="handleConvertToSupplierOrders"
                        >
                            <PackageIcon class="mr-2 h-4 w-4" />
                            Converter em Enc. Fornecedores
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </template>
    </ShowWrapper>
</template>

<script setup lang="ts">
import ShowWrapper from '@/components/common/ShowWrapper.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import type { Order } from '@/types';
import { router } from '@inertiajs/vue3';
import { FileTextIcon, PackageIcon } from 'lucide-vue-next';
import { computed } from 'vue';

interface SupplierOrder {
    id: number;
    number: string;
    order_date: string;
    supplier: {
        id: number;
        name: string;
    };
    order?: {
        id: number;
        number: string;
    };
    total_amount: number;
    status: 'draft' | 'closed';
    created_at: string;
}

interface Props {
    order: Order & {
        supplier_orders?: SupplierOrder[];
    };
}

const props = defineProps<Props>();

const hasSuppliers = computed(() => {
    return props.order.items.some((item: any) => item.supplier_id);
});

const handleConvertToSupplierOrders = () => {
    if (
        confirm(
            `Converter para Encomendas de Fornecedores?\n\nSerá criada uma encomenda para cada fornecedor.`,
        )
    ) {
        router.post(
            `/orders/${props.order.id}/convert-to-supplier-orders`,
            {},
            { preserveScroll: true },
        );
    }
};

const downloadPdf = () =>
    window.open(`/orders/${props.order.id}/pdf`, '_blank');

const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatDateTime = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>
