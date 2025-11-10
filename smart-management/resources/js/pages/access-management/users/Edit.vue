<template>
    <FormWrapper
        title="Editar Utilizador"
        :description="`${user.name} (${user.email})`"
        :schema="userSchema"
        :initial-values="initialValues"
        :submit-url="`/users/${user.id}`"
        submit-method="put"
        submit-text="Atualizar Utilizador"
    >
        <template #form-fields>
            <FormField v-slot="{ componentField }" name="name">
                <FormItem>
                    <FormLabel>Nome *</FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="email">
                <FormItem>
                    <FormLabel>Email *</FormLabel>
                    <FormControl>
                        <Input type="email" v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="password">
                <FormItem>
                    <FormLabel>Nova Palavra-passe</FormLabel>
                    <FormControl>
                        <Input
                            type="password"
                            v-bind="componentField"
                            placeholder="Deixe em branco para manter a atual"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="password_confirmation">
                <FormItem>
                    <FormLabel>Confirmar Nova Palavra-passe</FormLabel>
                    <FormControl>
                        <Input
                            type="password"
                            v-bind="componentField"
                            placeholder="Confirmar nova palavra-passe"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="roles">
                <FormItem>
                    <FormLabel>Grupos de Permissões</FormLabel>
                    <Select v-bind="componentField" multiple>
                        <FormControl>
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Selecione os grupos"
                                />
                            </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                            <SelectItem
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <CheckboxField
                name="is_active"
                label="Utilizador Ativo"
                description="Este utilizador pode fazer login no sistema"
            />
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import CheckboxField from '@/components/common/CheckboxField.vue';
import FormWrapper from '@/components/common/FormWrapper.vue';
import {
    FormControl,
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
import { userSchema } from '@/schemas/userSchema';
import { computed } from 'vue';

interface Props {
    user: {
        id: number;
        name: string;
        email: string;
        is_active: boolean;
    };
    roles: Array<{ id: number; name: string }>;
    userRoles: string[];
}

const props = defineProps<Props>();

const initialValues = computed(() => ({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    roles: props.userRoles,
    is_active: props.user.is_active,
}));
</script>
