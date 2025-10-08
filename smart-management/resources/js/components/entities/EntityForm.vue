<template>
    <Card>
        <CardContent class="p-6">
            <Form @submit="form.handleSubmit(handleFormSubmit)">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Coluna 1 -->
                    <div class="space-y-6">
                        <FormField
                            v-slot="{ componentField }"
                            name="tax_number"
                        >
                            <EntityVatField
                                v-bind="componentField"
                                @vat-data="fillFromVat"
                            />
                        </FormField>

                        <!-- Nome -->
                        <FormField name="name">
                            <FormItem>
                                <FormLabel>Nome *</FormLabel>
                                <FormControl>
                                    <Input
                                        placeholder="Nome da entidade"
                                        v-model="form.values.name"
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
                                        v-model="form.values.address"
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
                                            v-model="form.values.postal_code"
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
                                            v-model="form.values.city"
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
                    >
                        Cancelar
                    </Button>

                    <Button type="submit" :disabled="submitting">
                        <SaveIcon v-if="!submitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ submitting ? 'A guardar...' : submitLabel }}
                    </Button>
                </div>
            </Form>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
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
import { Textarea } from '@/components/ui/textarea';
import { entitySchema } from '@/schemas/entitySchema';
import { toTypedSchema } from '@vee-validate/zod';
import { LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { ref } from 'vue';
import EntityContactField from './EntityContactField.vue';
import EntityStatusField from './EntityStatusField.vue';
import EntityTypeSelector from './EntityTypeSelector.vue';
import EntityVatField from './EntityVatField.vue';

interface Country {
    id: number | string;
    name: string;
}
interface EntityFormValues {
    tax_number: string;
    name: string;
    address: string;
    postal_code: string;
    city: string;
    country_id: string;
}

const props = defineProps<{
    countries: Country[];
    submitLabel?: string;
    initialData?: Partial<EntityFormValues>;
    onSubmit?: (values: any) => void;
}>();

const emit = defineEmits(['cancel']);

// Schema
const schema = toTypedSchema(entitySchema);

// Formulário
const form = useForm<EntityFormValues>({
    validationSchema: schema,
    initialValues: {
        tax_number: '',
        name: '',
        address: '',
        postal_code: '',
        city: '',
        country_id: '',
        ...props.initialData,
    },
});

const submitting = ref(false);

const handleFormSubmit = async (values: EntityFormValues) => {
    submitting.value = true;
    try {
        if (props.onSubmit) await props.onSubmit(values);
    } finally {
        submitting.value = false;
    }
};

// Handler VAT
const fillFromVat = (data: any) => {
    if (data.name) form.setFieldValue('name', data.name);
    if (data.address) form.setFieldValue('address', data.address);
    if (data.postal_code) form.setFieldValue('postal_code', data.postal_code);
    if (data.city) form.setFieldValue('city', data.city);
};
</script>
