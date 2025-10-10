<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Novo Utilizador" description="Criar novo utilizador do sistema">
                <Button variant="outline" @click="goBack"><ArrowLeftIcon class="mr-2 h-4 w-4" />Voltar</Button>
            </PageHeader>

            <form @submit="onSubmit">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <FormField v-slot="{ componentField }" name="name">
                                <FormItem>
                                    <FormLabel>Nome *</FormLabel>
                                    <FormControl><Input v-bind="componentField" /></FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="email">
                                <FormItem>
                                    <FormLabel>Email *</FormLabel>
                                    <FormControl><Input type="email" v-bind="componentField" /></FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="mobile">
                                <FormItem>
                                    <FormLabel>Telemóvel</FormLabel>
                                    <FormControl><Input v-bind="componentField" /></FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="password">
                                <FormItem>
                                    <FormLabel>Password *</FormLabel>
                                    <FormControl><Input type="password" v-bind="componentField" /></FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="password_confirmation">
                                <FormItem>
                                    <FormLabel>Confirmar Password *</FormLabel>
                                    <FormControl><Input type="password" v-bind="componentField" /></FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ value, handleChange }" name="is_active">
                                <FormItem class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
                                    <FormControl>
                                        <Checkbox :checked="value" @update:checked="(checked: boolean) => handleChange(checked)" />
                                    </FormControl>
                                    <div class="space-y-1 leading-none">
                                        <FormLabel>Utilizador Ativo</FormLabel>
                                        <FormDescription>Marque se o utilizador deve estar ativo</FormDescription>
                                    </div>
                                </FormItem>
                            </FormField>

                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Grupos de Permissões</label>
                                <div class="grid grid-cols-2 gap-2 border rounded-md p-4">
                                    <div v-for="role in roles" :key="role.id" class="flex items-center space-x-2">
                                        <Checkbox :id="`role-${role.id}`" :checked="form.values.roles?.includes(role.name)" @update:checked="(checked: boolean) => toggleRole(role.name, checked)" />
                                        <label :for="`role-${role.id}`" class="text-sm cursor-pointer">{{ role.name }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack">Cancelar</Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A guardar...' : 'Criar Utilizador' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { userSchema } from '@/schemas/userSchema';
import { toTypedSchema } from '@vee-validate/zod';
import { router } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    roles: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

const isSubmitting = ref(false);

const form = useForm({
    validationSchema: toTypedSchema(userSchema),
    initialValues: {
        name: '',
        email: '',
        mobile: '',
        password: '',
        password_confirmation: '',
        roles: [],
        is_active: true,
    },
});

const toggleRole = (roleName: string, checked: boolean) => {
    const currentRoles = form.values.roles || [];
    if (checked) {
        form.setFieldValue('roles', [...currentRoles, roleName]);
    } else {
        form.setFieldValue('roles', currentRoles.filter((r: string) => r !== roleName));
    }
};

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;
    router.post('/users', values, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
});

const goBack = () => router.get('/users');
</script>




