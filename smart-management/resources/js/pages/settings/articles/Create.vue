<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Novo Artigo" description="Criar novo artigo no catálogo">
                <Button variant="outline" @click="goBack"><ArrowLeftIcon class="mr-2 h-4 w-4" />Voltar</Button>
            </PageHeader>

            <form @submit.prevent="submitForm">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Referência *</label>
                                <Input v-model="formData.reference" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Nome *</label>
                                <Input v-model="formData.name" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Preço *</label>
                                <Input type="number" step="0.01" min="0" v-model.number="formData.price" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">IVA *</label>
                                <select v-model="formData.tax_rate_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                                    <option value="">Selecione</option>
                                    <option v-for="tax in taxRates" :key="tax.id" :value="tax.id">
                                        {{ tax.name }} ({{ tax.rate }}%)
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Descrição</label>
                                <Textarea v-model="formData.description" rows="3" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Foto</label>
                                <Input type="file" accept="image/*" @change="handlePhotoChange" />
                                <p v-if="photoPreview" class="text-sm text-green-600">✓ Foto selecionada</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Estado *</label>
                                <select v-model="formData.status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
                                </select>
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Observações</label>
                                <Textarea v-model="formData.observations" rows="3" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack">Cancelar</Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A guardar...' : 'Criar Artigo' }}
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
import { Input } from '@/components/ui/input';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

interface Props {
    taxRates: Array<{ id: number; name: string; rate: number }>;
}

const props = defineProps<Props>();

const isSubmitting = ref(false);
const photoFile = ref<File | null>(null);
const photoPreview = ref<string | null>(null);

const formData = reactive({
    reference: '',
    name: '',
    description: '',
    price: 0,
    tax_rate_id: '',
    observations: '',
    status: 'active' as 'active' | 'inactive',
});

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        photoFile.value = target.files[0];
        photoPreview.value = URL.createObjectURL(target.files[0]);
    }
};

const submitForm = () => {
    isSubmitting.value = true;

    const data = new FormData();
    data.append('reference', formData.reference);
    data.append('name', formData.name);
    if (formData.description) data.append('description', formData.description);
    data.append('price', String(formData.price));
    data.append('tax_rate_id', formData.tax_rate_id);
    if (formData.observations) data.append('observations', formData.observations);
    data.append('status', formData.status);
    if (photoFile.value) data.append('photo', photoFile.value);

    router.post('/articles', data, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
};

const goBack = () => router.get('/articles');
</script>



