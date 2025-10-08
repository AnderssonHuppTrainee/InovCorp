<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                :title="type === 'client' ? 'Novo Cliente' : 'Novo Fornecedor'"
                :description="`Registar novo ${type === 'client' ? 'cliente' : 'fornecedor'} no sistema`"
            >
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <EntityForm
                :countries="countries"
                :type="type"
                submit-label="Criar"
                :on-submit="submitForm"
                @cancel="goBack"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import EntityForm from '@/components/entities/EntityForm.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import routeEntity from '@/routes/entities';
import { type BreadcrumbItem } from '@/types';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '#',
    },
];
const props = defineProps<{
    type: 'client' | 'supplier';
    countries: Array<{ id: number; name: string }>;
}>();

const submitting = ref(false);

const goBack = () => {
    window.history.back();
};

const submitForm = async (values: any) => {
    await router.post(routeEntity.store().url, values, {
        onSuccess: () => console.log('Entidade criada com sucesso!'),
    });
};
</script>
