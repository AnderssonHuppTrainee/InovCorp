<template>
    <FormWrapper
        title="Novo Artigo"
        description="Criar novo artigo no catálogo"
        :schema="articleSchema"
        :initial-values="initialValues"
        submit-url="/articles"
        submit-text="Criar Artigo"
        :on-submit="handleSubmit"
    >
        <template #form-fields>
            <FormField v-slot="{ componentField }" name="reference">
                <FormItem>
                    <FormLabel>Referência *</FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" placeholder="REF001" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="name">
                <FormItem>
                    <FormLabel>Nome *</FormLabel>
                    <FormControl>
                        <Input
                            v-bind="componentField"
                            placeholder="Nome do artigo"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="price">
                <FormItem>
                    <FormLabel>Preço *</FormLabel>
                    <FormControl>
                        <Input
                            type="number"
                            step="0.01"
                            min="0"
                            v-bind="componentField"
                            placeholder="0.00"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="tax_rate_id">
                <FormItem>
                    <FormLabel>IVA *</FormLabel>
                    <Select v-bind="componentField">
                        <FormControl>
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Selecione a taxa de IVA"
                                />
                            </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                            <SelectItem
                                v-for="tax in taxRates"
                                :key="tax.id"
                                :value="String(tax.id)"
                            >
                                {{ tax.name }} ({{ tax.rate }}%)
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField
                v-slot="{ componentField }"
                name="description"
                class="lg:col-span-2"
            >
                <FormItem>
                    <FormLabel>Descrição</FormLabel>
                    <FormControl>
                        <Textarea
                            v-bind="componentField"
                            rows="3"
                            placeholder="Descrição do artigo"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="photo">
                <FormItem>
                    <FormLabel>Foto</FormLabel>
                    <FormControl>
                        <Input
                            type="file"
                            accept="image/*"
                            @change="handlePhotoChange"
                        />
                    </FormControl>
                    <FormDescription>
                        JPG, PNG ou GIF (máx. 5MB)
                    </FormDescription>
                    <p v-if="photoPreview" class="text-sm text-green-600">
                        ✓ Foto selecionada
                    </p>
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
                            <SelectItem value="active">Ativo</SelectItem>
                            <SelectItem value="inactive">Inativo</SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField
                v-slot="{ componentField }"
                name="observations"
                class="lg:col-span-2"
            >
                <FormItem>
                    <FormLabel>Observações</FormLabel>
                    <FormControl>
                        <Textarea
                            v-bind="componentField"
                            rows="3"
                            placeholder="Observações internas"
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
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { articleSchema } from '@/schemas/articleSchema';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
    taxRates: Array<{ id: number; name: string; rate: number }>;
}

const props = defineProps<Props>();

const photoFile = ref<File | null>(null);
const photoPreview = ref<string | null>(null);

const initialValues = computed(() => ({
    reference: '',
    name: '',
    description: '',
    price: 0,
    tax_rate_id: '',
    observations: '',
    status: 'active',
}));

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        photoFile.value = target.files[0];
        photoPreview.value = URL.createObjectURL(target.files[0]);
    }
};

const handleSubmit = (values: any) => {
    const data = new FormData();
    data.append('reference', values.reference);
    data.append('name', values.name);
    if (values.description) data.append('description', values.description);
    data.append('price', String(values.price));
    data.append('tax_rate_id', values.tax_rate_id);
    if (values.observations) data.append('observations', values.observations);
    data.append('status', values.status);
    if (photoFile.value) data.append('photo', photoFile.value);

    router.post('/articles', data, {
        preserveScroll: true,
    });
};
</script>
