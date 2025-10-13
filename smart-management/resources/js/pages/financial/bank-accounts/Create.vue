<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Nova Conta Bancária" description="Registar nova conta bancária">
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
                                    <FormLabel>Nome da Conta *</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" placeholder="Ex: Conta Principal" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="bank_name">
                                <FormItem>
                                    <FormLabel>Nome do Banco *</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" placeholder="Ex: Banco Exemplo" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="account_number">
                                <FormItem>
                                    <FormLabel>Número da Conta *</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" placeholder="Ex: 0123456789" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="iban">
                                <FormItem>
                                    <FormLabel>IBAN</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" placeholder="Ex: PT50..." />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="swift">
                                <FormItem>
                                    <FormLabel>SWIFT/BIC</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" placeholder="Ex: ABCDEFGH" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="currency">
                                <FormItem>
                                    <FormLabel>Moeda *</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" placeholder="EUR" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="balance">
                                <FormItem>
                                    <FormLabel>Saldo Inicial</FormLabel>
                                    <FormControl>
                                        <Input type="number" step="0.01" v-bind="componentField" placeholder="0.00" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ value, handleChange }" name="is_active">
                                <FormItem class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
                                    <FormControl>
                                        <Checkbox :checked="value" @update:checked="(checked: boolean) => handleChange(checked)" />
                                    </FormControl>
                                    <div class="space-y-1 leading-none">
                                        <FormLabel>Conta Ativa</FormLabel>
                                        <FormDescription>
                                            Marque se a conta está ativa para transações
                                        </FormDescription>
                                    </div>
                                </FormItem>
                            </FormField>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button type="button" variant="outline" @click="goBack">Cancelar</Button>
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A guardar...' : 'Guardar Conta' }}
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
import { bankAccountSchema } from '@/schemas/bankAccountSchema';
import { toTypedSchema } from '@vee-validate/zod';
import { router } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const isSubmitting = ref(false);

const form = useForm({
    validationSchema: toTypedSchema(bankAccountSchema),
    initialValues: {
        name: '',
        account_number: '',
        iban: '',
        swift: '',
        bank_name: '',
        balance: 0,
        currency: 'EUR',
        is_active: true,
    },
});

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;
    router.post('/bank-accounts', values, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
});

const goBack = () => router.get('/bank-accounts');
</script>





