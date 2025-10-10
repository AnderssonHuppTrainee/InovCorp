<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="
                    type === 'client' ? 'Editar Cliente' : 'Editar Fornecedor'
                "
                :description="`Atualizar dados ${type === 'client' ? 'do cliente' : 'do fornecedor'}`"
            >
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <Card>
                <CardContent class="p-6">
                    <form @submit="submitForm">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div class="space-y-6">
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
                                                    v-bind="componentField"
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
                            <Button type="submit" :disabled="isSubmitting">
                                <SaveIcon
                                    v-if="!isSubmitting"
                                    class="mr-2 h-4 w-4"
                                />
                                <LoaderIcon
                                    v-else
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                {{
                                    isSubmitting
                                        ? 'A atualizar...'
                                        : 'Atualizar Entidade'
                                }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
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
import AppLayout from '@/layouts/AppLayout.vue';
import route from '@/routes/entities';
import { entitySchema } from '@/schemas/entitySchema';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import axios from 'axios';
import {
    ArrowLeftIcon,
    CheckCircleIcon,
    LoaderIcon,
    SaveIcon,
    SearchIcon,
    XCircleIcon,
} from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, onMounted, ref } from 'vue';

// Props
const props = defineProps({
    type: String,
    countries: Array,
    entity: Object,
});

// Refs
const vatLoading = ref(false);
const vatValid = ref(false);
const vatResult = ref(null);
const error = ref(null);
const isSubmitting = ref(false);

// Form with vee-validate + Zod - Preenchido com dados da entidade
const form = useForm({
    validationSchema: toTypedSchema(entitySchema),
    initialValues: {
        tax_number: props.entity.tax_number || '',
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
    },
});

// Computed
const isClient = computed(() => form.values.types.includes('client'));
const isSupplier = computed(() => form.values.types.includes('supplier'));

// Methods
const goBack = () => {
    router.get(route.index().url, { type: props.type });
};

const handleGdprChange = (e) => {
    const checked = e.target.checked;
    form.setFieldValue('gdpr_consent', checked);
};

const validateVat = async () => {
    console.log('validateVat foi chamado');
    console.log('NIF atual:', form.values.tax_number);
    if (!form.values.tax_number) return;

    vatLoading.value = true;
    vatResult.value = null;
    error.value = null;

    try {
        // Axios já está configurado com CSRF token no app.ts
        const response = await axios.post('/entities/vies-check', {
            vat_number: form.values.tax_number,
        });

        vatResult.value = response.data;

        if (response.data.valid) {
            vatValid.value = true;

            // Preenche automaticamente os campos usando setFieldValue
            if (response.data.name) {
                console.log('Preenchendo nome:', response.data.name);
                form.setFieldValue('name', response.data.name);
            }

            if (response.data.address) {
                const address = response.data.address
                    .replace(/\s{2,}/g, ' ') // remove espaços duplos
                    .trim();

                console.log('Preenchendo morada:', address);
                form.setFieldValue('address', address);

                // Regex para código postal português
                const postalRegex = /(\d{4}-\d{3})/;
                const postalMatch = address.match(postalRegex);

                if (postalMatch) {
                    console.log('Preenchendo código postal:', postalMatch[1]);
                    form.setFieldValue('postal_code', postalMatch[1]);

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
                        const city = cityParts[cityParts.length - 1];
                        console.log('Preenchendo cidade:', city);
                        form.setFieldValue('city', city);
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

const submitForm = form.handleSubmit(
    (values) => {
        isSubmitting.value = true;

        // PUT para atualizar a entidade
        router.put(route.update({ id: props.entity.id }).url, values, {
            preserveScroll: true,
            onSuccess: () => {
                isSubmitting.value = false;
            },
            onError: (errors) => {
                isSubmitting.value = false;
                console.error('Erros no formulário:', errors);
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    },
    (errors) => {
        console.error('Erros de validação:', errors);
    },
);

// Lifecycle hooks podem ser adicionados aqui se necessário
onMounted(() => {
    // Form é inicializado automaticamente com os valores da entidade
});
</script>
