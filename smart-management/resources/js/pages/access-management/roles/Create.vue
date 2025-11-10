<template>
    <FormWrapper
        title="Novo Grupo de Permissões"
        description="Criar novo grupo com permissões"
        :schema="roleSchema"
        :initial-values="{ name: '', permissions: [] }"
        submit-url="/roles"
        submit-method="post"
        submit-text="Criar Grupo"
    >
        <template #form-fields>
            <div class="space-y-6">
                            <FormField v-slot="{ componentField }" name="name">
                                <FormItem>
                                    <FormLabel>Nome do Grupo *</FormLabel>
                                    <FormControl><Input v-bind="componentField" placeholder="Ex: Administrador" /></FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <div class="space-y-4">
                                <label class="text-sm font-medium">Permissões</label>
                                <div v-for="(perms, module) in permissionsGrouped" :key="module" class="border rounded-lg p-4">
                                    <h3 class="font-semibold text-sm mb-3 capitalize">{{ module }}</h3>
                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                                        <div v-for="permission in perms" :key="permission.id" class="flex items-center space-x-2">
                                            <Checkbox
                                                :id="`perm-${permission.id}`"
                                    :checked="formCtx.values.permissions?.includes(permission.name)"
                                    @update:checked="(checked: boolean) => togglePermission(permission.name, checked)"
                                            />
                                            <label :for="`perm-${permission.id}`" class="text-xs cursor-pointer">
                                                {{ permission.name.split('.').pop() }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
            </div>
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import FormWrapper from '@/components/common/FormWrapper.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { roleSchema } from '@/schemas/roleSchema';
import { useForm } from 'vee-validate';

interface Props {
    permissionsGrouped: Record<string, Array<{ id: number; name: string }>>;
}

const props = defineProps<Props>();

const formCtx = useForm();

const togglePermission = (permName: string, checked: boolean) => {
    const current = (formCtx.values.permissions as string[] | undefined) || [];
    if (checked) {
        formCtx.setFieldValue('permissions', [...current, permName]);
    } else {
        formCtx.setFieldValue('permissions', current.filter((p: string) => p !== permName));
    }
};
</script>





