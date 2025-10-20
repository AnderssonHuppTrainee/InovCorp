<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <!-- Header com título, descrição e ações -->
            <PageHeader :title="title" :description="description">
                <template #actions>
                    <slot name="header-actions">
                        <Button variant="outline" @click="handleCancel">
                            <ArrowLeftIcon class="mr-2 h-4 w-4" />
                            Voltar
                        </Button>
                    </slot>
                </template>
            </PageHeader>

            <!-- Formulário -->
            <form @submit="handleSubmit">
                <!-- Card principal do formulário -->
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <!-- Grid responsivo para campos -->
                        <div :class="gridClass">
                            <!-- Slot para campos do formulário -->
                            <slot name="form-fields" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Botões de ação -->
                <div class="flex justify-end gap-3">
                    <Button
                        type="button"
                        variant="outline"
                        @click="handleCancel"
                        :disabled="isSubmitting"
                    >
                        Cancelar
                    </Button>
                    <Button
                        type="submit"
                        :disabled="isSubmitting"
                    >
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ submitText }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';

// Components
import AppLayout from '@/layouts/AppLayout.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

// Types
interface Props {
    title: string;
    description?: string;
    schema: any; // Zod schema
    initialValues?: Record<string, any>;
    submitUrl: string;
    submitMethod?: 'post' | 'put' | 'patch';
    submitText?: string;
    cancelUrl?: string;
    gridCols?: 1 | 2; // Grid columns (default: 2)
    onSuccess?: () => void;
    onError?: (errors: any) => void;
}

const props = withDefaults(defineProps<Props>(), {
    description: '',
    submitMethod: 'post',
    submitText: 'Guardar',
    gridCols: 2,
    onSuccess: () => {},
    onError: () => {},
});

// State
const isSubmitting = ref(false);

// Form setup
const form = useForm({
    validationSchema: toTypedSchema(props.schema),
    initialValues: props.initialValues ?? {},
});

// Computed
const gridClass = computed(() => {
    return props.gridCols === 1 
        ? 'grid grid-cols-1 gap-6'
        : 'grid grid-cols-1 gap-6 lg:grid-cols-2';
});

const cancelUrl = computed(() => {
    return props.cancelUrl ?? extractBaseUrl(props.submitUrl);
});

// Methods
const handleSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;
    
    const method = props.submitMethod;
    const url = props.submitUrl;
    
    router[method](url, values, {
        preserveScroll: true,
        onSuccess: () => {
            props.onSuccess();
        },
        onError: (errors) => {
            props.onError(errors);
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
});

const handleCancel = () => {
    router.get(cancelUrl.value);
};

// Helper function to extract base URL from submit URL
function extractBaseUrl(url: string): string {
    // Remove last segment if it's numeric (ID for edit)
    const segments = url.split('/').filter(Boolean);
    if (segments.length > 1 && /^\d+$/.test(segments[segments.length - 1])) {
        segments.pop();
    }
    return '/' + segments.join('/');
}

// Expose form for parent components
defineExpose({
    form,
    isSubmitting,
});
</script>