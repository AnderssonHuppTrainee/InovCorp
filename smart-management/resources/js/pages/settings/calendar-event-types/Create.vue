<template>
    <FormWrapper
        title="Novo Tipo de Evento"
        description="Criar um novo tipo de evento"
        :schema="calendarEventTypeSchema"
        :initial-values="initialValues"
        submit-url="/calendar-event-types"
        submit-method="post"
        submit-text="Criar Tipo de Evento"
    >
        <template #form-fields>
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
                        <Input type="color" v-bind="componentField" />
                    </FormControl>
                    <FormDescription>
                        Cor para identificar este tipo de evento
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>

            <CheckboxField
                name="is_active"
                label="Tipo Ativo"
                description="Este tipo de evento estará disponível para seleção"
            />
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import CheckboxField from '@/components/common/CheckboxField.vue';
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
import { calendarEventTypeSchema } from '@/schemas/calendarEventTypeSchema';
import { computed } from 'vue';

const initialValues = computed(() => ({
    name: '',
    color: '#3b82f6',
    is_active: true,
}));
</script>
