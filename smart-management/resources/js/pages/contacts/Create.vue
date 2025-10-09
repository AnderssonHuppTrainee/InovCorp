<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Novo Contacto"
                description="Registar novo contacto no sistema"
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
                            <!-- Coluna 1 -->
                            <div class="space-y-6">
                                <!-- Entidade -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="entity_id"
                                >
                                    <FormItem>
                                        <FormLabel>Entidade *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue
                                                        placeholder="Selecione uma entidade"
                                                    />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="entity in entities"
                                                    :key="entity.id"
                                                    :value="String(entity.id)"
                                                >
                                                    {{ entity.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <FormDescription>
                                            Selecione a entidade associada a este
                                            contacto
                                        </FormDescription>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Nome -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="first_name"
                                >
                                    <FormItem>
                                        <FormLabel>Nome *</FormLabel>
                                        <FormControl>
                                            <Input
                                                placeholder="Nome do contacto"
                                                v-bind="componentField"
                                            />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Apelido -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="last_name"
                                >
                                    <FormItem>
                                        <FormLabel>Apelido *</FormLabel>
                                        <FormControl>
                                            <Input
                                                placeholder="Apelido do contacto"
                                                v-bind="componentField"
                                            />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Função -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="contact_role_id"
                                >
                                    <FormItem>
                                        <FormLabel>Função *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue
                                                        placeholder="Selecione a função"
                                                    />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="role in roles"
                                                    :key="role.id"
                                                    :value="String(role.id)"
                                                >
                                                    {{ role.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <FormDescription>
                                            Função/Cargo do contacto na entidade
                                        </FormDescription>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                            </div>

                            <!-- Coluna 2 -->
                            <div class="space-y-6">
                                <!-- Telefone e Telemóvel -->
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

                                <!-- Email -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="email"
                                >
                                    <FormItem>
                                        <FormLabel>Email</FormLabel>
                                        <FormControl>
                                            <Input
                                                type="email"
                                                placeholder="contacto@exemplo.pt"
                                                v-bind="componentField"
                                            />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Consentimento RGPD -->
                                <FormField name="gdpr_consent">
                                    <FormItem
                                        class="flex flex-row items-start space-y-0 space-x-3 rounded-lg border p-4"
                                    >
                                        <FormControl>
                                            <Checkbox
                                                :checked="form.values.gdpr_consent"
                                                @update:checked="
                                                    (value) =>
                                                        form.setFieldValue(
                                                            'gdpr_consent',
                                                            !!value,
                                                        )
                                                "
                                            />
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
                                            Contactos inativos não aparecem nas
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
                                                placeholder="Notas internas sobre o contacto"
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
                                        ? 'A guardar...'
                                        : 'Guardar Contacto'
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
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
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
import { contactSchema } from '@/schemas/contactSchema';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { ref } from 'vue';

// Props
const props = defineProps({
    entities: Array,
    roles: Array,
});

// Refs
const isSubmitting = ref(false);

// Form with vee-validate + Zod
const form = useForm({
    validationSchema: toTypedSchema(contactSchema),
    initialValues: {
        entity_id: '',
        first_name: '',
        last_name: '',
        contact_role_id: '',
        phone: '',
        mobile: '',
        email: '',
        gdpr_consent: false,
        observations: '',
        status: 'active',
    },
});

// Methods
const goBack = () => {
    router.get('/contacts');
};

const submitForm = form.handleSubmit(
    (values) => {
        console.log('✅ Validação passou! Valores:', values);
        isSubmitting.value = true;

        router.post('/contacts', values, {
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
        console.log('❌ Validação falhou! Erros:', errors);
        console.log('Valores atuais do form:', form.values);
    },
);
</script>


