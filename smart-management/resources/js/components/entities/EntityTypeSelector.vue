<script setup>
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

defineProps({
    form: Object,
});
</script>

<template>
    <div>
        <FormField v-slot="{ componentField }" name="types">
            <FormItem>
                <FormLabel>Tipo *</FormLabel>
                <FormControl>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                value="client"
                                v-model="form.types"
                                v-bind="componentField"
                            />
                            Cliente
                        </label>
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                value="supplier"
                                v-model="form.types"
                                v-bind="componentField"
                            />
                            Fornecedor
                        </label>
                    </div>
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>
    </div>
</template>
