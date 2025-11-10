<template>
    <FormWrapper
        title="Novo Utilizador"
        description="Criar novo utilizador do sistema"
        :schema="userSchema"
        submit-url="/users"
        submit-method="post"
        submit-text="Criar Utilizador"
    >
        <template #form-fields>
            <FormField v-slot="{ componentField }" name="name">
                <FormItem>
                    <FormLabel>Nome *</FormLabel>
                    <FormControl>
                        <Input
                            v-bind="componentField"
                            placeholder="Nome completo"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="email">
                <FormItem>
                    <FormLabel>Email *</FormLabel>
                    <FormControl>
                        <Input
                            type="email"
                            v-bind="componentField"
                            placeholder="email@exemplo.com"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="password">
                <FormItem>
                    <FormLabel>Palavra-passe *</FormLabel>
                    <FormControl>
                        <Input
                            type="password"
                            v-bind="componentField"
                            placeholder="Palavra-passe"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="password_confirmation">
                <FormItem>
                    <FormLabel>Confirmar Palavra-passe *</FormLabel>
                    <FormControl>
                        <Input
                            type="password"
                            v-bind="componentField"
                            placeholder="Confirmar palavra-passe"
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
                                <SelectValue placeholder="Selecione os grupos" />
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

interface Props {
    roles: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();
</script>
