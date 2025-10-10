<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Editar Fatura" :description="`Fatura #${invoice.number}`">
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <form @submit.prevent="submitForm">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium">Data *</label>
                                        <DatePicker v-model="formData.invoice_date" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium">Vencimento *</label>
                                        <DatePicker v-model="formData.due_date" />
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Fornecedor *</label>
                                    <select v-model="formData.supplier_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                                        <option value="">Selecione</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Encomenda</label>
                                    <select v-model="formData.supplier_order_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        <option value="">Sem encomenda</option>
                                        <option v-for="order in filteredOrders" :key="order.id" :value="order.id">
                                            {{ order.number }}
                                        </option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Valor *</label>
                                    <Input type="number" step="0.01" min="0" v-model.number="formData.total_amount" required />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Documento</label>
                                    <Input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="handleDocumentChange" />
                                    <p v-if="hasDocument" class="text-sm text-green-600">✓ Documento existente</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Comprovativo</label>
                                    <Input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="handlePaymentProofChange" />
                                    <p v-if="hasPaymentProof" class="text-sm text-green-600">✓ Comprovativo existente</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Estado *</label>
                                    <select v-model="formData.status" @change="handleStatusChange" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        <option value="pending_payment">Pendente</option>
                                        <option value="paid">Paga</option>
                                    </select>
                                </div>
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

        <Dialog v-model:open="showEmailDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Enviar Comprovativo</DialogTitle>
                    <DialogDescription>Enviar comprovativo ao fornecedor?</DialogDescription>
                </DialogHeader>
                <div class="py-4">
                    <div class="flex items-center space-x-2">
                        <Checkbox id="send-email-edit" v-model:checked="sendEmailConfirmed" />
                        <label for="send-email-edit" class="text-sm font-medium cursor-pointer">Sim, enviar por email</label>
                    </div>
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showEmailDialog = false">Não</Button>
                    <Button type="button" @click="confirmEmailAndSubmit">Continuar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

interface Props {
    invoice: any;
    suppliers: Array<{ id: number; name: string; email: string | null }>;
    supplierOrders: Array<{ id: number; number: string; supplier_id: number }>;
    hasDocument: boolean;
    hasPaymentProof: boolean;
}

const props = defineProps<Props>();

const isSubmitting = ref(false);
const showEmailDialog = ref(false);
const sendEmailConfirmed = ref(false);
const documentFile = ref<File | null>(null);
const paymentProofFile = ref<File | null>(null);
const previousStatus = ref(props.invoice.status);

const formData = reactive({
    invoice_date: props.invoice.invoice_date,
    due_date: props.invoice.due_date,
    supplier_id: props.invoice.supplier_id,
    supplier_order_id: props.invoice.supplier_order_id || '',
    total_amount: props.invoice.total_amount,
    status: props.invoice.status,
});

const filteredOrders = computed(() => {
    if (!formData.supplier_id) return [];
    return props.supplierOrders.filter(order => order.supplier_id === Number(formData.supplier_id));
});

const selectedSupplier = computed(() => {
    return props.suppliers.find(s => s.id === Number(formData.supplier_id));
});

const selectedSupplierEmail = computed(() => selectedSupplier.value?.email || null);

const goBack = () => router.get('/supplier-invoices');

const handleDocumentChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) documentFile.value = target.files[0];
};

const handlePaymentProofChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) paymentProofFile.value = target.files[0];
};

const handleStatusChange = () => {
    const statusChangedToPaid = previousStatus.value !== 'paid' && formData.status === 'paid';
    if (statusChangedToPaid && paymentProofFile.value) {
        showEmailDialog.value = true;
    }
};

const confirmEmailAndSubmit = () => {
    showEmailDialog.value = false;
    performSubmit();
};

const submitForm = () => {
    const statusChangedToPaid = previousStatus.value !== 'paid' && formData.status === 'paid';
    if (statusChangedToPaid && paymentProofFile.value) {
        showEmailDialog.value = true;
        return;
    }
    performSubmit();
};

const performSubmit = () => {
    isSubmitting.value = true;

    const data = new FormData();
    data.append('invoice_date', formData.invoice_date);
    data.append('due_date', formData.due_date);
    data.append('supplier_id', String(formData.supplier_id));
    if (formData.supplier_order_id) data.append('supplier_order_id', String(formData.supplier_order_id));
    data.append('total_amount', String(formData.total_amount));
    data.append('status', formData.status);
    data.append('_method', 'PUT');

    if (documentFile.value) data.append('document', documentFile.value);
    if (paymentProofFile.value) data.append('payment_proof', paymentProofFile.value);
    if (sendEmailConfirmed.value) data.append('send_email', '1');

    router.post(`/supplier-invoices/${props.invoice.id}`, data, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
};
</script>




