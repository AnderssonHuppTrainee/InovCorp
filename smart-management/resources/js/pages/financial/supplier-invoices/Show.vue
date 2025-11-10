<template>
    <ShowWrapper
        :title="`Fatura #${invoice.number}`"
        :description="`${invoice.supplier.name} - ${formatCurrency(invoice.total_amount)}`"
        :edit-url="`/supplier-invoices/${invoice.id}/edit`"
        :delete-url="`/supplier-invoices/${invoice.id}`"
        :back-url="'/supplier-invoices'"
        :item-name="`Fatura #${invoice.number}`"
    >
        <template #button>
            <Button
                v-if="invoice.status === 'paid' && hasPaymentProof"
                @click="handleSendEmail"
            >
                <MailIcon class="mr-2 h-4 w-4" />
                Enviar Comprovativo
            </Button>
        </template>
        <template #main-content>
            <Card>
                <CardHeader>
                    <CardTitle>Detalhes da Fatura</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Número</div>
                        <div class="col-span-2 font-mono font-medium">
                            {{ invoice.number }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Fornecedor
                        </div>
                        <div class="col-span-2 font-medium">
                            {{ invoice.supplier.name }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Data da Fatura
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(invoice.invoice_date) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Data de Vencimento
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(invoice.due_date) }}
                        </div>
                    </div>

                    <Separator />
                    <div
                        class="grid grid-cols-3 gap-4"
                        v-if="invoice.supplier_order"
                    >
                        <div class="text-sm text-muted-foreground">
                            Encomenda
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ invoice.supplier_order.number }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Estado</div>
                        <div class="col-span-2">
                            <Badge
                                :variant="
                                    invoice.status === 'paid'
                                        ? 'default'
                                        : invoice.status === 'overdue'
                                          ? 'destructive'
                                          : invoice.status === 'sent'
                                            ? 'default'
                                            : 'secondary'
                                "
                            >
                                {{
                                    invoice.status === 'draft'
                                        ? 'Rascunho'
                                        : invoice.status === 'sent'
                                          ? 'Enviada'
                                          : invoice.status === 'paid'
                                            ? 'Paga'
                                            : 'Vencida'
                                }}
                            </Badge>
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Valor Total
                        </div>
                        <div class="col-span-2 text-lg font-bold">
                            {{ formatCurrency(invoice.total_amount) }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
        <template #sidebar>
            <Card>
                <CardHeader>
                    <CardTitle>Documentos</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <Button
                        v-if="hasDocument"
                        variant="outline"
                        class="w-full justify-start"
                        @click="downloadDocument"
                    >
                        <FileTextIcon class="mr-2 h-4 w-4" />
                        Download Documento
                    </Button>
                    <p v-else class="text-sm text-muted-foreground">
                        Sem documento
                    </p>

                    <Button
                        v-if="hasPaymentProof"
                        variant="outline"
                        class="w-full justify-start"
                        @click="downloadPaymentProof"
                    >
                        <DownloadIcon class="mr-2 h-4 w-4" />
                        Download Comprovativo
                    </Button>
                    <p v-else class="text-sm text-muted-foreground">
                        Sem comprovativo
                    </p>

                    <Button
                        v-if="invoice.status === 'paid' && hasPaymentProof"
                        class="w-full justify-start"
                        @click="handleSendEmail"
                    >
                        <MailIcon class="mr-2 h-4 w-4" />
                        Enviar Comprovativo
                    </Button>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle>Informações</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Criado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(invoice.created_at) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Atualizado em
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ formatDate(invoice.updated_at) }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
    </ShowWrapper>
</template>

<script setup lang="ts">
import ShowWrapper from '@/components/common/ShowWrapper.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { router } from '@inertiajs/vue3';
import { DownloadIcon, FileTextIcon, MailIcon } from 'lucide-vue-next';

interface Props {
    invoice: {
        id: number;
        number: string;
        invoice_date: string;
        due_date: string;
        total_amount: number;
        status: string;
        created_at: string;
        updated_at: string;
        supplier: {
            name: string;
        };
        supplier_order?: {
            number: string;
        };
    };
    hasDocument: boolean;
    hasPaymentProof: boolean;
}

const props = defineProps<Props>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};

const handleSendEmail = () => {
    if (
        confirm(
            `Enviar comprovativo ao fornecedor ${props.invoice.supplier.name}?`,
        )
    ) {
        router.post(
            `/supplier-invoices/${props.invoice.id}/send-payment-proof`,
            {},
            {
                preserveScroll: true,
            },
        );
    }
};

const downloadDocument = () => {
    window.open(
        `/supplier-invoices/${props.invoice.id}/download-document`,
        '_blank',
    );
};

const downloadPaymentProof = () => {
    window.open(
        `/supplier-invoices/${props.invoice.id}/download-payment-proof`,
        '_blank',
    );
};
</script>
