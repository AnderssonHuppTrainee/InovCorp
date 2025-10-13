<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="`Fatura ${invoice.number}`"
                :description="`Fornecedor: ${invoice.supplier?.name}`"
            >
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack">
                        <ArrowLeftIcon class="mr-2 h-4 w-4" />
                        Voltar
                    </Button>
                    <Button @click="handleEdit">
                        <PencilIcon class="mr-2 h-4 w-4" />
                        Editar
                    </Button>
                    <Button
                        v-if="invoice.status === 'paid' && hasPaymentProof"
                        @click="handleSendEmail"
                    >
                        <MailIcon class="mr-2 h-4 w-4" />
                        Enviar Comprovativo
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2Icon class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Detalhes da Fatura</CardTitle>
                                <Badge :variant="invoice.status === 'paid' ? 'default' : 'secondary'">
                                    {{ invoice.status === 'pending_payment' ? 'Pendente de Pagamento' : 'Paga' }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Número</div>
                                <div class="col-span-2 font-medium">{{ invoice.number }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Data</div>
                                <div class="col-span-2">{{ formatDate(invoice.invoice_date) }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Vencimento</div>
                                <div class="col-span-2">{{ formatDate(invoice.due_date) }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Fornecedor</div>
                                <div class="col-span-2">
                                    <a :href="`/entities/${invoice.supplier.id}`" class="text-primary hover:underline">
                                        {{ invoice.supplier.name }}
                                    </a>
                                </div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4" v-if="invoice.supplier_order">
                                <div class="text-sm text-muted-foreground">Encomenda</div>
                                <div class="col-span-2">{{ invoice.supplier_order.number }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Valor Total</div>
                                <div class="col-span-2 font-bold text-lg">{{ formatCurrency(invoice.total_amount) }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Documentos</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2">
                            <Button v-if="hasDocument" variant="outline" class="w-full justify-start" @click="downloadDocument">
                                <FileTextIcon class="mr-2 h-4 w-4" />
                                Download Documento
                            </Button>
                            <p v-else class="text-sm text-muted-foreground">Sem documento</p>

                            <Button v-if="hasPaymentProof" variant="outline" class="w-full justify-start" @click="downloadPaymentProof">
                                <DownloadIcon class="mr-2 h-4 w-4" />
                                Download Comprovativo
                            </Button>
                            <p v-else class="text-sm text-muted-foreground">Sem comprovativo</p>

                            <Button v-if="invoice.status === 'paid' && hasPaymentProof" class="w-full justify-start" @click="handleSendEmail">
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
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Criado em</div>
                                <div class="text-sm">{{ formatDateTime(invoice.created_at) }}</div>
                            </div>
                            <Separator />
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Atualizado</div>
                                <div class="text-sm">{{ formatDateTime(invoice.updated_at) }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, DownloadIcon, FileTextIcon, MailIcon, PencilIcon, Trash2Icon } from 'lucide-vue-next';

interface Props {
    invoice: any;
    hasDocument: boolean;
    hasPaymentProof: boolean;
}

const props = defineProps<Props>();

const goBack = () => router.get('/supplier-invoices');
const handleEdit = () => router.get(`/supplier-invoices/${props.invoice.id}/edit`);

const handleDelete = () => {
    if (confirm(`Eliminar fatura "${props.invoice.number}"?`)) {
        router.delete(`/supplier-invoices/${props.invoice.id}`, {
            onSuccess: () => router.get('/supplier-invoices'),
        });
    }
};

const handleSendEmail = () => {
    if (confirm(`Enviar comprovativo ao fornecedor ${props.invoice.supplier.name}?`)) {
        router.post(`/supplier-invoices/${props.invoice.id}/send-payment-proof`, {}, {
            preserveScroll: true,
        });
    }
};

const downloadDocument = () => {
    window.open(`/supplier-invoices/${props.invoice.id}/download-document`, '_blank');
};

const downloadPaymentProof = () => {
    window.open(`/supplier-invoices/${props.invoice.id}/download-payment-proof`, '_blank');
};

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





