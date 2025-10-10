<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Nova Ordem de Trabalho"
                description="Criar nova ordem de trabalho"
            >
                <Button variant="outline" @click="goBack">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Voltar
                </Button>
            </PageHeader>

            <Card>
                <CardContent class="p-6">
                    <form @submit="submitForm">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- Coluna 1 -->
                            <div class="space-y-6">
                                <!-- Título -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="title"
                                >
                                    <FormItem>
                                        <FormLabel>Título *</FormLabel>
                                        <FormControl>
                                            <Input
                                                placeholder="Título da ordem de trabalho"
                                                v-bind="componentField"
                                            />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Cliente -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="client_id"
                                >
                                    <FormItem>
                                        <FormLabel>Cliente *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue
                                                        placeholder="Selecione o cliente"
                                                    />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="client in clients"
                                                    :key="client.id"
                                                    :value="String(client.id)"
                                                >
                                                    {{ client.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Atribuído a -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="assigned_to"
                                >
                                    <FormItem>
                                        <FormLabel>Atribuído a *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue
                                                        placeholder="Selecione o utilizador"
                                                    />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="user in users"
                                                    :key="user.id"
                                                    :value="String(user.id)"
                                                >
                                                    {{ user.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Prioridade -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="priority"
                                >
                                    <FormItem>
                                        <FormLabel>Prioridade *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue
                                                        placeholder="Selecione a prioridade"
                                                    />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectItem value="low">Baixa</SelectItem>
                                                <SelectItem value="medium">Média</SelectItem>
                                                <SelectItem value="high">Alta</SelectItem>
                                                <SelectItem value="urgent">Urgente</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                            </div>

                            <!-- Coluna 2 -->
                            <div class="space-y-6">
                                <!-- Datas -->
                                <div class="grid grid-cols-2 gap-4">
                                    <FormField name="start_date">
                                        <FormItem>
                                            <FormLabel>Data de Início</FormLabel>
                                            <FormControl>
                                                <DatePicker
                                                    v-model="form.values.start_date"
                                                    placeholder="Selecione a data"
                                                />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    </FormField>

                                    <FormField name="end_date">
                                        <FormItem>
                                            <FormLabel>Data de Fim</FormLabel>
                                            <FormControl>
                                                <DatePicker
                                                    v-model="form.values.end_date"
                                                    placeholder="Selecione a data"
                                                />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    </FormField>
                                </div>

                                <!-- Estado -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="status"
                                >
                                    <FormItem>
                                        <FormLabel>Estado *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue
                                                        placeholder="Selecione o estado"
                                                    />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectItem value="pending">Pendente</SelectItem>
                                                <SelectItem value="in_progress">Em Progresso</SelectItem>
                                                <SelectItem value="completed">Concluído</SelectItem>
                                                <SelectItem value="cancelled">Cancelado</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <!-- Descrição -->
                                <FormField
                                    v-slot="{ componentField }"
                                    name="description"
                                >
                                    <FormItem>
                                        <FormLabel>Descrição</FormLabel>
                                        <FormControl>
                                            <Textarea
                                                placeholder="Descrição detalhada da ordem de trabalho"
                                                v-bind="componentField"
                                                rows="5"
                                            />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="mt-6 flex justify-end gap-3 border-t pt-6">
                            <Button type="button" variant="outline" @click="goBack">
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                                <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                                {{ isSubmitting ? 'A guardar...' : 'Guardar Ordem' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { workOrderSchema } from '@/schemas/workOrderSchema';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { ref } from 'vue';

const props = defineProps({
    clients: Array,
    users: Array,
});

const isSubmitting = ref(false);

const form = useForm({
    validationSchema: toTypedSchema(workOrderSchema),
    initialValues: {
        title: '',
        description: '',
        client_id: '',
        assigned_to: '',
        priority: 'medium',
        start_date: '',
        end_date: '',
        status: 'pending',
    },
});

const goBack = () => router.get('/work-orders');

const submitForm = form.handleSubmit(
    (values) => {
        isSubmitting.value = true;

        router.post('/work-orders', values, {
            preserveScroll: true,
            onFinish: () => (isSubmitting.value = false),
        });
    },
    (errors) => {
        console.log('Erros de validação:', errors);
    },
);
</script>




