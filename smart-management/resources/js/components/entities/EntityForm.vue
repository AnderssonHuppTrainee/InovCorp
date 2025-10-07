<template>
    <Card>
        <CardContent class="p-6">
            <Form @submit="submitForm">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Coluna 1 -->
                    <div class="space-y-6">
                        <EntityVatField :form="form" />
                        <!--nome-->
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nome *</FormLabel>
                                <FormControl>
                                    <Input
                                        placeholder="Nome da entidade"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <EntityAddressField
                            :form="form"
                            :countries="countries"
                        />
                        <EntityTypeSelector :form="form" />
                    </div>

                    <!-- Coluna 2 -->
                    <div class="space-y-6">
                        <EntityContactField :form="form" />
                        <EntityStatusField :form="form" />
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
                    <Button type="submit" :disabled="form.processing">
                        <LoaderIcon
                            v-if="form.processing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        <SaveIcon v-else class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'A guardar...' : submitLabel }}
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
import { LoaderIcon, SaveIcon } from 'lucide-vue-next';

import EntityAddressField from './EntityAddressField.vue';
import EntityContactField from './EntityContactField.vue';
import EntityStatusField from './EntityStatusField.vue';
import EntityTypeSelector from './EntityTypeSelector.vue';
import EntityVatField from './EntityVatField.vue';

const props = defineProps({
    form: Object,
    countries: Array,
    type: String,
    submitLabel: {
        type: String,
        default: 'Guardar Entidade',
    },
    onSubmit: Function,
});

const emit = defineEmits(['cancel']);

const submitForm = () => {
    if (props.onSubmit) {
        props.onSubmit(props.form);
    }
};
</script>
