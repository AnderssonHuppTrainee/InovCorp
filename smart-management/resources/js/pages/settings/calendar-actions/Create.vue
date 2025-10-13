<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Nova Ação de Calendário" description="Criar uma nova ação" />

            <Card>
                <CardContent class="pt-6">
                    <form @submit="onSubmit" class="space-y-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Ligação"
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
                                        placeholder="Descrição da ação..."
                                        v-bind="componentField"
                                        rows="3"
                                    />
                                </FormControl>
                                <FormDescription>
                                    Descrição opcional da ação
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <CheckboxField
                            name="is_active"
                            label="Ação Ativa"
                            description="Esta ação estará disponível para seleção"
                        />

                        <div class="flex justify-end gap-4">
                            <Button type="button" variant="outline" @click="handleCancel">
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <LoaderCircleIcon v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                                Criar Ação
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
import CheckboxField from '@/components/common/CheckboxField.vue'
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
import { calendarActionSchema, type CalendarActionFormData } from '@/schemas/calendarActionSchema'
import calendarActions from '@/routes/calendar-actions'

const isSubmitting = ref(false)

const form = useForm<CalendarActionFormData>({
    validationSchema: toTypedSchema(calendarActionSchema),
    initialValues: {
        name: '',
        description: '',
        is_active: true,
    },
})

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true

    router.post(calendarActions.store().url, values, {
        onFinish: () => {
            isSubmitting.value = false
        },
    })
})

const handleCancel = () => {
    router.visit(calendarActions.index().url)
}
</script>



