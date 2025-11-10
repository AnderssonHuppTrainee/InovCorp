<template>
    <ShowWrapper
        :title="`${contact.first_name} ${contact.last_name}`"
        :description="`Contacto #${contact.number}`"
        :edit-url="route.edit({ id: contact.id }).url"
        :delete-url="route.destroy({ id: contact.id }).url"
        :back-url="route.index().url"
    >
        <template #main-content>
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Informações Principais</CardTitle>
                        <Badge
                            :variant="
                                contact.status === 'active'
                                    ? 'default'
                                    : 'secondary'
                            "
                        >
                            {{
                                contact.status === 'active'
                                    ? 'Ativo'
                                    : 'Inativo'
                            }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Nome -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Nome</div>
                        <div class="col-span-2 font-medium">
                            {{ contact.first_name }}
                        </div>
                    </div>

                    <Separator />

                    <!-- Apelido -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Apelido</div>
                        <div class="col-span-2 font-medium">
                            {{ contact.last_name }}
                        </div>
                    </div>

                    <Separator />

                    <!-- Entidade -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">
                            Entidade
                        </div>
                        <div class="col-span-2">
                            <a
                                v-if="contact.entity"
                                :href="`/entities/${contact.entity.id}`"
                                class="text-primary hover:underline"
                            >
                                {{ contact.entity.name }}
                            </a>
                            <span v-else>-</span>
                        </div>
                    </div>

                    <Separator />

                    <!-- Função -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm text-muted-foreground">Função</div>
                        <div class="col-span-2">
                            <Badge variant="outline">
                                {{ contact.role?.name || '-' }}
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Observações -->
            <Card v-if="contact.observations">
                <CardHeader>
                    <CardTitle>Observações</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm whitespace-pre-line">
                        {{ contact.observations }}
                    </p>
                </CardContent>
            </Card>
        </template>

        <template #sidebar>
            <div class="space-y-6">
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
                                    v-if="contact.phone"
                                    :href="`tel:${contact.phone}`"
                                    class="text-primary hover:underline"
                                >
                                    {{ contact.phone }}
                                </a>
                                <span v-else class="text-muted-foreground"
                                    >-</span
                                >
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
                                    v-if="contact.mobile"
                                    :href="`tel:${contact.mobile}`"
                                    class="text-primary hover:underline"
                                >
                                    {{ contact.mobile }}
                                </a>
                                <span v-else class="text-muted-foreground"
                                    >-</span
                                >
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
                                    v-if="contact.email"
                                    :href="`mailto:${contact.email}`"
                                    class="text-primary hover:underline"
                                >
                                    {{ contact.email }}
                                </a>
                                <span v-else class="text-muted-foreground"
                                    >-</span
                                >
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Informações Adicionais -->
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
                                    contact.gdpr_consent
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    contact.gdpr_consent
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
                                {{ formatDate(contact.created_at) }}
                            </div>
                        </div>

                        <Separator />

                        <!-- Data de Atualização -->
                        <div class="space-y-1">
                            <div class="text-sm text-muted-foreground">
                                Última atualização
                            </div>
                            <div class="text-sm">
                                {{ formatDate(contact.updated_at) }}
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
import route from '@/routes/contacts';
import { Contact } from '@/types';
import { MailIcon, PhoneIcon, SmartphoneIcon } from 'lucide-vue-next';

interface Props {
    contact: Contact;
}

const props = defineProps<Props>();

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
