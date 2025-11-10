<template>
    <FormWrapper
        title="Editar País"
        :description="`Editar ${country.name}`"
        :schema="countrySchema"
        :initial-values="initialValues"
        :submit-url="`/countries/${country.id}`"
        submit-method="put"
        submit-text="Atualizar País"
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
import { computed } from 'vue';

interface Props {
    country: {
        id: number;
        name: string;
        code: string;
        is_active: boolean;
    };
}

const props = defineProps<Props>();

const initialValues = computed(() => ({
    name: props.country.name,
    code: props.country.code,
    is_active: props.country.is_active,
}));
</script>
