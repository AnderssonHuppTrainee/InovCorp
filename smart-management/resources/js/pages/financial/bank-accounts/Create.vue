<template>
    <FormWrapper
        title="Nova Conta Bancária"
        description="Registar nova conta bancária"
        :schema="bankAccountSchema"
        :initial-values="initialValues"
        submit-url="/bank-accounts"
        submit-method="post"
        submit-text="Guardar Conta"
    >
        <template #form-fields>
            <FormField v-slot="{ componentField }" name="name">
                <FormItem>
                    <FormLabel>Nome da Conta *</FormLabel>
                    <FormControl>
                        <Input
                            v-bind="componentField"
                            placeholder="Conta Principal"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="bank_name">
                <FormItem>
                    <FormLabel>Nome do Banco *</FormLabel>
                    <FormControl>
                        <Input
                            v-bind="componentField"
                            placeholder="Banco Exemplo"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="account_number">
                <FormItem>
                    <FormLabel>Número da Conta *</FormLabel>
                    <FormControl>
                        <Input
                            v-bind="componentField"
                            placeholder="0123456789"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="iban">
                <FormItem>
                    <FormLabel>IBAN</FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" placeholder="PT50..." />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="swift">
                <FormItem>
                    <FormLabel>SWIFT/BIC</FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" placeholder="ABCDEFGH" />
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
                        <Input
                            type="number"
                            step="0.01"
                            v-bind="componentField"
                            placeholder="0.00"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <CheckboxField
                name="is_active"
                label="Conta Ativa"
                description="Marque se a conta está ativa para transações"
            />
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import CheckboxField from '@/components/common/CheckboxField.vue';
import FormWrapper from '@/components/common/FormWrapper.vue';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { bankAccountSchema } from '@/schemas/bankAccountSchema';

const initialValues = {
    name: '',
    account_number: '',
    iban: '',
    swift: '',
    bank_name: '',
    balance: 0,
    currency: 'EUR',
    is_active: true,
};
</script>
