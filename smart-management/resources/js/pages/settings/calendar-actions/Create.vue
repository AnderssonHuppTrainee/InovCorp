<template>
    <FormWrapper
        title="Nova Ação de Calendário"
        description="Criar uma nova ação"
        :schema="calendarActionSchema"
        :initial-values="initalValues"
        submit-url="/calendar-actions"
        submit-method="post"
        submit-text="Criar Ação"
    >
        <template #form-fields>
            <FormField v-slot="{ componentField }" name="name">
                <FormItem>
                    <FormLabel>Nome</FormLabel>
                    <FormControl>
                        <Input
                            type="text"
                            placeholder="Ligação"
                            v-bind="componentField"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="description">
                <FormItem>
                    <FormLabel>Descrição</FormLabel>
                    <FormControl>
                        <Textarea
                            placeholder="Descrição da ação..."
                            v-bind="componentField"
                            rows="3"
                        />
                    </FormControl>
                    <FormDescription>
                        Descrição opcional da ação
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>

            <CheckboxField
                name="is_active"
                label="Ação Ativa"
                description="Esta ação estará disponível para seleção"
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
import { Textarea } from '@/components/ui/textarea';
import { calendarActionSchema } from '@/schemas/calendarActionSchema';
import { computed } from 'vue';

const initalValues = computed(() => ({
    name: '',
    description: '',
    is_active: true,
}));
</script>
