<template>
    <FormWrapper
        ref="formWrapper"
        title="Nova Fatura de Fornecedor"
        description="Registar nova fatura"
        :schema="supplierInvoiceSchema"
        :initial-values="initialValues"
        submit-url="/supplier-invoices"
        submit-text="Guardar Fatura"
        :on-submit="handleSubmit"
    >
        <template #form-fields>
            <FormField v-slot="{ componentField }" name="invoice_date">
                <FormItem>
                    <FormLabel>Data da Fatura *</FormLabel>
                    <FormControl>
                        <DatePicker v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="due_date">
                <FormItem>
                    <FormLabel>Vencimento *</FormLabel>
                    <FormControl>
                        <DatePicker v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <!-- Fornecedor -->
            <FormField v-slot="{ componentField }" name="supplier_id">
                <FormItem>
                    <FormLabel>Fornecedor *</FormLabel>
                    <Select v-bind="componentField">
                        <FormControl>
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Selecione o fornecedor"
                                />
                            </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                            <SelectItem
                                v-for="supplier in suppliers"
                                :key="supplier.id"
                                :value="String(supplier.id)"
                            >
                                {{ supplier.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <!-- Encomenda Fornecedor -->
            <FormField v-slot="{ componentField }" name="supplier_order_id">
                <FormItem>
                    <FormLabel>Encomenda Fornecedor</FormLabel>
                    <Select v-bind="componentField">
                        <FormControl>
                            <SelectTrigger>
                                <SelectValue placeholder="Sem encomenda" />
                            </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                            <SelectItem value="none">Sem encomenda</SelectItem>
                            <SelectItem
                                v-for="order in filteredOrders"
                                :key="order.id"
                                :value="String(order.id)"
                            >
                                {{ order.number }} -
                                {{ formatCurrency(order.total_amount) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <!-- Valor Total -->
            <FormField v-slot="{ componentField }" name="total_amount">
                <FormItem>
                    <FormLabel>Valor Total *</FormLabel>
                    <FormControl>
                        <Input
                            type="number"
                            step="0.01"
                            min="0"
                            v-bind="componentField"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <!-- Estado -->
            <FormField v-slot="{ componentField }" name="status">
                <FormItem>
                    <FormLabel>Estado *</FormLabel>
                    <Select v-bind="componentField">
                        <FormControl>
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione o estado" />
                            </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                            <SelectItem value="pending_payment"
                                >Pendente de Pagamento</SelectItem
                            >
                            <SelectItem value="paid">Paga</SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField name="document">
                <FormItem>
                    <FormLabel>Documento</FormLabel>
                    <FormControl>
                        <Input
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            @change="handleDocumentChange"
                        />
                    </FormControl>
                    <FormDescription
                        >PDF, JPG ou PNG (máx. 10MB)</FormDescription
                    >
                    <FormMessage />
                </FormItem>
            </FormField>

            <!-- Comprovativo de Pagamento -->
            <FormField name="payment_proof">
                <FormItem>
                    <FormLabel>Comprovativo de Pagamento</FormLabel>
                    <FormControl>
                        <Input
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            @change="handlePaymentProofChange"
                        />
                    </FormControl>
                    <FormDescription
                        >PDF, JPG ou PNG (máx. 10MB)</FormDescription
                    >
                    <FormMessage />
                </FormItem>
            </FormField>
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import FormWrapper from '@/components/common/FormWrapper.vue';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';

import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { supplierInvoiceSchema } from '@/schemas/supplierInvoiceSchema';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
    invoices?: any;
    filters?: any;
    suppliers: Array<{ id: number; name: string; email: string | null }>;
    supplierOrders: Array<{
        id: number;
        number: string;
        supplier_id: number;
        total_amount: number;
    }>;
}

const props = defineProps<Props>();

const formWrapper = ref<any>(null);
const showEmailDialog = ref(false);
const sendEmailConfirmed = ref(false);
const documentFile = ref<File | null>(null);
const paymentProofFile = ref<File | null>(null);

const initialValues = computed(() => ({
    invoice_date: new Date().toISOString().split('T')[0],
    due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)
        .toISOString()
        .split('T')[0],
    supplier_id: '',
    supplier_order_id: '',
    total_amount: 0,
    status: 'pending_payment',
}));

// Computed
const filteredOrders = computed(() => {
    const supplierId = formWrapper.value?.form?.values?.supplier_id;
    if (!supplierId) return [];
    return props.supplierOrders.filter(
        (order) => order.supplier_id === Number(supplierId),
    );
});

const handleDocumentChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        documentFile.value = target.files[0];
    }
};

const handlePaymentProofChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        paymentProofFile.value = target.files[0];
    }
};

const handleSubmit = (values: any) => {
    const data = new FormData();
    data.append('invoice_date', values.invoice_date);
    data.append('due_date', values.due_date);
    data.append('supplier_id', values.supplier_id);
    if (values.supplier_order_id)
        data.append('supplier_order_id', values.supplier_order_id);
    data.append('total_amount', String(values.total_amount));
    data.append('status', values.status);

    if (documentFile.value) data.append('document', documentFile.value);
    if (paymentProofFile.value)
        data.append('payment_proof', paymentProofFile.value);
    if (sendEmailConfirmed.value) data.append('send_email', '1');

    router.post('/supplier-invoices', data, {
        preserveScroll: true,
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>
