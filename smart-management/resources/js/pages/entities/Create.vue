<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="type === 'client' ? 'Novo Cliente' : 'Novo Fornecedor'"
                :description="`Registar novo ${type === 'client' ? 'cliente' : 'fornecedor'} no sistema`"
            >
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <Card>
                <CardContent class="p-6">
                    <Form @submit="submitForm">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- Coluna 1: Dados Básicos -->
                            <div class="space-y-6">
                                <!-- Tipo (hidden mas necessário) -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="types"
                                >
                                    <FormItem class="hidden">
                                        <FormControl>
                                            <Input
                                                type="hidden"
                                                v-bind="componentField"
                                            />
                                        </FormControl>
                                    </FormItem>
                                </FormField>

                                <!-- NIF com validação VIES -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="tax_number"
                                >
                                    <FormItem>
                                        <FormLabel>NIF *</FormLabel>
                                        <FormControl>
                                            <div class="flex gap-2">
                                                <Input
                                                    placeholder="PT123456789"
                                                    v-model="form.tax_number"
                                                    @blur="validateVat"
                                                    :class="{
                                                        'border-green-500':
                                                            vatValid,
                                                    }"
                                                />
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    @click="validateVat"
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
                                            Introduza o NIF e valide através do
                                            VIES
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
                                <FormField name="name">
                                    <FormItem>
                                        <FormLabel>Nome *</FormLabel>
                                        <FormControl>
                                            <Input
                                                placeholder="Nome da empresa"
                                                v-model="form.name"
                                            />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Morada -->
                                <FormField name="address">
                                    <FormItem>
                                        <FormLabel>Morada *</FormLabel>
                                        <FormControl>
                                            <Textarea
                                                placeholder="Morada completa"
                                                v-model="form.address"
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
                                            <FormLabel
                                                >Código Postal *</FormLabel
                                            >
                                            <FormControl>
                                                <Input
                                                    placeholder="1234-567"
                                                    v-model="form.postal_code"
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
                                                    v-model="form.city"
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
                                                    :value="country.id"
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

                                <!-- Tipo de Entidade -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="types"
                                >
                                    <FormItem>
                                        <FormLabel
                                            >Tipo de Entidade *</FormLabel
                                        >
                                        <div class="space-y-2">
                                            <FormItem
                                                class="flex flex-row items-start space-y-0 space-x-3"
                                            >
                                                <FormControl>
                                                    <Checkbox
                                                        :checked="isClient"
                                                        @update:checked="
                                                            (value) =>
                                                                toggleType(
                                                                    'client',
                                                                    value,
                                                                )
                                                        "
                                                    />
                                                </FormControl>
                                                <div
                                                    class="space-y-1 leading-none"
                                                >
                                                    <FormLabel
                                                        >Cliente</FormLabel
                                                    >
                                                    <FormDescription>
                                                        Esta entidade pode
                                                        receber propostas e
                                                        encomendas
                                                    </FormDescription>
                                                </div>
                                            </FormItem>
                                            <FormItem
                                                class="flex flex-row items-start space-y-0 space-x-3"
                                            >
                                                <FormControl>
                                                    <Checkbox
                                                        :checked="isSupplier"
                                                        @update:checked="
                                                            (value) =>
                                                                toggleType(
                                                                    'supplier',
                                                                    value,
                                                                )
                                                        "
                                                    />
                                                </FormControl>
                                                <div
                                                    class="space-y-1 leading-none"
                                                >
                                                    <FormLabel
                                                        >Fornecedor</FormLabel
                                                    >
                                                    <FormDescription>
                                                        Esta entidade pode
                                                        fornecer
                                                        produtos/serviços
                                                    </FormDescription>
                                                </div>
                                            </FormItem>
                                        </div>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Consentimento RGPD -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="gdpr_consent"
                                >
                                    <FormItem
                                        class="flex flex-row items-start space-y-0 space-x-3 rounded-lg border p-4"
                                    >
                                        <FormControl>
                                            <Checkbox v-bind="componentField" />
                                        </FormControl>
                                        <div class="space-y-1 leading-none">
                                            <FormLabel
                                                >Consentimento RGPD</FormLabel
                                            >
                                            <FormDescription>
                                                Autoriza o tratamento dos dados
                                                pessoais de acordo com o RGPD
                                            </FormDescription>
                                        </div>
                                    </FormItem>
                                    <FormMessage />
                                </FormField>

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
                        </div>

                        <!-- Botões de Acção -->
                        <div class="mt-6 flex justify-end gap-3 border-t pt-6">
                            <Button
                                type="button"
                                variant="outline"
                                @click="goBack"
                            >
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <SaveIcon
                                    v-if="!form.processing"
                                    class="mr-2 h-4 w-4"
                                />
                                <LoaderIcon
                                    v-else
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                {{
                                    form.processing
                                        ? 'A guardar...'
                                        : 'Guardar Entidade'
                                }}
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {
    Form,
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
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import {
    ArrowLeftIcon,
    CheckCircleIcon,
    LoaderIcon,
    SaveIcon,
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
const vatLoading = ref(false);
const vatValid = ref(false);
const vatResult = ref(null);

// Form
const form = useForm({
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
});

// Computed
const isClient = computed(() => form.types.includes('client'));
const isSupplier = computed(() => form.types.includes('supplier'));
const error = ref(null);
// Methods
const goBack = () => {
    router.get(route('entities.index', { type: props.type }));
};

const toggleType = (type, checked) => {
    if (checked) {
        if (!form.types.includes(type)) {
            form.types.push(type);
        }
    } else {
        form.types = form.types.filter((t) => t !== type);
    }
};

const validateVat = async () => {
    console.log('validateVat foi chamado');
    console.log('NIF atual:', form.tax_number);
    if (!form.tax_number) return;

    vatLoading.value = true;
    vatResult.value = null;
    error.value = null;

    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        const response = await axios.post(
            '/entities/vies-check',
            {
                vat_number: form.tax_number,
            },
            {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
            },
        );

        vatResult.value = response.data;

        if (response.data.valid) {
            vatValid.value = true;

            // Preenche automaticamente os campos
            if (response.data.name) form.name = response.data.name;

            if (response.data.address) {
                const address = response.data.address
                    .replace(/\s{2,}/g, ' ') // remove espaços duplos
                    .trim();

                form.address = address;

                // Regex para código postal português
                const postalRegex = /(\d{4}-\d{3})/;
                const postalMatch = address.match(postalRegex);

                if (postalMatch) {
                    form.postal_code = postalMatch[1];

                    // Agora tentamos pegar a cidade após o código postal
                    const afterPostal = address
                        .split(postalMatch[1])[1]
                        ?.trim();

                    if (afterPostal) {
                        // remove pontos finais e caracteres soltos
                        const cleanedCity = afterPostal
                            .replace(/\.$/, '') // remove ponto final
                            .replace(/\s+/g, ' ') // espaços duplos
                            .trim();

                        // Muitas vezes vem algo como "CARNAXIDE" ou "LISBOA"
                        // Se vier "PORTELA CARNAXIDE" pegamos a última palavra (a mais significativa)
                        const cityParts = cleanedCity.split(' ');
                        form.city = cityParts[cityParts.length - 1];
                    }
                }
            }
        } else {
            vatValid.value = false;
            error.value = response.data.error || 'Número VAT inválido';
        }
    } catch (err) {
        error.value =
            err.response?.data?.error ||
            'Erro ao validar o VAT. Tente novamente.';
        vatResult.value = { valid: false };
    } finally {
        vatLoading.value = false;
    }
};

const submitForm = () => {
    // Validação manual dos tipos
    if (form.types.length === 0) {
        alert('Selecione pelo menos um tipo (Cliente e/ou Fornecedor)');
        return;
    }

    form.post(route('entities.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by controller
        },
        onError: (errors) => {
            console.error('Erros no formulário:', errors);
        },
    });
};

// Auto-select type based on menu
onMounted(() => {
    if (props.defaultTypes && props.defaultTypes.length > 0) {
        form.types = props.defaultTypes;
    }
});
</script>
