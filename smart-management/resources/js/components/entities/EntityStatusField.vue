<script setup>
import { Checkbox } from '@/components/ui/checkbox';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

defineProps({
    form: Object,
});
</script>

<template>
    <FormField v-slot="{ componentField }" name="status">
        <FormItem>
            <FormLabel>Estado *</FormLabel>
            <Select v-model="form.status" v-bind="componentField">
                <FormControl>
                    <SelectTrigger>
                        <SelectValue placeholder="Selecione o estado" />
                    </SelectTrigger>
                </FormControl>
                <SelectContent>
                    <SelectItem value="active">Ativo</SelectItem>
                    <SelectItem value="inactive">Inativo</SelectItem>
                </SelectContent>
            </Select>
            <FormDescription>
                Entidades inativas não aparecem nas listas de seleção
            </FormDescription>
            <FormMessage />
        </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="observations">
        <FormItem>
            <FormLabel>Observações</FormLabel>
            <FormControl>
                <Textarea
                    placeholder="Notas internas sobre a entidade"
                    v-model="form.observations"
                    v-bind="componentField"
                    rows="3"
                />
            </FormControl>
            <FormMessage />
        </FormItem>
    </FormField>

    <!-- consentimento RGPD -->
    <FormField v-slot="{ componentField }" name="gdpr_consent">
        <FormItem
            class="flex flex-row items-start space-y-0 space-x-3 rounded-lg border p-4"
        >
            <FormControl>
                <Checkbox v-bind="componentField" v-model="form.gdpr_consent" />
            </FormControl>
            <div class="space-y-1 leading-none">
                <FormLabel>Consentimento RGPD</FormLabel>
                <FormDescription>
                    Autoriza o tratamento dos dados pessoais de acordo com o
                    RGPD
                </FormDescription>
            </div>
        </FormItem>
        <FormMessage />
    </FormField>
</template>
