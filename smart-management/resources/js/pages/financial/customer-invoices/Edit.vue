<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Editar Fatura" :description="`Fatura #${invoice.number}`">
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <form @submit="onSubmit">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Data *</label>
                                <DatePicker v-model="formData.invoice_date" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Vencimento *</label>
                                <DatePicker v-model="formData.due_date" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Cliente *</label>
                                <select v-model="formData.customer_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Encomenda</label>
                                <select v-model="formData.order_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    <option value="">Sem encomenda</option>
                                    <option v-for="order in filteredOrders" :key="order.id" :value="order.id">
                                        {{ order.number }}
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Total *</label>
                                <Input type="number" step="0.01" v-model.number="formData.total_amount" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Pago</label>
                                <Input type="number" step="0.01" v-model.number="formData.paid_amount" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Estado *</label>
                                <select v-model="formData.status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    <option value="draft">Rascunho</option>
                                    <option value="sent">Enviada</option>
                                    <option value="partially_paid">Parcialmente Paga</option>
                                    <option value="paid">Paga</option>
                                    <option value="overdue">Vencida</option>
                                    <option value="cancelled">Cancelada</option>
                                </select>
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Observações</label>
                                <Textarea v-model="formData.notes" rows="4" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack">Cancelar</Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A atualizar...' : 'Atualizar' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import { Input } from '@/components/ui/input';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

interface Props {
    invoice: any;
    customers: Array<{ id: number; name: string }>;
    orders: Array<{ id: number; number: string; client_id: number }>;
}

const props = defineProps<Props>();

const isSubmitting = ref(false);

const formData = reactive({
    invoice_date: props.invoice.invoice_date,
    due_date: props.invoice.due_date,
    customer_id: props.invoice.customer_id,
    order_id: props.invoice.order_id || '',
    total_amount: props.invoice.total_amount,
    paid_amount: props.invoice.paid_amount,
    notes: props.invoice.notes || '',
    status: props.invoice.status,
});

const filteredOrders = computed(() => {
    if (!formData.customer_id) return [];
    return props.orders.filter(order => order.client_id === Number(formData.customer_id));
});

const onSubmit = (e: Event) => {
    e.preventDefault();
    isSubmitting.value = true;
    router.put(`/customer-invoices/${props.invoice.id}`, formData, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
};

const goBack = () => router.get('/customer-invoices');
</script>









