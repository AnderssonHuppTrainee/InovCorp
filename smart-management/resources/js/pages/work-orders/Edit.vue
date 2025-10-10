<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Editar Ordem de Trabalho"
                :description="`OT #${workOrder.number}`"
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
                            <div class="space-y-6">
                                <FormField v-slot="{ componentField }" name="title">
                                    <FormItem>
                                        <FormLabel>Título *</FormLabel>
                                        <FormControl>
                                            <Input placeholder="Título" v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <FormField v-slot="{ componentField }" name="client_id">
                                    <FormItem>
                                        <FormLabel>Cliente *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Selecione" />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectItem v-for="client in clients" :key="client.id" :value="String(client.id)">
                                                    {{ client.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <FormField v-slot="{ componentField }" name="assigned_to">
                                    <FormItem>
                                        <FormLabel>Atribuído a *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Selecione" />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectItem v-for="user in users" :key="user.id" :value="String(user.id)">
                                                    {{ user.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>

                                <FormField v-slot="{ componentField }" name="priority">
                                    <FormItem>
                                        <FormLabel>Prioridade *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Selecione" />
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

                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <FormField name="start_date">
                                        <FormItem>
                                            <FormLabel>Data Início</FormLabel>
                                            <FormControl>
                                                <DatePicker v-model="form.values.start_date" placeholder="Início" />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    </FormField>

                                    <FormField name="end_date">
                                        <FormItem>
                                            <FormLabel>Data Fim</FormLabel>
                                            <FormControl>
                                                <DatePicker v-model="form.values.end_date" placeholder="Fim" />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    </FormField>
                                </div>

                                <FormField v-slot="{ componentField }" name="status">
                                    <FormItem>
                                        <FormLabel>Estado *</FormLabel>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Selecione" />
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

                                <FormField v-slot="{ componentField }" name="description">
                                    <FormItem>
                                        <FormLabel>Descrição</FormLabel>
                                        <FormControl>
                                            <Textarea placeholder="Descrição" v-bind="componentField" rows="5" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 border-t pt-6">
                            <Button type="button" variant="outline" @click="goBack">Cancelar</Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                                <LoaderIcon v-else class="mr-2 h-4 w-4 animate-spin" />
                                {{ isSubmitting ? 'A atualizar...' : 'Atualizar' }}
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
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { workOrderSchema } from '@/schemas/workOrderSchema';
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { ArrowLeftIcon, LoaderIcon, SaveIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { ref } from 'vue';

const props = defineProps({
    workOrder: Object,
    clients: Array,
    users: Array,
});

const isSubmitting = ref(false);

const form = useForm({
    validationSchema: toTypedSchema(workOrderSchema),
    initialValues: {
        title: props.workOrder.title || '',
        description: props.workOrder.description || '',
        client_id: String(props.workOrder.client_id || ''),
        assigned_to: String(props.workOrder.assigned_to || ''),
        priority: props.workOrder.priority || 'medium',
        start_date: props.workOrder.start_date || '',
        end_date: props.workOrder.end_date || '',
        status: props.workOrder.status || 'pending',
    },
});

const goBack = () => router.get('/work-orders');

const submitForm = form.handleSubmit((values) => {
    isSubmitting.value = true;
    router.put(`/work-orders/${props.workOrder.id}`, values, {
        preserveScroll: true,
        onFinish: () => (isSubmitting.value = false),
    });
});
</script>




