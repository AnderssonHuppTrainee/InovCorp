<template>
    <ShowWrapper
        :title="`Conta: ${account.name}`"
        :description="account.bank_name"
        :edit-url="`/bank-accounts/${account.id}/edit`"
        :delete-url="`/bank-accounts/${account.id}`"
        :back-url="'/bank-accounts'"
        :item-name="account.name"
    >
        <template #main-content>
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Informações da Conta</CardTitle>
                            <Badge
                                :variant="
                                    account.is_active ? 'default' : 'secondary'
                                "
                            >
                                {{ account.is_active ? 'Ativa' : 'Inativa' }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-muted-foreground">
                                Nome
                            </div>
                            <div class="col-span-2 font-medium">
                                {{ account.name }}
                            </div>
                        </div>
                        <Separator />
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-muted-foreground">
                                Banco
                            </div>
                            <div class="col-span-2">
                                {{ account.bank_name }}
                            </div>
                        </div>
                        <Separator />
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-muted-foreground">
                                Número da Conta
                            </div>
                            <div class="col-span-2 font-mono">
                                {{ account.account_number }}
                            </div>
                        </div>
                        <Separator />
                        <div class="grid grid-cols-3 gap-4" v-if="account.iban">
                            <div class="text-sm text-muted-foreground">
                                IBAN
                            </div>
                            <div class="col-span-2 font-mono">
                                {{ account.iban }}
                            </div>
                        </div>
                        <Separator v-if="account.iban" />
                        <div
                            class="grid grid-cols-3 gap-4"
                            v-if="account.swift"
                        >
                            <div class="text-sm text-muted-foreground">
                                SWIFT/BIC
                            </div>
                            <div class="col-span-2 font-mono">
                                {{ account.swift }}
                            </div>
                        </div>
                        <Separator v-if="account.swift" />
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-sm text-muted-foreground">
                                Moeda
                            </div>
                            <div class="col-span-2">{{ account.currency }}</div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </template>
        <template #sidebar>
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Saldo</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div
                            class="text-3xl font-bold"
                            :class="
                                account.balance < 0 ? 'text-destructive' : ''
                            "
                        >
                            {{
                                formatCurrency(
                                    account.balance,
                                    account.currency,
                                )
                            }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Detalhes</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-1">
                            <div class="text-sm text-muted-foreground">
                                Criado em
                            </div>
                            <div class="text-sm">
                                {{ formatDateTime(account.created_at) }}
                            </div>
                        </div>
                        <Separator />
                        <div class="space-y-1">
                            <div class="text-sm text-muted-foreground">
                                Atualizado
                            </div>
                            <div class="text-sm">
                                {{ formatDateTime(account.updated_at) }}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </template>
    </ShowWrapper>
</template>

<script setup lang="ts">
import ShowWrapper from '@/components/common/ShowWrapper.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

interface Props {
    account: any;
}

const props = defineProps<Props>();

const formatCurrency = (value: number, currency: string) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: currency || 'EUR',
    }).format(value);
};

const formatDateTime = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
