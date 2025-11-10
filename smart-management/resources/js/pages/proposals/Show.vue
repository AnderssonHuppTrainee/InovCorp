<template>
    <ShowWrapper
        :title="`Proposta: #${proposal.number}`"
        :description="`Cliente: ${proposal.client?.name}`"
        :edit-url="`/proposals/${proposal.id}/edit`"
        :delete-url="`/proposals/${proposal.id}`"
        :back-url="'/proposals'"
        :item-name="`Proposta: #${proposal.number}`"
    >
        <template #button>
            <Button variant="outline" @click="downloadPdf">
                <FileTextIcon class="mr-2 h-4 w-4" />
                Download PDF
            </Button>

            <Button
                v-if="proposal.status === 'closed'"
                @click="handleConvertToOrder"
            >
                <ShoppingCartIcon class="mr-2 h-4 w-4" />
                Converter em Encomenda
            </Button>
        </template>
        <template #main-content>
            <Card>
                <CardHeader>
                    <CardTitle>Detalhes da Proposta</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Número</div>
                        <div class="col-span-2 font-mono font-medium">
                            {{ proposal.number }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Data da Proposta
                        </div>
                        <div class="col-span-2">
                            {{ formatDate(proposal.proposal_date) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Validade
                        </div>
                        <div class="col-span-2">
                            {{ formatDate(proposal.validity_date) }}
                        </div>
                    </div>
                    <Separator />

                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Cliente</div>
                        <div class="col-span-2 font-medium">
                            {{ proposal.client?.name }}
                        </div>
                    </div>

                    <Separator />
                    <div
                        class="grid grid-cols-3 gap-4"
                        v-if="proposal.total_amount"
                    >
                        <div class="text-sm text-muted-foreground">
                            Valor Total
                        </div>
                        <div class="col-span-2 text-sm font-medium">
                            {{ formatCurrency(proposal.total_amount) }}
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
                            v-for="(item, index) in proposal.items"
                            :key="item.id"
                            class="rounded-lg border p-4"
                        >
                            <div class="mb-2 flex items-start justify-between">
                                <div>
                                    <div class="font-medium">
                                        {{ item.article?.reference }} -
                                        {{ item.article?.name }}
                                    </div>
                                    <div
                                        v-if="item.supplier"
                                        class="text-sm text-muted-foreground"
                                    >
                                        Fornecedor:
                                        {{ item.supplier.name }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">
                                        {{
                                            formatCurrency(
                                                item.quantity * item.unit_price,
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4 text-sm">
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
                                <div v-if="item.cost_price">
                                    <span class="text-muted-foreground"
                                        >Custo:</span
                                    >
                                    {{ formatCurrency(item.cost_price) }}
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

                    <!-- Total -->
                    <Separator class="my-4" />
                    <div class="text-right">
                        <div class="text-lg">
                            <span class="text-muted-foreground">Total:</span>
                            <span class="ml-4 text-2xl font-bold">{{
                                formatCurrency(proposal.total_amount)
                            }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
        <template #sidebar>
            <Card>
                <CardHeader>
                    <CardTitle>Informações</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Estado</div>
                        <div class="col-span-2">
                            <Badge
                                :variant="
                                    proposal.status === 'closed'
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    proposal.status === 'draft'
                                        ? 'Rascunho'
                                        : 'Fechado'
                                }}
                            </Badge>
                        </div>
                    </div>
                    <Separator />
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-muted-foreground">
                            Total de Itens
                        </div>
                        <div class="font-medium">
                            {{ proposal.items?.length || 0 }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Criado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(proposal.created_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Atualizado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(proposal.updated_at) }}
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
                        v-if="proposal.status === 'closed'"
                        variant="outline"
                        class="w-full justify-start"
                        @click="handleConvertToOrder"
                    >
                        <ShoppingCartIcon class="mr-2 h-4 w-4" />
                        Converter em Encomenda
                    </Button>
                </CardContent>
            </Card>
        </template>
    </ShowWrapper>
</template>

<script setup lang="ts">
import ShowWrapper from '@/components/common/ShowWrapper.vue';
import { Badge } from '@/components/ui/badge';
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import type { Proposal } from '@/types';
import { router } from '@inertiajs/vue3';
import { FileTextIcon, ShoppingCartIcon } from 'lucide-vue-next';

interface Props {
    proposal: Proposal;
}
const props = defineProps<Props>();

const handleConvertToOrder = () => {
    if (
        confirm(
            `Converter a proposta "${props.proposal.number}" em encomenda?\n\nSerá criada uma encomenda em estado de rascunho.`,
        )
    ) {
        router.post(
            `/proposals/${props.proposal.id}/convert-to-order`,
            {},
            {
                preserveScroll: true,
            },
        );
    }
};

const downloadPdf = () => {
    window.open(`/proposals/${props.proposal.id}/pdf`, '_blank');
};
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT', {
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
