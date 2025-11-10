<template>
    <FormWrapper
        title="Nova Função de Contacto"
        description="Criar uma nova função de contacto"
        :schema="contactRoleSchema"
        :initial-values="initialValues"
        submit-url="/contact-roles"
        submit-method="post"
        submit-text="Criar Função"
    >
        <template #form-fields>
            <div class="space-y-6">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                placeholder="Gerente"
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
                                placeholder="Descrição da função..."
                                v-bind="componentField"
                                rows="3"
                            />
                        </FormControl>
                        <FormDescription>
                            Descrição opcional da função
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <CheckboxField
                    name="is_active"
                    label="Função Ativa"
                    description="Esta função estará disponível para seleção"
                />
            </div>
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
import { contactRoleSchema } from '@/schemas/contactRoleSchema';
import { computed } from 'vue';

const initialValues = computed(() => ({
    is_active: true,
}));
</script>
