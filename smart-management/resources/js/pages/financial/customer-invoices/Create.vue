<template>
    <FormWrapper
        ref="formWrapper"
        title="Nova Fatura de Cliente"
        description="Criar fatura para cliente"
        :schema="customerInvoiceSchema"
        :initial-values="initialValues"
        submit-url="/customer-invoices"
        submit-method="post"
        submit-text="Guardar Fatura"
    >
        <template #form-fields>
            <FormField v-slot="{ value, handleChange }" name="invoice_date">
                <FormItem>
                    <FormLabel>Data da Fatura *</FormLabel>
                    <FormControl>
                        <DatePicker
                            :model-value="value"
                            @update:model-value="handleChange"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ value, handleChange }" name="due_date">
                <FormItem>
                    <FormLabel>Vencimento *</FormLabel>
                    <FormControl>
                        <DatePicker
                            :model-value="value"
                            @update:model-value="handleChange"
                        />
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
                            step="0.01"
                            min="0"
                            v-bind="componentField"
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
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField
                v-slot="{ componentField }"
                name="notes"
                class="lg:col-span-2"
            >
                <FormItem>
                    <FormLabel>Observações</FormLabel>
                    <FormControl>
                        <Textarea
                            placeholder="Notas adicionais..."
                            rows="4"
                            v-bind="componentField"
                        />
                    </FormControl>
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
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { customerInvoiceSchema } from '@/schemas/customerInvoiceSchema';
import { computed, ref } from 'vue';

interface Props {
    customers: Array<{ id: number; name: string }>;
    orders: Array<{
        id: number;
        number: string;
        client_id: number;
        total_amount: number;
    }>;
}

const props = defineProps<Props>();

const formWrapper = ref<any>(null);

const initialValues = computed(() => ({
    invoice_date: new Date().toISOString().split('T')[0],
    due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)
        .toISOString()
        .split('T')[0],
    customer_id: '',
    order_id: '',
    total_amount: 0,
    notes: '',
    status: 'draft',
}));

const filteredOrders = computed(() => {
    const customerId = formWrapper.value?.form?.values?.customer_id;
    if (!customerId || !formWrapper.value?.form) return [];
    return props.orders.filter(
        (order) => order.client_id === Number(customerId),
    );
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>
