<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Novo País" description="Adicionar um novo país">
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

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
                                <FormLabel
                                    >Código (ISO 3166-1 alpha-2)</FormLabel
                                >
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

                        <FormField
                            v-slot="{ componentField }"
                            name="phone_code"
                        >
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

                        <CheckboxField
                            name="is_active"
                            label="País Ativo"
                            description="Este país estará disponível para seleção"
                        />

                        <div class="flex justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="handleCancel"
                            >
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <LoaderCircleIcon
                                    v-if="isSubmitting"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                Criar País
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import countries from '@/routes/countries';
import { countrySchema, type CountryFormData } from '@/schemas/countrySchema';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { ArrowLeftIcon, LoaderCircleIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { ref } from 'vue';

const isSubmitting = ref(false);

const form = useForm<CountryFormData>({
    validationSchema: toTypedSchema(countrySchema),
    initialValues: {
        name: '',
        code: '',
        phone_code: '',
        is_active: true,
    },
});

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;

    router.post(countries.store().url, values, {
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
});

const goBack = () => router.get('/countries');

const handleCancel = () => {
    router.visit(countries.index().url);
};
</script>
