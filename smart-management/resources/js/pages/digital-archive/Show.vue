<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="archive.name"
                :description="`Tipo: ${archive.document_type}`"
            >
                <div class="flex gap-2">
                    <Button variant="outline" @click="goBack">
                        <ArrowLeftIcon class="mr-2 h-4 w-4" />
                        Voltar
                    </Button>
                    <Button variant="outline" @click="handleDownload" v-if="fileExists">
                        <DownloadIcon class="mr-2 h-4 w-4" />
                        Download
                    </Button>
                    <Button variant="outline" @click="handleView" v-if="fileExists && canPreview">
                        <EyeIcon class="mr-2 h-4 w-4" />
                        Visualizar
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
                <div class="space-y-6 lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Detalhes do Ficheiro</CardTitle>
                                <Badge :variant="archive.is_public ? 'default' : 'secondary'">
                                    {{ archive.is_public ? 'Público' : 'Privado' }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Nome</div>
                                <div class="col-span-2 font-medium">{{ archive.name }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Ficheiro</div>
                                <div class="col-span-2 font-mono text-sm">{{ archive.file_name }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Tipo</div>
                                <div class="col-span-2">
                                    <Badge variant="outline">{{ archive.document_type }}</Badge>
                                </div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Tamanho</div>
                                <div class="col-span-2 font-medium">{{ formattedSize }}</div>
                            </div>
                            <Separator />
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm text-muted-foreground">Extensão</div>
                                <div class="col-span-2 uppercase font-mono">{{ extension }}</div>
                            </div>
                            <Separator v-if="archive.description" />
                            <div class="grid grid-cols-3 gap-4" v-if="archive.description">
                                <div class="text-sm text-muted-foreground">Descrição</div>
                                <div class="col-span-2 text-sm">{{ archive.description }}</div>
                            </div>
                            <Separator v-if="archive.expires_at" />
                            <div class="grid grid-cols-3 gap-4" v-if="archive.expires_at">
                                <div class="text-sm text-muted-foreground">Expira em</div>
                                <div class="col-span-2" :class="isExpired ? 'text-destructive font-medium' : ''">
                                    {{ formatDate(archive.expires_at) }}
                                    <span v-if="isExpired" class="ml-2 text-xs">(Expirado)</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Informações</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Enviado por</div>
                                <div class="text-sm font-medium">{{ archive.uploaded_by_user?.name || '-' }}</div>
                            </div>
                            <Separator />
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Enviado em</div>
                                <div class="text-sm">{{ formatDateTime(archive.created_at) }}</div>
                            </div>
                            <Separator />
                            <div class="space-y-1">
                                <div class="text-sm text-muted-foreground">Atualizado</div>
                                <div class="text-sm">{{ formatDateTime(archive.updated_at) }}</div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card v-if="!fileExists">
                        <CardContent class="p-6">
                            <div class="rounded-lg bg-destructive/10 p-4 text-sm text-destructive">
                                ⚠️ Ficheiro físico não encontrado no servidor
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
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftIcon, DownloadIcon, EyeIcon, PencilIcon, Trash2Icon } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    archive: any;
    fileExists: boolean;
    formattedSize: string;
    extension: string;
}

const props = defineProps<Props>();

const canPreview = computed(() => {
    const previewableTypes = ['image/', 'application/pdf'];
    return previewableTypes.some(type => props.archive.mime_type.startsWith(type));
});

const isExpired = computed(() => {
    return props.archive.expires_at && new Date(props.archive.expires_at) < new Date();
});

const goBack = () => router.get('/digital-archive');
const handleEdit = () => router.get(`/digital-archive/${props.archive.id}/edit`);

const handleDelete = () => {
    if (confirm(`Eliminar ficheiro "${props.archive.name}"?`)) {
        router.delete(`/digital-archive/${props.archive.id}`, {
            onSuccess: () => router.get('/digital-archive'),
        });
    }
};

const handleDownload = () => {
    window.open(`/digital-archive/${props.archive.id}/download`, '_blank');
};

const handleView = () => {
    window.open(`/digital-archive/${props.archive.id}/view`, '_blank');
};

const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-PT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
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


