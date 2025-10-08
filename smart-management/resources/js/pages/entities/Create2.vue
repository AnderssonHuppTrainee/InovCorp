<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                :title="type === 'client' ? 'Novo Cliente' : 'Novo Fornecedor'"
                :description="`Registar novo ${type === 'client' ? 'cliente' : 'fornecedor'} no sistema`"
            >
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <EntityForm
                :countries="countries"
                :type="type"
                :on-submit="submitForm"
                submitLabel="Criar Entidade"
                @cancel="goBack"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import EntityForm from '@/components/entities/EntityForm.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeftIcon } from 'lucide-vue-next';
</script>
