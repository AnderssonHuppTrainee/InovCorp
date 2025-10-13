<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Editar Ficheiro" :description="`${archive.name}`">
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
                                <label class="text-sm font-medium">Nome *</label>
                                <Input v-model="formData.name" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Tipo de Documento *</label>
                                <Input v-model="formData.document_type" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Data de Expiração</label>
                                <DatePicker v-model="formData.expires_at" />
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Descrição</label>
                                <Textarea v-model="formData.description" rows="4" />
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <div class="flex items-center space-x-2">
                                    <Checkbox id="is-public-edit" v-model:checked="formData.is_public" />
                                    <label for="is-public-edit" class="text-sm font-medium cursor-pointer">
                                        Ficheiro Público
                                    </label>
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
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

interface Props {
    archive: any;
}

const props = defineProps<Props>();

const isSubmitting = ref(false);

const formData = reactive({
    name: props.archive.name,
    description: props.archive.description || '',
    document_type: props.archive.document_type,
    is_public: props.archive.is_public,
    expires_at: props.archive.expires_at || '',
});

const submitForm = () => {
    isSubmitting.value = true;
    router.put(`/digital-archive/${props.archive.id}`, formData, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
};

const goBack = () => router.get('/digital-archive');
</script>





