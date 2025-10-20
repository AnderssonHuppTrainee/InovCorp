<template>
    <ShowWrapper
        :title="entity.name"
        :description="`${entity.types.includes('client') ? 'Cliente' : ''}${entity.types.includes('client') && entity.types.includes('supplier') ? ' e ' : ''}${entity.types.includes('supplier') ? 'Fornecedor' : ''} #${entity.number || entity.id}`"
        :edit-url="route.edit({ id: entity.id }).url"
        :delete-url="route.destroy({ id: entity.id }).url"
        :back-url="route.index().url"
        :item-name="entity.name"
    >
        <template #main-content>
            <!-- Informações Principais -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Informações Principais</CardTitle>
                        <Badge
                            :variant="
                                entity.status === 'active'
                                    ? 'default'
                                    : 'secondary'
                            "
                        >
                            {{
                                entity.status === 'active' ? 'Ativo' : 'Inativo'
                            }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- NIF -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">NIF/VAT</div>
                        <div class="col-span-2 font-medium">
                            {{ entity.tax_number }}
                        </div>
                    </div>

                    <Separator />

                    <!-- Nome -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Nome</div>
                        <div class="col-span-2 font-medium">
                            {{ entity.name }}
                        </div>
                    </div>

                    <Separator />

                    <!-- Tipo de Entidade -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Tipo de Entidade
                        </div>
                        <div class="col-span-2 flex gap-2">
                            <Badge
                                v-if="entity.types.includes('client')"
                                variant="outline"
                            >
                                Cliente
                            </Badge>
                            <Badge
                                v-if="entity.types.includes('supplier')"
                                variant="outline"
                            >
                                Fornecedor
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Morada -->
            <Card>
                <CardHeader>
                    <CardTitle>Morada</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Endereço -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Endereço
                        </div>
                        <div class="col-span-2 whitespace-pre-line">
                            {{ entity.address || '-' }}
                        </div>
                    </div>

                    <Separator />

                    <!-- Código Postal -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Código Postal
                        </div>
                        <div class="col-span-2">
                            {{ entity.postal_code || '-' }}
                        </div>
                    </div>

                    <Separator />

                    <!-- Localidade -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Localidade
                        </div>
                        <div class="col-span-2">
                            {{ entity.city || '-' }}
                        </div>
                    </div>

                    <Separator />

                    <!-- País -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">País</div>
                        <div class="col-span-2">
                            <div class="flex items-center gap-2">
                                <span v-if="entity.country">
                                    {{ entity.country.name }} ({{
                                        entity.country.code
                                    }})
                                </span>
                                <span v-else class="text-muted-foreground"
                                    >-</span
                                >
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Observações -->
            <Card v-if="entity.observations">
                <CardHeader>
                    <CardTitle>Observações</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm whitespace-pre-line">
                        {{ entity.observations }}
                    </p>
                </CardContent>
            </Card>
        </template>

        <template #sidebar>
            <!-- Contactos -->
            <Card>
                <CardHeader>
                    <CardTitle>Contactos</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Telefone -->
                    <div class="space-y-1">
                        <div
                            class="flex items-center text-sm text-muted-foreground"
                        >
                            <PhoneIcon class="mr-2 h-4 w-4" />
                            Telefone
                        </div>
                        <div class="pl-6">
                            <a
                                v-if="entity.phone"
                                :href="`tel:${entity.phone}`"
                                class="text-primary hover:underline"
                            >
                                {{ entity.phone }}
                            </a>
                            <span v-else class="text-muted-foreground">-</span>
                        </div>
                    </div>

                    <Separator />

                    <!-- Telemóvel -->
                    <div class="space-y-1">
                        <div
                            class="flex items-center text-sm text-muted-foreground"
                        >
                            <SmartphoneIcon class="mr-2 h-4 w-4" />
                            Telemóvel
                        </div>
                        <div class="pl-6">
                            <a
                                v-if="entity.mobile"
                                :href="`tel:${entity.mobile}`"
                                class="text-primary hover:underline"
                            >
                                {{ entity.mobile }}
                            </a>
                            <span v-else class="text-muted-foreground">-</span>
                        </div>
                    </div>

                    <Separator />

                    <!-- Email -->
                    <div class="space-y-1">
                        <div
                            class="flex items-center text-sm text-muted-foreground"
                        >
                            <MailIcon class="mr-2 h-4 w-4" />
                            Email
                        </div>
                        <div class="pl-6">
                            <a
                                v-if="entity.email"
                                :href="`mailto:${entity.email}`"
                                class="text-primary hover:underline"
                            >
                                {{ entity.email }}
                            </a>
                            <span v-else class="text-muted-foreground">-</span>
                        </div>
                    </div>

                    <Separator />

                    <!-- Website -->
                    <div class="space-y-1">
                        <div
                            class="flex items-center text-sm text-muted-foreground"
                        >
                            <GlobeIcon class="mr-2 h-4 w-4" />
                            Website
                        </div>
                        <div class="pl-6">
                            <a
                                v-if="entity.website"
                                :href="entity.website"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-primary hover:underline"
                            >
                                {{ entity.website }}
                                <ExternalLinkIcon class="ml-1 inline h-3 w-3" />
                            </a>
                            <span v-else class="text-muted-foreground">-</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- RGPD e Metadados -->
            <Card>
                <CardHeader>
                    <CardTitle>Informações Adicionais</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- RGPD -->
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-muted-foreground">
                            Consentimento RGPD
                        </div>
                        <Badge
                            :variant="
                                entity.gdpr_consent ? 'default' : 'secondary'
                            "
                        >
                            {{
                                entity.gdpr_consent
                                    ? 'Autorizado'
                                    : 'Não autorizado'
                            }}
                        </Badge>
                    </div>

                    <Separator />

                    <!-- Data de Criação -->
                    <div class="space-y-1">
                        <div class="text-sm text-muted-foreground">
                            Criado em
                        </div>
                        <div class="text-sm">
                            {{ formatDate(entity.created_at) }}
                        </div>
                    </div>

                    <Separator />

                    <!-- Data de Atualização -->
                    <div class="space-y-1">
                        <div class="text-sm text-muted-foreground">
                            Última atualização
                        </div>
                        <div class="text-sm">
                            {{ formatDate(entity.updated_at) }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
    </ShowWrapper>
</template>

<script setup lang="ts">
import ShowWrapper from '@/components/common/ShowWrapper.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import route from '@/routes/entities';
import { Entity } from '@/types';
import {
    ExternalLinkIcon,
    GlobeIcon,
    MailIcon,
    PhoneIcon,
    SmartphoneIcon,
} from 'lucide-vue-next';

interface Props {
    entity: Entity;
    type: 'client' | 'supplier';
}

const props = defineProps<Props>();

// Methods
const formatDate = (dateString: string) => {
    if (!dateString) return '-';

    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};
</script>
