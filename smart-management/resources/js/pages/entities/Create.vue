<template>
    <FormWrapper
        ref="formWrapper"
        :title="type === 'client' ? 'Novo Cliente' : 'Novo Fornecedor'"
        :description="`Registar novo ${type === 'client' ? 'cliente' : 'fornecedor'} no sistema`"
        :schema="entitySchema"
        :initial-values="initialValues"
        submit-url="/entities"
        submit-text="Guardar Entidade"
    >
        <template #form-fields>
            <!-- Coluna 1 -->
            <div class="space-y-6">
                <!-- NIF com validação VIES -->
                <FormField
                    v-slot="{ componentField }"
                    name="tax_number"
                >
                    <FormItem>
                        <FormLabel>NIF/VAT *</FormLabel>
                        <FormControl>
                            <div class="flex gap-2">
                                <Input
                                    placeholder="PT123456789"
                                    v-bind="componentField"
                                    :class="{
                                        'border-green-500':
                                            vatValid,
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
                            Introduza o NIF/VAT e valide através
                            do VIES
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
                                    <div
                                        v-if="vatResult.address"
                                    >
                                        {{ vatResult.address }}
                                    </div>
                                </div>
                            </div>
                            <div
                                v-else
                                class="flex items-center"
                            >
                                <XCircleIcon
                                    class="mr-2 h-4 w-4 text-red-600"
                                />
                                <span>{{
                                    vatResult.error ||
                                    'NIF inválido'
                                }}</span>
                            </div>
                        </div>
                    </FormItem>
                </FormField>

                <!-- Nome -->
                <FormField
                    v-slot="{ componentField }"
                    name="name"
                >
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
                <FormField
                    v-slot="{ componentField }"
                    name="address"
                >
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
                    <FormField
                        v-slot="{ componentField }"
                        name="postal_code"
                    >
                        <FormItem>
                            <FormLabel
                                >Código Postal *</FormLabel
                            >
                            <FormControl>
                                <Input
                                    placeholder="1234-567"
                                    v-bind="componentField"
                                />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField
                        v-slot="{ componentField }"
                        name="city"
                    >
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
                                    {{ country.name }} ({{
                                        country.code
                                    }})
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
                    <FormField
                        v-slot="{ componentField }"
                        name="phone"
                    >
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

                    <FormField
                        v-slot="{ componentField }"
                        name="mobile"
                    >
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
                <FormField
                    v-slot="{ componentField }"
                    name="website"
                >
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

                <FormField
                    v-slot="{ componentField }"
                    name="email"
                >
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
                <div
                    class="flex flex-row items-start space-y-0 space-x-3 rounded-lg border p-4"
                >
                    <input
                        type="checkbox"
                        id="gdpr-consent"
                        :checked="form.values.gdpr_consent"
                        @change="handleGdprChange"
                        class="peer h-4 w-4 shrink-0 cursor-pointer rounded-sm border border-primary ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                    />
                    <div class="grid gap-1.5 leading-none">
                        <label
                            for="gdpr-consent"
                            class="cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                        >
                            Consentimento RGPD
                        </label>
                        <p
                            class="text-sm text-muted-foreground"
                        >
                            Autoriza o tratamento dos dados
                            pessoais de acordo com o RGPD
                        </p>
                    </div>
                </div>

                <!-- Estado -->
                <FormField
                    v-slot="{ componentField }"
                    name="status"
                >
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
                                <SelectItem value="active"
                                    >Ativo</SelectItem
                                >
                                <SelectItem value="inactive"
                                    >Inativo</SelectItem
                                >
                            </SelectContent>
                        </Select>
                        <FormDescription>
                            Entidades inativas não aparecem nas
                            listas de seleção
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Observações -->
                <FormField
                    v-slot="{ componentField }"
                    name="observations"
                >
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

<script setup>
import FormWrapper from '@/components/common/FormWrapper.vue';
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
import { Button } from '@/components/ui/button';
import { useViesValidation } from '@/composables/useViesValidation';
import { entitySchema } from '@/schemas/entitySchema';
import {
    CheckCircleIcon,
    LoaderIcon,
    SearchIcon,
    XCircleIcon,
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

// Props
const props = defineProps({
    type: String,
    countries: Array,
    defaultTypes: Array,
});

// Refs
const { vatLoading, vatValid, vatResult, error, validateVat } =
    useViesValidation();

// FormWrapper ref
const formWrapper = ref(null);

// Initial values
const initialValues = computed(() => ({
    tax_number: '',
    name: '',
    types: props.defaultTypes || [],
    address: '',
    postal_code: '',
    city: '',
    country_id: '',
    phone: '',
    mobile: '',
    website: '',
    email: '',
    gdpr_consent: false,
    observations: '',
    status: 'active',
}));

// Computed
const isClient = computed(() => formWrapper.value?.form.values.types.includes('client'));
const isSupplier = computed(() => formWrapper.value?.form.values.types.includes('supplier'));

// Methods
const handleGdprChange = (e) => {
    const checked = e.target.checked;
    formWrapper.value?.form.setFieldValue('gdpr_consent', checked);
};

const handleValidateVat = async () => {
    const vatNumber = formWrapper.value?.form.values.tax_number?.trim();
    if (!vatNumber) return;

    const result = await validateVat(vatNumber);
    if (result?.valid) {
        // preenche os campos automaticamente
        if (result.name) formWrapper.value?.form.setFieldValue('name', result.name);
        if (result.address) formWrapper.value?.form.setFieldValue('address', result.address);
        if (result.postal_code)
            formWrapper.value?.form.setFieldValue('postal_code', result.postal_code);
        if (result.city) formWrapper.value?.form.setFieldValue('city', result.city);
    }
};

// Auto-select type based on menu
onMounted(() => {
    console.log('Props defaultTypes:', props.defaultTypes);
    if (props.defaultTypes && props.defaultTypes.length > 0) {
        console.log('Definindo tipos padrão:', props.defaultTypes);
        // Wait for FormWrapper to be mounted
        setTimeout(() => {
            formWrapper.value?.form.setFieldValue('types', props.defaultTypes);
        }, 100);
    }

    // Log dos valores iniciais
    setTimeout(() => {
        console.log('Valores do form após mount:', formWrapper.value?.form.values);
    }, 200);
});
</script>
