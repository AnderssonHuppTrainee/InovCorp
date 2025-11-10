<template>
    <FormWrapper
        title="Novo País"
        description="Criar um novo país"
        :schema="countrySchema"
        :initial-values="{ is_active: true }"
        submit-url="/countries"
        submit-method="post"
        submit-text="Criar País"
    >
        <template #form-fields>
            <div class="space-y-6">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                placeholder="Portugal"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="code">
                    <FormItem>
                        <FormLabel>Código</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                placeholder="PT"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormDescription>
                            Código ISO do país (ex: PT, ES, FR)
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <CheckboxField
                    name="is_active"
                    label="País Ativo"
                    description="Este país estará disponível para seleção"
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
import { countrySchema } from '@/schemas/countrySchema';
</script>
