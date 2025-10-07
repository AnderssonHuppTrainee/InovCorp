<template>
    <FormField v-slot="{ componentField }" name="tax_number">
        <FormItem>
            <FormLabel>NIF *</FormLabel>
            <FormControl>
                <div class="flex gap-2">
                    <Input
                        v-bind="componentField"
                        @blur="validateVat"
                        :class="vatClass"
                    />
                    <Button
                        type="button"
                        variant="outline"
                        @click="validateVat"
                        :disabled="loading"
                    >
                        <LoaderIcon
                            v-if="loading"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        <SearchIcon v-else class="mr-2 h-4 w-4" />
                        {{ loading ? 'A validar...' : 'Validar VIES' }}
                    </Button>
                </div>
            </FormControl>
            <FormDescription
                >Introduza o NIF e valide através do VIES</FormDescription
            >
            <FormMessage />
            <VatResult :result="vatResult" />
        </FormItem>
    </FormField>

    <!-- NIF com validação VIES -->
    <FormField v-slot="{ componentField }" name="tax_number">
        <FormItem>
            <FormLabel>NIF *</FormLabel>
            <FormControl>
                <div class="flex gap-2">
                    <Input
                        placeholder="PT123456789"
                        v-bind="componentField"
                        @blur="validateVat"
                        :class="{
                            'border-green-500': vatValid,
                        }"
                    />
                    <Button
                        type="button"
                        variant="outline"
                        @click="validateVat"
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
            <FormMessage />

            <!-- Resultado VIES -->
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
                        <div v-if="vatResult.name">
                            {{ vatResult.name }}
                        </div>
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

<script setup>
import { Button } from '@/components/ui/button';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useViesValidation } from '@/composables/useViesValidation';
import { LoaderIcon, SearchIcon } from 'lucide-vue-next';
import VatResult from './VatResult.vue';

const props = defineProps({ form: Object });
const { validateVat, vatResult, loading, vatClass } = useViesValidation(
    props.form,
);
</script>
