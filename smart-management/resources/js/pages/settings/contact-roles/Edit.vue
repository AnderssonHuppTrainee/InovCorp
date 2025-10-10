<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Editar Função de Contacto" :description="`Editar ${contactRole.name}`" />

            <Card>
                <CardContent class="pt-6">
                    <form @submit="onSubmit" class="space-y-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Diretor Geral"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="description">
                            <FormItem>
                                <FormLabel>Descrição</FormLabel>
                                <FormControl>
                                    <Textarea
                                        placeholder="Descrição da função..."
                                        v-bind="componentField"
                                        rows="3"
                                    />
                                </FormControl>
                                <FormDescription>
                                    Descrição opcional da função
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ value, handleChange }" name="is_active">
                            <FormItem class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
                                <FormControl>
                                    <Checkbox :checked="value" @update:checked="handleChange" />
                                </FormControl>
                                <div class="space-y-1 leading-none">
                                    <FormLabel>Função Ativa</FormLabel>
                                    <FormDescription>
                                        Esta função estará disponível para seleção
                                    </FormDescription>
                                </div>
                            </FormItem>
                        </FormField>

                        <div class="flex justify-end gap-4">
                            <Button type="button" variant="outline" @click="handleCancel">
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <LoaderCircleIcon v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                                Atualizar Função
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Checkbox } from '@/components/ui/checkbox'
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form'
import PageHeader from '@/components/PageHeader.vue'
import { LoaderCircleIcon } from 'lucide-vue-next'
import { contactRoleSchema, type ContactRoleFormData } from '@/schemas/contactRoleSchema'
import contactRoles from '@/routes/contact-roles'

interface Props {
    contactRole: {
        id: number
        name: string
        description: string | null
        is_active: boolean
    }
}

const props = defineProps<Props>()

const isSubmitting = ref(false)

const form = useForm<ContactRoleFormData>({
    validationSchema: toTypedSchema(contactRoleSchema),
    initialValues: {
        name: props.contactRole.name,
        description: props.contactRole.description || '',
        is_active: props.contactRole.is_active,
    },
})

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true

    router.put(contactRoles.update({ contactRole: props.contactRole.id }).url, values, {
        onFinish: () => {
            isSubmitting.value = false
        },
    })
})

const handleCancel = () => {
    router.visit(contactRoles.index().url)
}
</script>


