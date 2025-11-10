<template>
    <FormWrapper
        ref="formWrapper"
        title="Editar Fatura de Fornecedor"
        :description="`Fatura #${invoice.number}`"
        :schema="supplierInvoiceSchema"
        :initial-values="initialValues"
        :submit-url="`/supplier-invoices/${invoice.id}`"
        submit-method="put"
        submit-text="Atualizar Fatura"
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
                    <FormLabel>Data de Vencimento *</FormLabel>
                    <FormControl>
                        <DatePicker v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

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

            <FormField v-slot="{ componentField }" name="supplier_order_id">
                <FormItem>
                    <FormLabel>Encomenda </FormLabel>
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

            <FormField v-slot="{ componentField }" name="total_amount">
                <FormItem>
                    <FormLabel>Valor Total *</FormLabel>
                    <FormControl>
                        <Input
                            type="number"
                            min="0"
                            step="0.01"
                            v-bind="componentField"
                            placeholder="0.00"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

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

            <FormField name="document" class="lg:col-span-2">
                <FormItem>
                    <FormLabel>Documento</FormLabel>
                    <FormControl>
                        <Input
                            type="file"
                            accept=".pdf,.doc,.docx"
                            @change="handleDocumentChange"
                        />
                    </FormControl>
                    <FormDescription>
                        PDF, DOC ou DOCX (máx. 10MB)
                    </FormDescription>
                    <p v-if="hasDocument" class="text-sm text-green-600">
                        ✓ Comprovativo existente
                    </p>
                    <p v-if="documentPreview" class="text-sm text-green-600">
                        ✓ Documento selecionado
                    </p>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField name="payment_proof" class="lg:col-span-2">
                <FormItem>
                    <FormLabel>Comprovativo de Pagamento</FormLabel>
                    <FormControl>
                        <Input
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            @change="handlePaymentProofChange"
                        />
                    </FormControl>
                    <FormDescription>
                        PDF, JPG ou PNG (máx. 10MB)
                    </FormDescription>
                    <p v-if="hasPaymentProof" class="text-sm text-green-600">
                        ✓ Comprovativo existente
                    </p>
                    <p
                        v-if="paymentProofPreview"
                        class="text-sm text-green-600"
                    >
                        ✓ Comprovativo selecionado
                    </p>
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
    invoice: {
        id: number;
        number: string;
        invoice_date: string;
        due_date: string;
        supplier_id: number;
        supplier_order_id: number | null;
        total_amount: number;
        status: string;
    };
    suppliers: Array<{ id: number; name: string }>;
    supplierOrders: Array<{
        id: number;
        number: string;
        total_amount: number;
        supplier_id: number;
    }>;
    hasDocument: boolean;
    hasPaymentProof: boolean;
}

const props = defineProps<Props>();

const showEmailDialog = ref(false);
const formWrapper = ref<any>(null);
const documentFile = ref<File | null>(null);
const paymentProofFile = ref<File | null>(null);
const documentPreview = ref<string | null>(null);
const paymentProofPreview = ref<string | null>(null);

const filteredOrders = computed(() => {
    // Usa o supplier_id do formulário ou dos props como fallback
    const supplierId =
        formWrapper.value?.form?.values?.supplier_id ||
        props.invoice.supplier_id;

    if (!supplierId) return [];

    // Filtra as encomendas do fornecedor
    const orders = props.supplierOrders.filter(
        (order) => order.supplier_id === Number(supplierId),
    );

    // Se há uma encomenda associada à fatura, garante que ela está na lista
    // (caso o fornecedor tenha mudado ou a encomenda não esteja na lista filtrada)
    if (props.invoice.supplier_order_id) {
        const currentOrder = props.supplierOrders.find(
            (order) => order.id === props.invoice.supplier_order_id,
        );
        if (currentOrder && !orders.find((o) => o.id === currentOrder.id)) {
            orders.push(currentOrder);
        }
    }

    return orders;
});

const initialValues = computed(() => ({
    invoice_date: props.invoice.invoice_date,
    due_date: props.invoice.due_date,
    supplier_id: String(props.invoice.supplier_id),
    supplier_order_id: props.invoice.supplier_order_id
        ? String(props.invoice.supplier_order_id)
        : 'none',
    total_amount: props.invoice.total_amount,
    status: props.invoice.status,
}));

const handleDocumentChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        documentFile.value = target.files[0];
        documentPreview.value = target.files[0].name;
    }
};

const handlePaymentProofChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        paymentProofFile.value = target.files[0];
        paymentProofPreview.value = target.files[0].name;
    }
};

const handleSubmit = (values: any) => {
    const data = new FormData();
    data.append('invoice_date', values.invoice_date);
    data.append('due_date', values.due_date);
    data.append('supplier_id', values.supplier_id);
    if (values.supplier_order_id && values.supplier_order_id !== 'none')
        data.append('supplier_order_id', values.supplier_order_id);
    data.append('total_amount', String(values.total_amount));
    data.append('status', values.status);
    data.append('_method', 'PUT');

    if (documentFile.value) data.append('document', documentFile.value);
    if (paymentProofFile.value)
        data.append('payment_proof', paymentProofFile.value);

    router.post(`/supplier-invoices/${props.invoice.id}`, data, {
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
