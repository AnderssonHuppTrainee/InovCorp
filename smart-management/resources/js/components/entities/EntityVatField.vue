<template>
    <FormField name="tax_number">
        <FormItem>
            <FormLabel>NIF *</FormLabel>
            <FormControl>
                <div class="flex gap-2">
                    <Input
                        v-model="localValue"
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

            <!-- Resultado -->
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
                        <div v-if="vatResult.address">
                            {{ vatResult.address }}
                        </div>
                    </div>
                </div>
                <div v-else class="flex items-center">
                    <XCircleIcon class="mr-2 h-4 w-4 text-red-600" />
                    <span>{{ vatResult.error || 'NIF inválido' }}</span>
                </div>
            </div>
        </FormItem>
    </FormField>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    FormControl,
    FormDescription,
    FormField,
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
import { defineEmits, defineProps, ref, watch } from 'vue';

const props = defineProps<{ modelValue: string }>();
const emit = defineEmits(['update:modelValue', 'vatData']);

const localValue = ref(props.modelValue);
const { vatLoading, vatResult, vatValid, validateVat } = useViesValidation();

watch(
    () => props.modelValue,
    (val) => (localValue.value = val),
);
watch(localValue, (val) => emit('update:modelValue', val));

const handleValidate = async () => {
    const result = await validateVat(localValue.value);
    if (result && result.valid) emit('vatData', result);
};
</script>
