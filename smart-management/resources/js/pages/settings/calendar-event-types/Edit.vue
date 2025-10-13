<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Editar Tipo de Evento" :description="`Editar ${calendarEventType.name}`" />

            <Card>
                <CardContent class="pt-6">
                    <form @submit="onSubmit" class="space-y-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Reunião"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="color">
                            <FormItem>
                                <FormLabel>Cor</FormLabel>
                                <FormControl>
                                    <div class="flex gap-2">
                                        <Input
                                            type="color"
                                            v-bind="componentField"
                                            class="h-10 w-20"
                                        />
                                        <Input
                                            type="text"
                                            v-bind="componentField"
                                            placeholder="#3B82F6"
                                            class="font-mono"
                                        />
                                    </div>
                                </FormControl>
                                <FormDescription>
                                    Cor que será exibida no calendário (formato hexadecimal)
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <CheckboxField
                            name="is_active"
                            label="Tipo Ativo"
                            description="Este tipo estará disponível para seleção"
                        />

                        <div class="flex justify-end gap-4">
                            <Button type="button" variant="outline" @click="handleCancel">
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <LoaderCircleIcon v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                                Atualizar Tipo
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
import { calendarEventTypeSchema, type CalendarEventTypeFormData } from '@/schemas/calendarEventTypeSchema'
import calendarEventTypes from '@/routes/calendar-event-types'

interface Props {
    calendarEventType: {
        id: number
        name: string
        color: string
        is_active: boolean
    }
}

const props = defineProps<Props>()

const isSubmitting = ref(false)

const form = useForm<CalendarEventTypeFormData>({
    validationSchema: toTypedSchema(calendarEventTypeSchema),
    initialValues: {
        name: props.calendarEventType.name,
        color: props.calendarEventType.color,
        is_active: props.calendarEventType.is_active,
    },
})

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true

    router.put(calendarEventTypes.update({ calendarEventType: props.calendarEventType.id }).url, values, {
        onFinish: () => {
            isSubmitting.value = false
        },
    })
})

const handleCancel = () => {
    router.visit(calendarEventTypes.index().url)
}
</script>



