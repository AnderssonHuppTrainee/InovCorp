<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Novo Tipo de Evento"
                description="Criar um novo tipo de evento"
            >
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <Card>
                <CardContent class="pt-6">
                    <form @submit="onSubmit" class="space-y-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Reunião"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="color">
                            <FormItem>
                                <FormLabel>Cor</FormLabel>
                                <FormControl>
                                    <div class="flex gap-2">
                                        <Input
                                            type="color"
                                            v-bind="componentField"
                                            class="h-10 w-20"
                                        />
                                        <Input
                                            type="text"
                                            v-bind="componentField"
                                            placeholder="#3B82F6"
                                            class="font-mono"
                                        />
                                    </div>
                                </FormControl>
                                <FormDescription>
                                    Cor que será exibida no calendário (formato
                                    hexadecimal)
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <CheckboxField
                            name="is_active"
                            label="Tipo Ativo"
                            description="Este tipo estará disponível para seleção"
                        />

                        <div class="flex justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="handleCancel"
                            >
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <LoaderCircleIcon
                                    v-if="isSubmitting"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                Criar Tipo
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import CheckboxField from '@/components/common/CheckboxField.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import calendarEventTypes from '@/routes/calendar-event-types';
import {
    calendarEventTypeSchema,
    type CalendarEventTypeFormData,
} from '@/schemas/calendarEventTypeSchema';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { ArrowLeftIcon, LoaderCircleIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { ref } from 'vue';

const isSubmitting = ref(false);

const form = useForm<CalendarEventTypeFormData>({
    validationSchema: toTypedSchema(calendarEventTypeSchema),
    initialValues: {
        name: '',
        color: '#3B82F6',
        is_active: true,
    },
});

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;

    router.post(calendarEventTypes.store().url, values, {
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
});
const goBack = () => router.get('/calendar-event-types');
const handleCancel = () => {
    router.visit(calendarEventTypes.index().url);
};
</script>
