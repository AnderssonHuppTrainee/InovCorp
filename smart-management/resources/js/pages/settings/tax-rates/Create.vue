<template>
    <FormWrapper
        title="Nova Taxa de IVA"
        description="Criar nova taxa de IVA no sistema"
        :schema="taxRateSchema"
        :initial-values="{ name: '', rate: 0, is_active: true }"
        submit-url="/tax-rates"
        submit-text="Criar Taxa"
        submit-method="post"
    >
        <template #form-fields>
            <div class="space-y-6">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome *</FormLabel>
                        <FormControl>
                            <Input
                                v-bind="componentField"
                                placeholder="Ex: Normal, Reduzida, Isenta"
                            />
                        </FormControl>
                        <FormDescription>
                            Nome descritivo da taxa (ex: Normal, Reduzida,
                            Isenta)
                        </FormDescription>
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
                                placeholder="23.00"
                            />
                        </FormControl>
                        <FormDescription>
                            Percentagem da taxa de IVA (0-100)
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <CheckboxField
                    name="is_active"
                    label="Taxa Ativa"
                    description="Marque se a taxa está ativa para uso"
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
import { taxRateSchema } from '@/schemas/taxRateSchema';
</script>
