<template>
    <FormItem>
        <FormLabel>NIF *</FormLabel>
        <FormControl>
            <div class="flex gap-2">
                <Input
                    :value="modelValue"
                    @input="emit('update:modelValue', $event.target.value)"
                    placeholder="PT123456789"
                    :class="{ 'border-green-500': vatValid }"
                />
                <Button
                    type="button"
                    variant="outline"
                    @click="handleValidate"
                    :disabled="vatLoading"
                    class="whitespace-nowrap"
                >
                    <SearchIcon v-if="!vatLoading" class="mr-2 h-4 w-4" />
                    <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                    {{ vatLoading ? 'A validar...' : 'Validar VIES' }}
                </Button>
            </div>
        </FormControl>
        <FormDescription>
            Introduza o NIF e valide através do VIES
        </FormDescription>

        <div
            v-if="vatResult"
            class="mt-2 rounded-md p-3 text-sm"
            :class="
                vatResult.valid
                    ? 'border border-green-200 bg-green-50 text-green-800'
                    : 'border border-red-200 bg-red-50 text-red-800'
            "
        >
            <div v-if="vatResult.valid" class="flex items-center">
                <CheckCircleIcon class="mr-2 h-4 w-4 text-green-600" />
                <div>
                    <strong>NIF Válido</strong>
                    <div v-if="vatResult.name">{{ vatResult.name }}</div>
                    <div v-if="vatResult.address">{{ vatResult.address }}</div>
                </div>
            </div>
            <div v-else class="flex items-center">
                <XCircleIcon class="mr-2 h-4 w-4 text-red-600" />
                <span>{{ vatResult.error || 'NIF inválido' }}</span>
            </div>
        </div>
    </FormItem>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    FormControl,
    FormDescription,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useViesValidation } from '@/composables/useViesValidation';
import {
    CheckCircleIcon,
    LoaderIcon,
    SearchIcon,
    XCircleIcon,
} from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string;
    'onUpdate:modelValue'?: (value: string) => void;
}>();
const emit = defineEmits(['update:modelValue', 'vatData']);

const { vatLoading, vatResult, vatValid, validateVat } = useViesValidation();

const handleValidate = async () => {
    const result = await validateVat(props.modelValue);
    if (result && result.valid) emit('vatData', result);
};
</script>
