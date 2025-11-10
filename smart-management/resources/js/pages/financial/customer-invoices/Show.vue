<template>
    <ShowWrapper
        :title="`Fatura #${invoice.number}`"
        :description="`${invoice.customer.name} - ${formatCurrency(invoice.total_amount)}`"
        :edit-url="`/customer-invoices/${invoice.id}/edit`"
        :delete-url="`/customer-invoices/${invoice.id}`"
        :back-url="'/customer-invoices'"
        :item-name="`Fatura #${invoice.number}`"
    >
        <template #button>
            <Button v-if="invoice.balance > 0" @click="openPaymentDialog">
                <CreditCardIcon class="mr-2 h-4 w-4" />
                Registar Pagamento
            </Button>
        </template>
        <template #main-content>
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Detalhes da Fatura</CardTitle>
                        <Badge :variant="getStatusVariant(invoice.status)">
                            {{ getStatusLabel(invoice.status) }}
                        </Badge>
                    </div>
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
                        <div class="text-sm text-muted-foreground">Cliente</div>
                        <div class="col-span-2 font-medium">
                            {{ invoice.customer.name }}
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
                    <Separator v-if="invoice.order" />
                    <div class="grid grid-cols-3 gap-4" v-if="invoice.order">
                        <div class="text-sm text-muted-foreground">
                            Encomenda
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ invoice.order.number }}
                        </div>
                    </div>
                    <Separator v-if="invoice.notes" />
                    <div class="grid grid-cols-3 gap-4" v-if="invoice.notes">
                        <div class="text-sm text-muted-foreground">
                            Observações
                        </div>
                        <div class="col-span-2 text-sm">
                            {{ invoice.notes }}
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
                    <div class="space-y-1">
                        <div class="text-sm text-muted-foreground">
                            Valor Total
                        </div>
                        <div class="col-span-2 text-lg font-bold">
                            {{ formatCurrency(invoice.total_amount) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="space-y-1">
                        <div class="text-sm text-muted-foreground">
                            Valor Pago
                        </div>
                        <div class="text-xl font-medium text-green-600">
                            {{ formatCurrency(invoice.paid_amount) }}
                        </div>
                    </div>
                    <Separator />
                    <div class="space-y-1">
                        <div class="text-sm text-muted-foreground">Saldo</div>
                        <div
                            class="text-2xl font-bold"
                            :class="
                                invoice.balance > 0
                                    ? 'text-destructive'
                                    : 'text-green-600'
                            "
                        >
                            {{ formatCurrency(invoice.balance) }}
                        </div>
                    </div>
                    <Separator />
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

        <!-- Dialog de Pagamento -->
        <Dialog v-model:open="showPaymentDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Registar Pagamento</DialogTitle>
                    <DialogDescription>
                        Registar pagamento para a fatura {{ invoice.number }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitPayment">
                    <div class="space-y-4 py-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium"
                                >Valor do Pagamento *</label
                            >
                            <Input
                                type="number"
                                step="0.01"
                                :max="invoice.balance"
                                v-model.number="paymentData.amount"
                                required
                            />
                            <p class="text-sm text-muted-foreground">
                                Máximo: {{ formatCurrency(invoice.balance) }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium"
                                >Data do Pagamento *</label
                            >
                            <DatePicker v-model="paymentData.payment_date" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium"
                                >Observações</label
                            >
                            <Textarea
                                v-model="paymentData.notes"
                                placeholder="Notas sobre o pagamento..."
                                rows="3"
                            />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="showPaymentDialog = false"
                            >Cancelar</Button
                        >
                        <Button type="submit" :disabled="isSubmittingPayment">
                            <SaveIcon
                                v-if="!isSubmittingPayment"
                                class="mr-2 h-4 w-4"
                            />
                            <LoaderIcon
                                v-else
                                class="mr-2 h-4 w-4 animate-spin"
                            />
                            {{
                                isSubmittingPayment
                                    ? 'A registar...'
                                    : 'Registar Pagamento'
                            }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </ShowWrapper>
</template>

<script setup lang="ts">
import ShowWrapper from '@/components/common/ShowWrapper.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import { router } from '@inertiajs/vue3';
import { CreditCardIcon } from 'lucide-vue-next';
import { reactive, ref } from 'vue';
interface Props {
    invoice: {
        id: number;
        number: string;
        invoice_date: string;
        due_date: string;
        total_amount: number;
        paid_amount: number;
        balance: number;
        notes: string;
        status: string;
        created_at: string;
        updated_at: string;
        customer: {
            name: string;
        };
        order?: {
            number: string;
        };
    };
}

const props = defineProps<Props>();

const showPaymentDialog = ref(false);
const isSubmittingPayment = ref(false);

const paymentData = reactive({
    amount: 0,
    payment_date: new Date().toISOString().split('T')[0],
    notes: '',
});

const openPaymentDialog = () => {
    paymentData.amount = props.invoice.balance;
    showPaymentDialog.value = true;
};

const submitPayment = () => {
    isSubmittingPayment.value = true;
    router.post(
        `/customer-invoices/${props.invoice.id}/register-payment`,
        paymentData,
        {
            preserveScroll: true,
            onSuccess: () => {
                showPaymentDialog.value = false;
                paymentData.amount = 0;
                paymentData.notes = '';
            },
            onFinish: () => (isSubmittingPayment.value = false),
        },
    );
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        draft: 'Rascunho',
        sent: 'Enviada',
        partially_paid: 'Parcialmente Paga',
        paid: 'Paga',
        overdue: 'Vencida',
        cancelled: 'Cancelada',
    };
    return labels[status] || status;
};

const getStatusVariant = (
    status: string,
): 'default' | 'secondary' | 'destructive' | 'outline' => {
    const variants: Record<
        string,
        'default' | 'secondary' | 'destructive' | 'outline'
    > = {
        draft: 'secondary',
        sent: 'outline',
        partially_paid: 'default',
        paid: 'default',
        overdue: 'destructive',
        cancelled: 'secondary',
    };
    return variants[status] || 'secondary';
};

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
</script>
