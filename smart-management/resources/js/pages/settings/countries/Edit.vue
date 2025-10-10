<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Editar País" :description="`Editar ${country.name}`" />

            <Card>
                <CardContent class="pt-6">
                    <form @submit="onSubmit" class="space-y-6">
                        <FormField v-slot="{ componentField }" name="name">
                            <FormItem>
                                <FormLabel>Nome</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Portugal"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="code">
                            <FormItem>
                                <FormLabel>Código (ISO 3166-1 alpha-2)</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="PT"
                                        v-bind="componentField"
                                        maxlength="2"
                                        class="uppercase"
                                    />
                                </FormControl>
                                <FormDescription>
                                    Código de 2 letras (ex: PT, ES, FR)
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <FormField v-slot="{ componentField }" name="phone_code">
                            <FormItem>
                                <FormLabel>Código Telefónico</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="+351"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormDescription>
                                    Código telefónico internacional (ex: +351)
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
                                    <FormLabel>País Ativo</FormLabel>
                                    <FormDescription>
                                        Este país estará disponível para seleção
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
                                Atualizar País
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
import { countrySchema, type CountryFormData } from '@/schemas/countrySchema'
import countries from '@/routes/countries'

interface Props {
    country: {
        id: number
        name: string
        code: string
        phone_code: string | null
        is_active: boolean
    }
}

const props = defineProps<Props>()

const isSubmitting = ref(false)

const form = useForm<CountryFormData>({
    validationSchema: toTypedSchema(countrySchema),
    initialValues: {
        name: props.country.name,
        code: props.country.code,
        phone_code: props.country.phone_code || '',
        is_active: props.country.is_active,
    },
})

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true

    router.put(countries.update({ country: props.country.id }).url, values, {
        onFinish: () => {
            isSubmitting.value = false
        },
    })
})

const handleCancel = () => {
    router.visit(countries.index().url)
}
</script>


