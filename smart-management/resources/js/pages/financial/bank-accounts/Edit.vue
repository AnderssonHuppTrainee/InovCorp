<template>
    <FormWrapper
        title="Editar Conta Bancária"
        :description="`Conta: ${account.name}`"
        :schema="bankAccountSchema"
        :initial-values="initialValues"
        :submit-url="`/bank-accounts/${account.id}`"
        submit-method="put"
        submit-text="Atualizar Conta"
    >
        <template #form-fields>
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
                        <Input
                            type="number"
                            step="0.01"
                            v-bind="componentField"
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
import { computed } from 'vue';

interface Props {
    account: {
        id: string | number;
        name: string;
        account_number: string;
        iban: string;
        swift: string;
        bank_name: string;
        balance: string | number;
        currency: string;
        is_active: boolean;
    };
}

const props = defineProps<Props>();

const initialValues = computed(() => ({
    name: props.account.name,
    account_number: props.account.account_number,
    iban: props.account.iban || '',
    swift: props.account.swift || '',
    bank_name: props.account.bank_name,
    balance: props.account.balance,
    currency: props.account.currency,
    is_active: props.account.is_active,
}));
</script>
