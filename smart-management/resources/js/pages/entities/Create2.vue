<template>
    <AppLayout>
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
                :on-submit="submitForm"
                submitLabel="Criar Entidade"
                @cancel="goBack"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import EntityForm from '@/components/entities/EntityForm.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { useEntityForm } from '@/composables/useEntityForm';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon } from 'lucide-vue-next';
import { onMounted } from 'vue';

// Props
const props = defineProps({
    type: String,
    countries: Array,
    defaultTypes: Array,
});

const { goBack } = useEntityForm(props.defaultTypes, props.type);

// Auto-select type based on menu
onMounted(() => {
    // Inicializações específicas da página, se necessário
});

function submitForm(formData) {
    // Submissão via Inertia para o endpoint store do EntityController
    router.post('/entities', formData, {
        onSuccess: () => {
            // Redirecionamento será feito pelo backend
        },
        onError: (errors) => {
            // Erros serão exibidos automaticamente pelo Inertia
            console.error('Erro ao criar entidade:', errors);
        },
    });
}
</script>
