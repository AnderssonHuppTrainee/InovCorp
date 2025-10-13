<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Editar Grupo" :description="`${role.name}`">
                <Button variant="outline" @click="goBack"><ArrowLeftIcon class="mr-2 h-4 w-4" />Voltar</Button>
            </PageHeader>

            <form @submit="onSubmit">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="space-y-6">
                            <FormField v-slot="{ componentField }" name="name">
                                <FormItem>
                                    <FormLabel>Nome *</FormLabel>
                                    <FormControl><Input v-bind="componentField" /></FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <div class="space-y-4">
                                <label class="text-sm font-medium">Permissões</label>
                                <div v-for="(perms, module) in permissionsGrouped" :key="module" class="border rounded-lg p-4">
                                    <h3 class="font-semibold text-sm mb-3 capitalize">{{ module }}</h3>
                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                                        <div v-for="permission in perms" :key="permission.id" class="flex items-center space-x-2">
                                            <Checkbox :id="`perm-${permission.id}`" :checked="form.values.permissions?.includes(permission.name)" @update:checked="(checked: boolean) => togglePermission(permission.name, checked)" />
                                            <label :for="`perm-${permission.id}`" class="text-xs cursor-pointer">{{ permission.name.split('.').pop() }}</label>
                                        </div>
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
                        {{ isSubmitting ? 'A atualizar...' : 'Atualizar' }}
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
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { roleSchema } from '@/schemas/roleSchema';
import { toTypedSchema } from '@vee-validate/zod';
import { router } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    role: any;
    permissionsGrouped: Record<string, Array<{ id: number; name: string }>>;
    rolePermissions: string[];
}

const props = defineProps<Props>();

const isSubmitting = ref(false);

const form = useForm({
    validationSchema: toTypedSchema(roleSchema),
    initialValues: {
        name: props.role.name,
        permissions: props.rolePermissions,
    },
});

const togglePermission = (permName: string, checked: boolean) => {
    const current = form.values.permissions || [];
    if (checked) {
        form.setFieldValue('permissions', [...current, permName]);
    } else {
        form.setFieldValue('permissions', current.filter((p: string) => p !== permName));
    }
};

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;
    router.put(`/roles/${props.role.id}`, values, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
});

const goBack = () => router.get('/roles');
</script>





