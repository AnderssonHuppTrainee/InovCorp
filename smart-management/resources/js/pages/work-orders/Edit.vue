<template>
    <FormWrapper
        title="Editar Ordem de Trabalho"
        :description="`${workOrder.number} - ${workOrder.title}`"
        :schema="workOrderSchema"
        :initial-values="initialValues"
        :submit-url="`/work-orders/${workOrder.id}`"
        submit-method="put"
        submit-text="Atualizar Ordem de Trabalho"
    >
        <template #form-fields>
            <FormField v-slot="{ componentField }" name="title">
                <FormItem>
                    <FormLabel>Título *</FormLabel>
                    <FormControl>
                        <Input
                            v-bind="componentField"
                            placeholder="Título da ordem de trabalho"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="priority">
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
            <FormField v-slot="{ componentField }" name="start_date">
                <FormItem>
                    <FormLabel>Data de Inicio</FormLabel>
                    <FormControl>
                        <DatePicker v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="end_date">
                <FormItem>
                    <FormLabel>Data de Vencimento</FormLabel>
                    <FormControl>
                        <DatePicker v-bind="componentField" />
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

            <FormField v-slot="{ componentField }" name="assigned_to">
                <FormItem>
                    <FormLabel>Atribuído a *</FormLabel>
                    <Select v-bind="componentField">
                        <FormControl>
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Selecione o responsável"
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

            <FormField v-slot="{ componentField }" name="status">
                <FormItem>
                    <FormLabel>Estado *</FormLabel>
                    <Select v-bind="componentField">
                        <FormControl>
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione o estado" />
                            </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                            <SelectItem value="pending">Pendente</SelectItem>
                            <SelectItem value="in_progress"
                                >Em Progresso</SelectItem
                            >
                            <SelectItem value="completed">Concluída</SelectItem>
                            <SelectItem value="cancelled">Cancelada</SelectItem>
                        </SelectContent>
                    </Select>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField
                v-slot="{ componentField }"
                name="description"
                class="lg:col-span-2"
            >
                <FormItem>
                    <FormLabel>Descrição</FormLabel>
                    <FormControl>
                        <Textarea
                            v-bind="componentField"
                            rows="4"
                            placeholder="Descrição detalhada da ordem de trabalho"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
        </template>
    </FormWrapper>
</template>

<script setup lang="ts">
import FormWrapper from '@/components/common/FormWrapper.vue';
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
import { workOrderSchema } from '@/schemas/workOrderSchema';
import { computed } from 'vue';

interface Props {
    workOrder: {
        id: number;
        number: string;
        title: string;
        description: string | null;
        priority: string;
        status: string;
        client_id: number;
        assigned_to: number | null;
        start_date: string | null;
        end_date: string | null;
    };
    clients: Array<{ id: number; name: string }>;
    users: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

const initialValues = computed(() => ({
    title: props.workOrder.title,
    description: props.workOrder.description || '',
    client_id: String(props.workOrder.client_id),
    assigned_to: String(props.workOrder.assigned_to) || '',
    priority: props.workOrder.priority,
    start_date: props.workOrder.start_date || '',
    end_date: props.workOrder.end_date || '',
    status: props.workOrder.status,
}));
</script>
