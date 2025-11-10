<template>
    <FormWrapper
        ref="formWrapper"
        :title="type === 'client' ? 'Editar Cliente' : 'Editar Fornecedor'"
        :description="`Atualizar dados ${type === 'client' ? 'do cliente' : 'do fornecedor'}`"
        :schema="entitySchema"
        :initial-values="initialValues"
        :submit-url="`/entities/${entity.id}`"
        submit-method="put"
        submit-text="Atualizar Entidade"
        :cancel-url="
            route.index().url +
            `?type=${type === 'client' ? 'client' : 'supplier'}`
        "
    >
        <template #form-fields>
            <!-- Coluna 1 -->
            <div class="space-y-6">
                <!-- NIF com validação VIES -->
                <FormField v-slot="{ componentField }" name="tax_number">
                    <FormItem>
                        <FormLabel>NIF/VAT *</FormLabel>
                        <FormControl>
                            <div class="flex gap-2">
                                <Input
                                    placeholder="PT123456789"
                                    v-bind="componentField"
                                    :class="{
                                        'border-green-500': vatValid,
                                    }"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="handleValidateVat"
                                    :disabled="vatLoading"
                                    class="whitespace-nowrap"
                                >
                                    <SearchIcon
                                        v-if="!vatLoading"
                                        class="mr-2 h-4 w-4"
                                    />
                                    <LoaderIcon
                                        v-else
                                        class="mr-2 h-4 w-4 animate-spin"
                                    />
                                    {{
                                        vatLoading
                                            ? 'A validar...'
                                            : 'Validar VIES'
                                    }}
                                </Button>
                            </div>
                        </FormControl>
                        <FormDescription>
                            Introduza o NIF/VAT e valide através do VIES
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
                            <div
                                v-if="vatResult.valid"
                                class="flex items-center"
                            >
                                <CheckCircleIcon
                                    class="mr-2 h-4 w-4 text-green-600"
                                />
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
                                <XCircleIcon
                                    class="mr-2 h-4 w-4 text-red-600"
                                />
                                <span>{{
                                    vatResult.error || 'NIF inválido'
                                }}</span>
                            </div>
                        </div>
                    </FormItem>
                </FormField>

                <!-- Nome -->
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Nome *</FormLabel>
                        <FormControl>
                            <Input
                                placeholder="Nome da empresa"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Morada -->
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
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="city">
                        <FormItem>
                            <FormLabel>Localidade *</FormLabel>
                            <FormControl>
                                <Input
                                    placeholder="Cidade"
                                    v-bind="componentField"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <!-- País -->
                <FormField v-slot="{ componentField }" name="country_id">
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
                                    {{ country.name }} ({{ country.code }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </div>

            <!-- Coluna 2: Contactos e Outros -->
            <div class="space-y-6">
                <!-- Contactos -->
                <div class="grid grid-cols-2 gap-4">
                    <FormField v-slot="{ componentField }" name="phone">
                        <FormItem>
                            <FormLabel>Telefone</FormLabel>
                            <FormControl>
                                <Input
                                    placeholder="+351 123 456 789"
                                    v-bind="componentField"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="mobile">
                        <FormItem>
                            <FormLabel>Telemóvel</FormLabel>
                            <FormControl>
                                <Input
                                    placeholder="+351 912 345 678"
                                    v-bind="componentField"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <!-- Website e Email -->
                <FormField v-slot="{ componentField }" name="website">
                    <FormItem>
                        <FormLabel>Website</FormLabel>
                        <FormControl>
                            <Input
                                placeholder="https://exemplo.pt"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input
                                type="email"
                                placeholder="empresa@exemplo.pt"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Consentimento RGPD -->
                <CheckboxField
                    name="gdpr_consent"
                    label="Consentimento RGPD"
                    description=" Autoriza o tratamento dos dados pessoais de acordo
                            com o RGPD"
                />

                <!-- Estado -->
                <FormField v-slot="{ componentField }" name="status">
                    <FormItem>
                        <FormLabel>Estado *</FormLabel>
                        <Select v-bind="componentField">
                            <FormControl>
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder="Selecione o estado"
                                    />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="active">Ativo</SelectItem>
                                <SelectItem value="inactive"
                                    >Inativo</SelectItem
                                >
                            </SelectContent>
                        </Select>
                        <FormDescription>
                            Entidades inativas não aparecem nas listas de
                            seleção
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Observações -->
                <FormField v-slot="{ componentField }" name="observations">
                    <FormItem>
                        <FormLabel>Observações</FormLabel>
                        <FormControl>
                            <Textarea
                                placeholder="Notas internas sobre a entidade"
                                v-bind="componentField"
                                rows="3"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </div>
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import CheckboxField from '@/components/common/CheckboxField.vue';
import FormWrapper from '@/components/common/FormWrapper.vue';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useViesValidation } from '@/composables/useViesValidation';
import route from '@/routes/entities';
import { entitySchema } from '@/schemas/entitySchema';
import type { Country, Entity } from '@/types';
import {
    CheckCircleIcon,
    LoaderIcon,
    SearchIcon,
    XCircleIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { ComponentPublicInstance } from 'vue';
import type { FormContext } from 'vee-validate';

interface Props {
    type: 'client' | 'supplier';
    countries: Country[];
    entity: Entity;
}

interface FormWrapperInstance extends ComponentPublicInstance {
    form: FormContext<any>;
    isSubmitting: { value: boolean };
}

const props = defineProps<Props>();

// Refs
const { vatLoading, vatValid, vatResult, error, validateVat } =
    useViesValidation();

// FormWrapper ref
const formWrapper = ref<FormWrapperInstance | null>(null);

// Initial values - Preenchido com dados da entidade
const initialValues = computed(() => ({
    tax_number: String(props.entity.tax_number || ''),
    name: props.entity.name || '',
    types: props.entity.types || [],
    address: props.entity.address || '',
    postal_code: props.entity.postal_code || '',
    city: props.entity.city || '',
    country_id: String(props.entity.country_id || ''),
    phone: props.entity.phone || '',
    mobile: props.entity.mobile || '',
    website: props.entity.website || '',
    email: props.entity.email || '',
    gdpr_consent: props.entity.gdpr_consent || false,
    observations: props.entity.observations || '',
    status: props.entity.status || 'active',
}));

const handleValidateVat = async () => {
    const vatNumber = formWrapper.value?.form.values.tax_number?.trim();
    if (!vatNumber) return;

    const result = await validateVat(vatNumber);
    if (result?.valid) {
        // preenche os campos automaticamente
        if (result.name)
            formWrapper.value?.form.setFieldValue('name', result.name);
        if (result.address)
            formWrapper.value?.form.setFieldValue('address', result.address);
        if (result.postal_code)
            formWrapper.value?.form.setFieldValue(
                'postal_code',
                result.postal_code,
            );
        if (result.city)
            formWrapper.value?.form.setFieldValue('city', result.city);
    }
};
</script>
