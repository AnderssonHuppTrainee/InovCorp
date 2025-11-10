<template>
    <FormWrapper
        title="Editar Contacto"
        description="Atualizar dados do contacto"
        :schema="contactSchema"
        :initial-values="initialValues"
        :submit-url="`/contacts/${contact.id}`"
        submit-method="put"
        submit-text="Atualizar Contacto"
    >
        <template #form-fields>
            <!-- Coluna 1 -->
            <div class="space-y-6">
                <!-- Entidade -->
                <FormField v-slot="{ componentField }" name="entity_id">
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
                            Selecione a entidade associada a este contacto
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <!-- Nome -->
                <FormField v-slot="{ componentField }" name="first_name">
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
                <FormField v-slot="{ componentField }" name="last_name">
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
                <FormField v-slot="{ componentField }" name="contact_role_id">
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

                <!-- Email -->
                <FormField v-slot="{ componentField }" name="email">
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
                            Contactos inativos não aparecem nas listas de
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
                                placeholder="Notas internas sobre o contacto"
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
import { contactSchema } from '@/schemas/contactSchema';
import type { ContactRole, Entity } from '@/types';
import { useForm } from 'vee-validate';
import { computed } from 'vue';
import type { Contact as ContactTable } from './columns';

interface Contact extends ContactTable {
    contact_role_id?: number;
}

interface Props {
    contact: Contact;
    entities: Entity[];
    roles: ContactRole[];
}

const props = defineProps<Props>();

const form = useForm();

const initialValues = computed(() => ({
    entity_id: String(props.contact.entity_id || ''),
    first_name: props.contact.first_name || '',
    last_name: props.contact.last_name || '',
    contact_role_id: String(props.contact.contact_role_id || ''),
    phone: props.contact.phone || '',
    mobile: props.contact.mobile || '',
    email: props.contact.email || '',
    gdpr_consent: props.contact.gdpr_consent || false,
    observations: props.contact.observations || '',
    status: props.contact.status || 'active',
}));
</script>
