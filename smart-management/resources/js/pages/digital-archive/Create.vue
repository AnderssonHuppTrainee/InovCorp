<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Enviar Ficheiro" description="Adicionar novo ficheiro ao arquivo digital">
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <form @submit.prevent="submitForm">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Ficheiro *</label>
                                <Input type="file" @change="handleFileChange" required />
                                <p class="text-sm text-muted-foreground">Máximo: 50MB</p>
                                <p v-if="selectedFile" class="text-sm text-green-600">
                                    ✓ {{ selectedFile.name }} ({{ formatBytes(selectedFile.size) }})
                                </p>
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Nome *</label>
                                <Input v-model="formData.name" placeholder="Nome descritivo do ficheiro" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Tipo de Documento *</label>
                                <Input v-model="formData.document_type" placeholder="Ex: Fatura, Contrato, Proposta" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Data de Expiração</label>
                                <DatePicker v-model="formData.expires_at" placeholder="Opcional" />
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Descrição</label>
                                <Textarea v-model="formData.description" placeholder="Descrição adicional do ficheiro..." rows="4" />
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <div class="flex items-center space-x-2">
                                    <Checkbox id="is-public" v-model:checked="formData.is_public" />
                                    <label for="is-public" class="text-sm font-medium cursor-pointer">
                                        Ficheiro Público
                                    </label>
                                </div>
                                <p class="text-sm text-muted-foreground pl-6">
                                    Se marcado, o ficheiro poderá ser acedido sem autenticação
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack">Cancelar</Button>
                    <Button type="submit" :disabled="isSubmitting || !selectedFile">
                        <UploadIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A enviar...' : 'Enviar Ficheiro' }}
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
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import { Input } from '@/components/ui/input';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, LoaderIcon, UploadIcon } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

const isSubmitting = ref(false);
const selectedFile = ref<File | null>(null);

const formData = reactive({
    name: '',
    description: '',
    document_type: '',
    is_public: false,
    expires_at: '',
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        selectedFile.value = target.files[0];
        
        // Auto-fill name if empty
        if (!formData.name) {
            formData.name = target.files[0].name;
        }
    }
};

const submitForm = () => {
    if (!selectedFile.value) {
        alert('Por favor selecione um ficheiro');
        return;
    }

    isSubmitting.value = true;

    const data = new FormData();
    data.append('file', selectedFile.value);
    data.append('name', formData.name);
    if (formData.description) data.append('description', formData.description);
    data.append('document_type', formData.document_type);
    if (formData.is_public) data.append('is_public', '1');
    if (formData.expires_at) data.append('expires_at', formData.expires_at);

    router.post('/digital-archive', data, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
};

const goBack = () => router.get('/digital-archive');

const formatBytes = (bytes: number) => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let i = 0;
    
    while (size > 1024 && i < units.length - 1) {
        size /= 1024;
        i++;
    }
    
    return `${size.toFixed(2)} ${units[i]}`;
};
</script>




