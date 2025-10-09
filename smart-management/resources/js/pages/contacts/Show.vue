<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="`${contact.first_name} ${contact.last_name}`"
                :description="`Contacto #${contact.number}`"
            >
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack">
                        <ArrowLeftIcon class="mr-2 h-4 w-4" />
                        Voltar
                    </Button>
                    <Button @click="handleEdit">
                        <PencilIcon class="mr-2 h-4 w-4" />
                        Editar
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2Icon class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </PageHeader>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Coluna Principal (2/3) -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Informações Principais -->
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
                                <div class="text-sm text-muted-foreground">
                                    Nome
                                </div>
                                <div class="col-span-2 font-medium">
                                    {{ contact.first_name }}
                                </div>
                            </div>

                            <Separator />

                            <!-- Apelido -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">
                                    Apelido
                                </div>
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
                                <div class="text-sm text-muted-foreground">
                                    Função
                                </div>
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
                            <p class="whitespace-pre-line text-sm">
                                {{ contact.observations }}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Coluna Lateral (1/3) -->
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
                                    <span
                                        v-else
                                        class="text-muted-foreground"
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
                                    <span
                                        v-else
                                        class="text-muted-foreground"
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
                                    <span
                                        v-else
                                        class="text-muted-foreground"
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
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    MailIcon,
    PencilIcon,
    PhoneIcon,
    SmartphoneIcon,
    Trash2Icon,
} from 'lucide-vue-next';

interface Contact {
    id: number;
    number: string;
    first_name: string;
    last_name: string;
    entity_id: number;
    entity?: {
        id: number;
        name: string;
    };
    role?: {
        id: number;
        name: string;
    };
    phone: string | null;
    mobile: string | null;
    email: string | null;
    gdpr_consent: boolean;
    observations: string | null;
    status: 'active' | 'inactive';
    created_at: string;
    updated_at: string;
}

interface Props {
    contact: Contact;
}

const props = defineProps<Props>();

// Methods
const goBack = () => {
    router.get('/contacts');
};

const handleEdit = () => {
    router.get(`/contacts/${props.contact.id}/edit`);
};

const handleDelete = () => {
    if (
        confirm(
            `Tem certeza que deseja eliminar "${props.contact.first_name} ${props.contact.last_name}"?\n\nEsta ação não pode ser desfeita.`
        )
    ) {
        router.delete(`/contacts/${props.contact.id}`, {
            onSuccess: () => {
                router.get('/contacts');
            },
        });
    }
};

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




