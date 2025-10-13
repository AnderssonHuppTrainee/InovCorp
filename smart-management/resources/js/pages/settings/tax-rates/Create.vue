<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Nova Taxa de IVA" description="Criar nova taxa de IVA">
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <form @submit="onSubmit">
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <FormField v-slot="{ componentField }" name="name">
                                <FormItem>
                                    <FormLabel>Nome *</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" placeholder="Ex: Normal, Reduzida, Isenta" />
                                    </FormControl>
                                    <FormDescription>
                                        Nome descritivo da taxa (ex: Normal, Reduzida, Isenta)
                                    </FormDescription>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="rate">
                                <FormItem>
                                    <FormLabel>Taxa (%) *</FormLabel>
                                    <FormControl>
                                        <Input type="number" step="0.01" min="0" max="100" v-bind="componentField" placeholder="23.00" />
                                    </FormControl>
                                    <FormDescription>
                                        Percentagem da taxa de IVA (0-100)
                                    </FormDescription>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <CheckboxField
                                name="is_active"
                                label="Taxa Ativa"
                                description="Marque se a taxa está ativa para uso"
                            />
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack">Cancelar</Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A guardar...' : 'Criar Taxa' }}
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
import CheckboxField from '@/components/common/CheckboxField.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { taxRateSchema } from '@/schemas/taxRateSchema';
import { toTypedSchema } from '@vee-validate/zod';
import { router } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const isSubmitting = ref(false);

const form = useForm({
    validationSchema: toTypedSchema(taxRateSchema),
    initialValues: {
        name: '',
        rate: 0,
        is_active: true,
    },
});

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;
    router.post('/tax-rates', values, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
});

const goBack = () => router.get('/tax-rates');
</script>




