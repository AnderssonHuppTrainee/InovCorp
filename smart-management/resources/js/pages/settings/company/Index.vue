<template>
    <Head title="Configurações da Empresa" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader title="Configurações da Empresa" description="Personalizar dados da empresa">
            </PageHeader>

            <form @submit.prevent="submitForm">
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle>Logotipo</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-6">
                            <div v-if="logoPreview || company.logo" class="flex-shrink-0">
                                <img :src="logoPreview || `/storage/${company.logo}`" alt="Logo" class="h-24 w-24 rounded object-contain border" />
                            </div>
                            <div class="flex-1 space-y-2">
                                <Input type="file" accept="image/*" @change="handleLogoChange" />
                                <p class="text-sm text-muted-foreground">Formato: PNG, JPG (máx. 2MB)</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle>Dados da Empresa</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Nome *</label>
                                <Input v-model="formData.name" required />
                            </div>

                            <div class="space-y-2 lg:col-span-2">
                                <label class="text-sm font-medium">Morada *</label>
                                <Input v-model="formData.address" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Código Postal *</label>
                                <Input v-model="formData.postal_code" placeholder="0000-000" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Localidade *</label>
                                <Input v-model="formData.city" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Número Contribuinte *</label>
                                <Input v-model="formData.tax_number" required />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Telefone</label>
                                <Input v-model="formData.phone" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Email</label>
                                <Input type="email" v-model="formData.email" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Website</label>
                                <Input v-model="formData.website" placeholder="https://" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button type="submit" :disabled="isSubmitting">
                        <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                        <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                        {{ isSubmitting ? 'A guardar...' : 'Guardar Alterações' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

interface Props {
    company: any;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Configurações da Empresa',
        href: '/settings/company',
    },
];

const isSubmitting = ref(false);
const logoFile = ref<File | null>(null);
const logoPreview = ref<string | null>(null);

const formData = reactive({
    name: props.company.name,
    address: props.company.address,
    postal_code: props.company.postal_code,
    city: props.company.city,
    tax_number: props.company.tax_number,
    phone: props.company.phone || '',
    email: props.company.email || '',
    website: props.company.website || '',
});

const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        logoFile.value = target.files[0];
        logoPreview.value = URL.createObjectURL(target.files[0]);
    }
};

const submitForm = () => {
    isSubmitting.value = true;

    const data = new FormData();
    Object.entries(formData).forEach(([key, value]) => {
        data.append(key, value);
    });
    if (logoFile.value) data.append('logo', logoFile.value);
    data.append('_method', 'PUT');

    router.post('/settings/company', data, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
};
</script>




