<template>
    <FormWrapper
        ref="formWrapper"
        title="Editar Fatura de Cliente"
        :description="`Fatura #${invoice.number}`"
        :schema="customerInvoiceSchema"
        :initial-values="initialValues"
        :submit-url="`/customer-invoices/${invoice.id}`"
        submit-method="put"
        submit-text="Atualizar Fatura"
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

            <FormField v-slot="{ componentField }" name="customer_id">
                <FormItem>
                    <FormLabel>Cliente *</FormLabel>
                    <Select v-bind="componentField">
                        <FormControl>
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Selecione o cliente"
                                />
                            </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                            <SelectItem
                                v-for="customer in customers"
                                :key="customer.id"
                                :value="String(customer.id)"
                            >
                                {{ customer.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="order_id">
                <FormItem>
                    <FormLabel>Encomenda</FormLabel>
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
            <FormField v-slot="{ componentField }" name="paid_amount">
                <FormItem>
                    <FormLabel>Valor Pago *</FormLabel>
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
                            <SelectItem value="draft">Rascunho</SelectItem>
                            <SelectItem value="sent">Enviada</SelectItem>
                            <SelectItem value="paid">Paga</SelectItem>
                            <SelectItem value="partially_paid"
                                >Parcialmente Paga</SelectItem
                            >
                            <SelectItem value="overdue">Vencida</SelectItem>
                            <SelectItem value="cancelled">Cancelada</SelectItem>
                        </SelectContent>
                    </Select>
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
import { customerInvoiceSchema } from '@/schemas/customerInvoiceSchema';
import { computed, ref } from 'vue';

interface Props {
    invoice: {
        id: number;
        number: string;
        invoice_date: string;
        due_date: string;
        customer_id: number;
        order_id: number | null;
        paid_amount: number;
        total_amount: number;
        notes?: string;
        status: string;
    };
    customers: Array<{ id: number; name: string }>;
    orders: Array<{
        id: number;
        number: string;
        total_amount: number;
        client_id: number;
    }>;
}

const props = defineProps<Props>();

const formWrapper = ref<any>(null);

const initialValues = computed(() => ({
    invoice_date: props.invoice.invoice_date,
    due_date: props.invoice.due_date,
    customer_id: String(props.invoice.customer_id),
    order_id: props.invoice.order_id ? String(props.invoice.order_id) : 'none',
    paid_amount: props.invoice.paid_amount,
    total_amount: props.invoice.total_amount,
    notes: props.invoice.notes ?? '',
    status: props.invoice.status,
}));

const filteredOrders = computed(() => {
    // Usa o customer_id do formulário ou dos props como fallback
    const customerId = formWrapper.value?.form?.values?.customer_id 
        || props.invoice.customer_id;
    
    if (!customerId) return [];
    
    // Filtra as encomendas do cliente
    const orders = props.orders.filter(
        (order) => order.client_id === Number(customerId),
    );
    
    // Se há uma encomenda associada à fatura, garante que ela está na lista
    // (caso o cliente tenha mudado ou a encomenda não esteja na lista filtrada)
    if (props.invoice.order_id) {
        const currentOrder = props.orders.find(
            (order) => order.id === props.invoice.order_id
        );
        if (currentOrder && !orders.find((o) => o.id === currentOrder.id)) {
            orders.push(currentOrder);
        }
    }
    
    return orders;
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>
