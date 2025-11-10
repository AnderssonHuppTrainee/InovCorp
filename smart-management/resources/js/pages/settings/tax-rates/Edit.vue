<template>
    <FormWrapper
        title="Editar Taxa de IVA"
        :description="`${taxRate.name} (${taxRate.rate}%)`"
        :schema="taxRateSchema"
        :initial-values="initialValues"
        :submit-url="`/tax-rates/${taxRate.id}`"
        submit-method="put"
        submit-text="Atualizar Taxa"
    >
        <template #form-fields>
            <div class="space-y-6">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome *</FormLabel>
                        <FormControl>
                            <Input v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="rate">
                    <FormItem>
                        <FormLabel>Taxa (%) *</FormLabel>
                        <FormControl>
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <CheckboxField name="is_active" label="Taxa Ativa" />
            </div>
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import CheckboxField from '@/components/common/CheckboxField.vue';
import FormWrapper from '@/components/common/FormWrapper.vue';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { taxRateSchema } from '@/schemas/taxRateSchema';
import { computed } from 'vue';

interface Props {
    taxRate: any;
}

const props = defineProps<Props>();

const initialValues = computed(() => ({
    name: props.taxRate.name,
    rate: props.taxRate.rate,
    is_active: props.taxRate.is_active,
}));
</script>
