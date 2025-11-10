<template>
    <FormWrapper
        ref="wrapperRef"
        title="Editar Encomenda"
        :description="`Encomenda #${order.number}`"
        :schema="orderSchema"
        :initial-values="initialValues"
        :submit-url="`/orders/${order.id}`"
        submit-method="put"
        submit-text="Atualizar Encomenda"
        :on-submit="handleSubmit"
        card-title="Informações da Encomenda"
    >
        <template #form-fields>
            <FormField v-slot="{ componentField }" name="order_date">
                <FormItem>
                    <FormLabel>Data da Encomenda</FormLabel>
                    <FormControl>
                        <DatePicker v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="client_id">
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
                                v-for="client in clients"
                                :key="client.id"
                                :value="String(client.id)"
                            >
                                {{ client.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="delivery_date">
                <FormItem>
                    <FormLabel>Data de Entrega *</FormLabel>
                    <FormControl>
                        <DatePicker v-bind="componentField" />
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
                            <SelectItem value="closed">Fechada</SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>
        </template>
        <template #articles-fields>
            <Card class="mb-6">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Artigos</CardTitle>
                        <Button type="button" @click="addItem">
                            <PlusIcon class="mr-2 h-4 w-4" />
                            Adicionar Artigo
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Validation message for items array -->
                    <FormField name="items">
                        <FormItem>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <div
                        v-if="formData.items.length === 0"
                        class="py-8 text-center text-muted-foreground"
                    >
                        Nenhum artigo adicionado. Clique em "Adicionar Artigo"
                        para começar.
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="(item, index) in formData.items"
                            :key="index"
                            class="space-y-4 rounded-lg border p-4"
                        >
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium">
                                    Item {{ index + 1 }}
                                </h4>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    @click="removeItem(index)"
                                >
                                    <Trash2Icon class="h-4 w-4" />
                                </Button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                                <!-- Artigo -->
                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-sm">Artigo *</label>
                                    <select
                                        v-model="item.article_id"
                                        @change="onArticleChange(index)"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        required
                                    >
                                        <option value="">Selecione</option>
                                        <option
                                            v-for="article in articles"
                                            :key="article.id"
                                            :value="String(article.id)"
                                        >
                                            {{ article.reference }} -
                                            {{ article.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Fornecedor -->
                                <div class="space-y-2">
                                    <label class="text-sm">Fornecedor</label>
                                    <select
                                        v-model="item.supplier_id"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    >
                                        <option value="">Sem fornecedor</option>
                                        <option
                                            v-for="supplier in suppliers"
                                            :key="supplier.id"
                                            :value="String(supplier.id)"
                                        >
                                            {{ supplier.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Quantidade -->
                                <div class="space-y-2">
                                    <label class="text-sm">Quantidade *</label>
                                    <Input
                                        type="number"
                                        step="1.00"
                                        min="0"
                                        v-model.number="item.quantity"
                                        @input="calculateItemTotal(index)"
                                        required
                                    />
                                </div>

                                <!-- Preço Unit. -->
                                <div class="space-y-2">
                                    <label class="text-sm">Preço Unit. *</label>
                                    <Input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        v-model.number="item.unit_price"
                                        @input="calculateItemTotal(index)"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Notas -->
                            <div class="space-y-2">
                                <label class="text-sm">Notas</label>
                                <Textarea
                                    v-model="item.notes"
                                    placeholder="Notas sobre este item"
                                    rows="2"
                                />
                            </div>

                            <!-- Total da Linha -->
                            <div class="text-right">
                                <span class="text-sm text-muted-foreground"
                                    >Subtotal:</span
                                >
                                <span class="ml-2 font-medium">{{
                                    formatCurrency(
                                        item.quantity * item.unit_price || 0,
                                    )
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Geral -->
                    <div
                        v-if="formData.items.length > 0"
                        class="mt-6 border-t pt-4 text-right"
                    >
                        <div class="text-lg">
                            <span class="text-muted-foreground">Total:</span>
                            <span class="ml-4 text-2xl font-bold">{{
                                formatCurrency(totalAmount)
                            }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import FormWrapper from '@/components/common/FormWrapper.vue';
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { Textarea } from '@/components/ui/textarea';
import { orderSchema } from '@/schemas/orderSchema';
import { Article } from '@/types';
import { router } from '@inertiajs/vue3';
import { PlusIcon, Trash2Icon } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

interface OrderItem {
    id?: number;
    article_id: string | number;
    supplier_id?: string | number;
    quantity: number;
    unit_price: number;
    notes?: string;
}

interface Props {
    order: {
        id: number;
        number: string;
        order_date: string;
        client_id: number;
        delivery_date: string;
        status: 'draft' | 'closed';
        items: OrderItem[];
    };
    clients: Array<{ id: number; name: string }>;
    suppliers: Array<{ id: number; name: string }>;
    articles: Article[];
}

const props = defineProps<Props>();

const wrapperRef = ref<InstanceType<typeof FormWrapper> | null>(null);

//form data para items
const formData = reactive({
    items: props.order.items.map((item) => ({
        article_id: String(item.article_id),
        supplier_id: item.supplier_id ? String(item.supplier_id) : '',
        quantity: item.quantity,
        unit_price: item.unit_price,
        notes: item.notes || '',
    })),
});

// Initial values for FormWrapper
const initialValues = computed(() => ({
    order_date: props.order.order_date,
    client_id: String(props.order.client_id),
    delivery_date: props.order.delivery_date || '',
    status: props.order.status,
    items: formData.items,
}));

const addItem = () => {
    formData.items.push({
        article_id: '',
        supplier_id: '',
        quantity: 1,
        unit_price: 0,
        notes: '',
    });
};

const removeItem = (index: number) => {
    formData.items.splice(index, 1);
};

const onArticleChange = (index: number) => {
    const item = formData.items[index];
    const article = props.articles.find(
        (a) => a.id === Number(item.article_id),
    );
    if (article) {
        item.unit_price = article.price;
    }
};

const calculateItemTotal = (index: number) => {
    // Trigger reactivity
    formData.items[index] = { ...formData.items[index] };
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};

watch(
    () => formData.items,
    (items) => {
        wrapperRef.value?.form.setFieldValue('items', items);
    },
    { deep: true, immediate: true },
);

const totalAmount = computed(() => {
    return formData.items.reduce((sum, item) => {
        return sum + (item.quantity || 0) * (item.unit_price || 0);
    }, 0);
});

// Custom submit handler
const handleSubmit = (values: any) => {
    // Merge form values with items
    const submitData = {
        ...values,
        items: formData.items,
    };

    router.put(`/orders/${props.order.id}`, submitData, {
        preserveScroll: true,
    });
};
</script>
