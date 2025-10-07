<script setup>
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '../ui/form';

import { Input } from '@/components/ui/input';
import { Textarea } from '../ui/textarea';

defineProps({
    form: Object,
    countries: Array,
});
</script>
<template>
    <FormField v-slot="{ componentField }" name="address">
        <FormItem>
            <FormLabel>Morada *</FormLabel>
            <FormControl>
                <Textarea
                    placeholder="Morada completa"
                    v-bind="componentField"
                    rows="3"
                />
            </FormControl>
            <FormMessage />
        </FormItem>
    </FormField>

    <!-- Código Postal e Localidade -->
    <div class="grid grid-cols-2 gap-4">
        <FormField v-slot="{ componentField }" name="postal_code">
            <FormItem>
                <FormLabel>Código Postal *</FormLabel>
                <FormControl>
                    <Input
                        placeholder="1234-567"
                        v-bind="componentField"
                        v-mask="'####-###'"
                    />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="city">
            <FormItem>
                <FormLabel>Localidade *</FormLabel>
                <FormControl>
                    <Input placeholder="Cidade" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>
    </div>

    <FormField v-slot="{ componentField }" name="country_id">
        <FormItem>
            <FormLabel>País *</FormLabel>
            <Select v-bind="componentField">
                <FormControl>
                    <SelectTrigger>
                        <SelectValue placeholder="Selecione um país" />
                    </SelectTrigger>
                </FormControl>
                <SelectContent>
                    <SelectItem
                        v-for="country in countries"
                        :key="country.id"
                        :value="country.id"
                    >
                        {{ country.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <FormMessage />
        </FormItem>
    </FormField>
</template>
