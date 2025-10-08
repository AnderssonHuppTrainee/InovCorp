<template>
    <Card>
        <CardContent class="p-6">
            <Form @submit="onSubmit">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Coluna 1 -->
                    <div class="space-y-6">
                        <EntityVatField
                            v-model="form.tax_number"
                            @vat-data="fillFromVat"
                        />

                        <!-- Nome -->
                        <FormField name="name">
                            <FormItem>
                                <FormLabel>Nome *</FormLabel>
                                <FormControl>
                                    <Input
                                        placeholder="Nome da entidade"
                                        v-model="form.name"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!--morada-->
                        <FormField name="address">
                            <FormItem>
                                <FormLabel>Morada *</FormLabel>
                                <FormControl>
                                    <Textarea
                                        placeholder="Morada completa"
                                        v-model="form.address"
                                        rows="3"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Código Postal e Localidade -->
                        <div class="grid grid-cols-2 gap-4">
                            <FormField name="postal_code">
                                <FormItem>
                                    <FormLabel>Código Postal *</FormLabel>
                                    <FormControl>
                                        <Input
                                            placeholder="1234-567"
                                            v-model="form.postal_code"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField name="city">
                                <FormItem>
                                    <FormLabel>Localidade *</FormLabel>
                                    <FormControl>
                                        <Input
                                            placeholder="Cidade"
                                            v-model="form.city"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>

                        <FormField
                            v-slot="{ componentField }"
                            name="country_id"
                        >
                            <FormItem>
                                <FormLabel>País *</FormLabel>
                                <Select v-bind="componentField">
                                    <FormControl>
                                        <SelectTrigger>
                                            <SelectValue
                                                placeholder="Selecione um país"
                                            />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="country in countries"
                                            :key="country.id"
                                            :value="String(country.id)"
                                        >
                                            {{ country.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <EntityTypeSelector />
                    </div>

                    <!-- Coluna 2 -->
                    <div class="space-y-6">
                        <EntityContactField />
                        <EntityStatusField />
                    </div>
                </div>

                <!-- Botões -->
                <div class="mt-6 flex justify-end gap-3 border-t pt-6">
                    <Button
                        type="button"
                        variant="outline"
                        @click="$emit('cancel')"
                        >Cancelar</Button
                    >
                    <Button type="submit" :disabled="isSubmitting">
                        <LoaderIcon
                            v-if="isSubmitting"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        <SaveIcon v-else class="mr-2 h-4 w-4" />
                        {{ isSubmitting ? 'A guardar...' : submitLabel }}
                    </Button>
                </div>
            </Form>
        </CardContent>
    </Card>
</template>

<script setup>
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { entitySchema } from '@/schemas/entitySchema';
import { toTypedSchema } from '@vee-validate/zod';
import { LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import EntityContactField from './EntityContactField.vue';
import EntityStatusField from './EntityStatusField.vue';
import EntityTypeSelector from './EntityTypeSelector.vue';
import EntityVatField from './EntityVatField.vue';

const props = defineProps({
    countries: Array,
    type: String,
    submitLabel: {
        type: String,
        default: 'Guardar Entidade',
    },
    onSubmit: Function,
    initialData: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    tax_number: '',
    name: '',
    address: '',
    postal_code: '',
    city: '',
});
const emit = defineEmits(['cancel']);

// Schema de validação
const schema = toTypedSchema(entitySchema);

// Handler de submissão
const onSubmit = handleSubmit((values) => {
    if (props.onSubmit) {
        props.onSubmit(values);
    }
});
</script>
