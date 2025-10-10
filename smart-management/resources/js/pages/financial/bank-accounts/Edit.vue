<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Editar Conta Bancária" :description="`Conta: ${account.name}`">
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
                                        <Input v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="bank_name">
                                <FormItem>
                                    <FormLabel>Nome do Banco *</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="account_number">
                                <FormItem>
                                    <FormLabel>Número da Conta *</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="iban">
                                <FormItem>
                                    <FormLabel>IBAN</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="swift">
                                <FormItem>
                                    <FormLabel>SWIFT/BIC</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="currency">
                                <FormItem>
                                    <FormLabel>Moeda *</FormLabel>
                                    <FormControl>
                                        <Input v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="balance">
                                <FormItem>
                                    <FormLabel>Saldo</FormLabel>
                                    <FormControl>
                                        <Input type="number" step="0.01" v-bind="componentField" />
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
                        {{ isSubmitting ? 'A atualizar...' : 'Atualizar Conta' }}
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

interface Props {
    account: any;
}

const props = defineProps<Props>();

const isSubmitting = ref(false);

const form = useForm({
    validationSchema: toTypedSchema(bankAccountSchema),
    initialValues: {
        name: props.account.name,
        account_number: props.account.account_number,
        iban: props.account.iban || '',
        swift: props.account.swift || '',
        bank_name: props.account.bank_name,
        balance: props.account.balance,
        currency: props.account.currency,
        is_active: props.account.is_active,
    },
});

const onSubmit = form.handleSubmit((values) => {
    isSubmitting.value = true;
    router.put(`/bank-accounts/${props.account.id}`, values, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
});

const goBack = () => router.get('/bank-accounts');
</script>




