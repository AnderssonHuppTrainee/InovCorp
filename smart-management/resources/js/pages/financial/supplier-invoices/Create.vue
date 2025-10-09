<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Nova Fatura de Fornecedor" description="Registar nova fatura">
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <form @submit.prevent="submitForm">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- Coluna 1 -->
                            <div class="space-y-6">
                                <!-- Datas -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium">Data da Fatura *</label>
                                        <DatePicker v-model="formData.invoice_date" placeholder="Selecione" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium">Vencimento *</label>
                                        <DatePicker v-model="formData.due_date" placeholder="Selecione" />
                                    </div>
                                </div>

                                <!-- Fornecedor -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Fornecedor *</label>
                                    <select v-model="formData.supplier_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                                        <option value="">Selecione</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Encomenda Fornecedor -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Encomenda Fornecedor</label>
                                    <select v-model="formData.supplier_order_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        <option value="">Sem encomenda</option>
                                        <option v-for="order in filteredOrders" :key="order.id" :value="order.id">
                                            {{ order.number }} - {{ formatCurrency(order.total_amount) }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Valor Total -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Valor Total *</label>
                                    <Input type="number" step="0.01" min="0" v-model.number="formData.total_amount" required />
                                </div>
                            </div>

                            <!-- Coluna 2 -->
                            <div class="space-y-6">
                                <!-- Documento -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Documento</label>
                                    <Input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="handleDocumentChange" />
                                    <p class="text-sm text-muted-foreground">PDF, JPG ou PNG (máx. 10MB)</p>
                                </div>

                                <!-- Comprovativo de Pagamento -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Comprovativo de Pagamento</label>
                                    <Input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="handlePaymentProofChange" />
                                    <p class="text-sm text-muted-foreground">PDF, JPG ou PNG (máx. 10MB)</p>
                                </div>

                                <!-- Estado -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Estado *</label>
                                    <select v-model="formData.status" @change="handleStatusChange" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        <option value="pending_payment">Pendente de Pagamento</option>
                                        <option value="paid">Paga</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Botões -->
                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack">Cancelar</Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A guardar...' : 'Guardar Fatura' }}
                    </Button>
                </div>
            </form>
        </div>

        <!-- Dialog de Envio de Comprovativo -->
        <Dialog v-model:open="showEmailDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Enviar Comprovativo ao Fornecedor</DialogTitle>
                    <DialogDescription>
                        Deseja enviar o comprovativo de pagamento ao fornecedor por email?
                    </DialogDescription>
                </DialogHeader>

                <div class="py-4">
                    <p class="text-sm text-muted-foreground mb-4">
                        <strong>Fornecedor:</strong> {{ selectedSupplierName }}<br />
                        <strong>Email:</strong> {{ selectedSupplierEmail || 'Não configurado' }}
                    </p>

                    <div v-if="!selectedSupplierEmail" class="rounded-lg bg-destructive/10 p-4 text-sm text-destructive">
                        ⚠️ O fornecedor não tem email configurado. O comprovativo não pode ser enviado automaticamente.
                    </div>

                    <div v-else class="flex items-center space-x-2">
                        <Checkbox id="send-email" v-model:checked="sendEmailConfirmed" />
                        <label for="send-email" class="text-sm font-medium cursor-pointer">
                            Sim, enviar comprovativo por email
                        </label>
                    </div>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="showEmailDialog = false">
                        Não enviar
                    </Button>
                    <Button type="button" @click="confirmEmailAndSubmit" :disabled="!selectedSupplierEmail">
                        Continuar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import DataTable from '@/components/ui/data-table/DataTable.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, ChevronLeftIcon, ChevronRightIcon, LoaderIcon, PlusIcon, SaveIcon, SearchIcon, XIcon } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';
import { columns } from './columns';

interface Props {
    invoices?: any;
    filters?: any;
    suppliers: Array<{ id: number; name: string; email: string | null }>;
    supplierOrders: Array<{ id: number; number: string; supplier_id: number; total_amount: number }>;
}

const props = defineProps<Props>();

const isSubmitting = ref(false);
const showEmailDialog = ref(false);
const sendEmailConfirmed = ref(false);
const documentFile = ref<File | null>(null);
const paymentProofFile = ref<File | null>(null);

const formData = reactive({
    invoice_date: new Date().toISOString().split('T')[0],
    due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    supplier_id: '',
    supplier_order_id: '',
    total_amount: 0,
    status: 'pending_payment' as 'pending_payment' | 'paid',
});

// Computed
const filteredOrders = computed(() => {
    if (!formData.supplier_id) return [];
    return props.supplierOrders.filter(order => order.supplier_id === Number(formData.supplier_id));
});

const selectedSupplier = computed(() => {
    return props.suppliers.find(s => s.id === Number(formData.supplier_id));
});

const selectedSupplierName = computed(() => selectedSupplier.value?.name || '');
const selectedSupplierEmail = computed(() => selectedSupplier.value?.email || null);

// Paginação (se Index for chamado aqui)
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const supplierFilter = ref(props.filters?.supplier_id || 'all');

const hasFilters = computed(() => {
    return searchQuery.value !== '' || statusFilter.value !== 'all' || supplierFilter.value !== 'all';
});

const visiblePages = computed(() => {
    if (!props.invoices) return [];
    const current = props.invoices.current_page;
    const last = props.invoices.last_page;
    const delta = 2;
    const pages: number[] = [];

    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        pages.push(i);
    }

    if (current - delta > 2) pages.unshift(-1);
    if (current + delta < last - 1) pages.push(-1);
    pages.unshift(1);
    if (last > 1) pages.push(last);

    return pages.filter((v, i, a) => a.indexOf(v) === i);
});

let searchTimeout: ReturnType<typeof setTimeout>;

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 300);
};

const handleFilterChange = () => applyFilters();

const applyFilters = () => {
    const params: any = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all') params.status = statusFilter.value;
    if (supplierFilter.value && supplierFilter.value !== 'all') params.supplier_id = supplierFilter.value;

    router.get('/supplier-invoices', params, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    supplierFilter.value = 'all';
    router.get('/supplier-invoices', {}, { preserveState: true, preserveScroll: true });
};

const goToPage = (page: number) => {
    const params: any = { page };
    if (searchQuery.value) params.search = searchQuery.value;
    if (statusFilter.value && statusFilter.value !== 'all') params.status = statusFilter.value;
    if (supplierFilter.value && supplierFilter.value !== 'all') params.supplier_id = supplierFilter.value;

    router.get('/supplier-invoices', params, { preserveState: true, preserveScroll: true });
};

const handleCreate = () => router.get('/supplier-invoices/create');

// Methods
const goBack = () => router.get('/supplier-invoices');

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

const handleStatusChange = () => {
    // Se mudou para "paga" e tem comprovativo, mostrar dialog
    if (formData.status === 'paid' && paymentProofFile.value) {
        showEmailDialog.value = true;
    }
};

const confirmEmailAndSubmit = () => {
    showEmailDialog.value = false;
    performSubmit();
};

const submitForm = () => {
    // Se está como "paga" e tem comprovativo, mostrar dialog
    if (formData.status === 'paid' && paymentProofFile.value && !showEmailDialog.value) {
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
    data.append('supplier_id', formData.supplier_id);
    if (formData.supplier_order_id) data.append('supplier_order_id', formData.supplier_order_id);
    data.append('total_amount', String(formData.total_amount));
    data.append('status', formData.status);
    
    if (documentFile.value) data.append('document', documentFile.value);
    if (paymentProofFile.value) data.append('payment_proof', paymentProofFile.value);
    if (sendEmailConfirmed.value) data.append('send_email', '1');

    router.post('/supplier-invoices', data, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};
</script>


