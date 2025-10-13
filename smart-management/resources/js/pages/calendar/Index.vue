<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    FormControl,
    FormDescription,
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
import calendar from '@/routes/calendar';
import { calendarEventSchema } from '@/schemas/calendarEventSchema';
import { type BreadcrumbItem } from '@/types';
import type {
    CalendarOptions,
    DateSelectArg,
    EventClickArg,
    EventDropArg,
    EventInput,
} from '@fullcalendar/core';
import ptLocale from '@fullcalendar/core/locales/pt';
import dayGridPlugin from '@fullcalendar/daygrid';
import type { EventResizeDoneArg } from '@fullcalendar/interaction';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';
import FullCalendar from '@fullcalendar/vue3';
import { Head, router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { FilterIcon, Loader2Icon, PlusIcon, XIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, ref } from 'vue';

interface CalendarEvent {
    id: string | number;
    title: string;
    start: string;
    end: string;
    backgroundColor?: string;
    borderColor?: string;
    extendedProps?: {
        entity_id: number | null;
        entity_name: string | null;
        type_name: string | null;
        action_name: string | null;
        status: string;
        knowledge: boolean;
    };
}

interface Props {
    events: CalendarEvent[];
    filters: {
        user_id?: number;
        entity_id?: number;
        type_id?: number;
        status?: string;
    };
    users: Array<{ id: number; name: string }>;
    entities: Array<{ id: number; name: string }>;
    types: Array<{ id: number; name: string; color: string }>;
    actions: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

// Refs
const calendarRef = ref();
const showEventDialog = ref(false);
const showFilters = ref(false);
const isEditMode = ref(false);
const selectedEventId = ref<number | null>(null);
const isSubmitting = ref(false);

// Filters
const userFilter = ref(props.filters.user_id?.toString() || 'all');
const entityFilter = ref(props.filters.entity_id?.toString() || 'all');
const typeFilter = ref(props.filters.type_id?.toString() || 'all');
const statusFilter = ref(props.filters.status || 'all');

// Form
const form = useForm({
    validationSchema: toTypedSchema(calendarEventSchema),
    initialValues: {
        event_date: '',
        event_time: '',
        duration: 60,
        shared_with: [],
        knowledge: false,
        entity_id: undefined,
        calendar_event_type_id: '',
        calendar_action_id: '',
        description: '',
        status: 'scheduled',
    },
});

// FullCalendar configuration
const calendarOptions = computed<CalendarOptions>(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
    initialView: 'dayGridMonth',
    locale: ptLocale,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    },
    buttonText: {
        today: 'Hoje',
        month: 'Mês',
        week: 'Semana',
        day: 'Dia',
        list: 'Lista',
    },
    height: 'auto',
    events: props.events.map((event) => ({
        id: String(event.id),
        title: event.title,
        start: event.start,
        end: event.end,
        backgroundColor: event.backgroundColor,
        borderColor: event.borderColor,
        extendedProps: event.extendedProps,
    })) as EventInput[],
    editable: true,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    select: handleDateSelect,
    eventClick: handleEventClick,
    eventDrop: handleEventDrop,
    eventResize: handleEventResize,
    eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    },
}));

// Handlers
const handleDateSelect = (selectInfo: DateSelectArg) => {
    const calendarApi = selectInfo.view.calendar;
    calendarApi.unselect();

    // Preencher formulário com data/hora selecionada
    const startDate = new Date(selectInfo.start);
    form.setFieldValue('event_date', selectInfo.startStr.split('T')[0]);
    form.setFieldValue('event_time', startDate.toTimeString().substring(0, 5));

    isEditMode.value = false;
    selectedEventId.value = null;
    form.resetForm({
        values: {
            event_date: selectInfo.startStr.split('T')[0],
            event_time:
                startDate.getHours().toString().padStart(2, '0') + ':00',
            duration: 60,
            shared_with: [],
            knowledge: false,
            entity_id: undefined,
            calendar_event_type_id: '',
            calendar_action_id: '',
            description: '',
            status: 'scheduled',
        },
    });

    showEventDialog.value = true;
};

const handleEventClick = (clickInfo: EventClickArg) => {
    const eventId = parseInt(clickInfo.event.id);
    selectedEventId.value = eventId;

    // Buscar detalhes do evento
    fetch(calendar.show(eventId).url)
        .then((res) => res.json())
        .then((data) => {
            console.log('Event loaded:', data);
            isEditMode.value = true;

            // Convert shared_with array to strings (user IDs)
            const sharedWith = Array.isArray(data.event.shared_with)
                ? data.event.shared_with.map((id: number) => id.toString())
                : [];

            // Ensure event_time is in HH:MM format
            let eventTime = data.event.event_time;
            if (eventTime && eventTime.length > 5) {
                // If it's in HH:MM:SS format, extract only HH:MM
                eventTime = eventTime.substring(0, 5);
            }

            form.resetForm({
                values: {
                    event_date: data.event.event_date,
                    event_time: eventTime,
                    duration: data.event.duration,
                    shared_with: sharedWith,
                    knowledge: data.event.knowledge,
                    entity_id: data.event.entity_id?.toString(),
                    calendar_event_type_id:
                        data.event.calendar_event_type_id.toString(),
                    calendar_action_id:
                        data.event.calendar_action_id.toString(),
                    description: data.event.description,
                    status: data.event.status,
                },
            });

            showEventDialog.value = true;
        })
        .catch((err) => {
            console.error('Error loading event:', err);
        });
};

const handleEventDrop = (dropInfo: EventDropArg) => {
    const eventId = parseInt(dropInfo.event.id);
    const newStart = dropInfo.event.start!;

    router.put(
        calendar.update(eventId).url,
        {
            event_date: newStart.toISOString().split('T')[0],
            event_time: newStart.toTimeString().substring(0, 5),
        },
        {
            preserveState: true,
            onError: () => {
                dropInfo.revert();
            },
        },
    );
};

const handleEventResize = (resizeInfo: EventResizeDoneArg) => {
    const eventId = parseInt(resizeInfo.event.id);
    const start = resizeInfo.event.start!;
    const end = resizeInfo.event.end!;
    const durationMinutes = Math.round(
        (end.getTime() - start.getTime()) / (1000 * 60),
    );

    router.put(
        calendar.update(eventId).url,
        {
            duration: durationMinutes,
        },
        {
            preserveState: true,
            onError: () => {
                resizeInfo.revert();
            },
        },
    );
};

const submitForm = form.handleSubmit((values) => {
    // Prevent double submission
    if (isSubmitting.value) {
        console.log('Already submitting, ignoring duplicate submit');
        return;
    }

    isSubmitting.value = true;

    // Transform shared_with to numbers for backend
    const payload = {
        ...values,
        shared_with: Array.isArray(values.shared_with)
            ? values.shared_with.map((id) =>
                  typeof id === 'string' ? parseInt(id) : id,
              )
            : [],
    };

    console.log('Submitting form:', {
        isEditMode: isEditMode.value,
        selectedEventId: selectedEventId.value,
        payload,
        event_time_type: typeof payload.event_time,
        event_time_value: payload.event_time,
    });

    if (isEditMode.value && selectedEventId.value) {
        router.put(calendar.update(selectedEventId.value).url, payload, {
            preserveState: true,
            preserveScroll: true,
            only: ['events'],
            onSuccess: (page) => {
                console.log('Update success');
                showEventDialog.value = false;
            },
            onError: (errors) => {
                console.error('Update error:', errors);
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    } else {
        router.post(calendar.store().url, payload, {
            preserveState: true,
            preserveScroll: true,
            only: ['events'],
            onSuccess: (page) => {
                console.log('Create success');
                showEventDialog.value = false;
            },
            onError: (errors) => {
                console.error('Create error:', errors);
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    }
});

const deleteEvent = () => {
    if (
        selectedEventId.value &&
        confirm('Tem certeza que deseja eliminar este evento?')
    ) {
        router.delete(calendar.destroy(selectedEventId.value).url, {
            preserveState: true,
            onSuccess: () => {
                showEventDialog.value = false;
            },
        });
    }
};

const applyFilters = () => {
    router.get(
        calendar.index().url,
        {
            user_id: userFilter.value !== 'all' ? userFilter.value : undefined,
            entity_id:
                entityFilter.value !== 'all' ? entityFilter.value : undefined,
            type_id: typeFilter.value !== 'all' ? typeFilter.value : undefined,
            status:
                statusFilter.value !== 'all' ? statusFilter.value : undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilters = () => {
    userFilter.value = 'all';
    entityFilter.value = 'all';
    typeFilter.value = 'all';
    statusFilter.value = 'all';
    router.get(
        calendar.index().url,
        {},
        { preserveState: true, replace: true },
    );
};

const hasFilters = computed(() => {
    return (
        userFilter.value !== 'all' ||
        entityFilter.value !== 'all' ||
        typeFilter.value !== 'all' ||
        statusFilter.value !== 'all'
    );
});

const handleKnowledgeChange = (e: any) => {
    const checked = e.target.checked;
    form.setFieldValue('knowledge', checked);
};

const handleSharedWithChange = (userId: string, event: Event) => {
    const checked = (event.target as HTMLInputElement).checked;
    const currentShared = form.values.shared_with || [];

    if (checked) {
        // Add user to shared_with
        form.setFieldValue('shared_with', [...currentShared, userId]);
    } else {
        // Remove user from shared_with
        form.setFieldValue(
            'shared_with',
            currentShared.filter((id) => id !== userId),
        );
    }
};
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Calendário',
        href: '#',
    },
];
</script>

<template>
    <Head title="Calendário" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <PageHeader
                title="Calendário"
                description="Gerir eventos e compromissos"
            >
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        @click="showFilters = !showFilters"
                    >
                        <FilterIcon class="mr-2 h-4 w-4" />
                        {{ showFilters ? 'Ocultar' : 'Mostrar' }} Filtros
                    </Button>
                    <Button
                        @click="
                            showEventDialog = true;
                            isEditMode = false;
                            form.resetForm();
                        "
                    >
                        <PlusIcon class="mr-2 h-4 w-4" />
                        Novo Evento
                    </Button>
                </div>
            </PageHeader>

            <!-- Filtros -->
            <Card v-if="showFilters">
                <CardHeader>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center"
                    >
                        <div class="flex flex-1 flex-wrap gap-2">
                            <Select
                                v-model="userFilter"
                                @update:model-value="applyFilters"
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Utilizador" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all"
                                        >Todos os Utilizadores</SelectItem
                                    >
                                    <SelectItem
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="user.id.toString()"
                                    >
                                        {{ user.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Select
                                v-model="entityFilter"
                                @update:model-value="applyFilters"
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Entidade" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all"
                                        >Todas as Entidades</SelectItem
                                    >
                                    <SelectItem
                                        v-for="entity in entities"
                                        :key="entity.id"
                                        :value="entity.id.toString()"
                                    >
                                        {{ entity.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Select
                                v-model="typeFilter"
                                @update:model-value="applyFilters"
                            >
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Tipo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all"
                                        >Todos os Tipos</SelectItem
                                    >
                                    <SelectItem
                                        v-for="type in types"
                                        :key="type.id"
                                        :value="type.id.toString()"
                                    >
                                        {{ type.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Select
                                v-model="statusFilter"
                                @update:model-value="applyFilters"
                            >
                                <SelectTrigger class="w-[150px]">
                                    <SelectValue placeholder="Estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="scheduled"
                                        >Agendado</SelectItem
                                    >
                                    <SelectItem value="completed"
                                        >Concluído</SelectItem
                                    >
                                    <SelectItem value="cancelled"
                                        >Cancelado</SelectItem
                                    >
                                </SelectContent>
                            </Select>

                            <Button
                                variant="ghost"
                                @click="clearFilters"
                                v-if="hasFilters"
                            >
                                <XIcon class="mr-2 h-4 w-4" />
                                Limpar
                            </Button>
                        </div>
                    </div>
                </CardHeader>
            </Card>

            <!-- Calendário -->
            <Card>
                <CardContent class="p-6">
                    <FullCalendar
                        ref="calendarRef"
                        :options="calendarOptions"
                    />
                </CardContent>
            </Card>

            <!-- Dialog Criar/Editar Evento -->
            <Dialog v-model:open="showEventDialog">
                <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>{{
                            isEditMode ? 'Editar Evento' : 'Novo Evento'
                        }}</DialogTitle>
                        <DialogDescription>
                            {{
                                isEditMode
                                    ? 'Atualizar informações do evento'
                                    : 'Criar um novo evento no calendário'
                            }}
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit="submitForm" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Data -->
                            <FormField
                                v-slot="{ componentField }"
                                name="event_date"
                            >
                                <FormItem>
                                    <FormLabel>Data *</FormLabel>
                                    <FormControl>
                                        <DatePicker
                                            v-bind="componentField"
                                            placeholder="Selecione a data"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Hora -->
                            <FormField
                                v-slot="{ componentField }"
                                name="event_time"
                            >
                                <FormItem>
                                    <FormLabel>Hora *</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="time"
                                            v-bind="componentField"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>

                        <!-- Duração e Tipo -->
                        <div class="grid grid-cols-2 gap-4">
                            <FormField
                                v-slot="{ componentField }"
                                name="duration"
                            >
                                <FormItem>
                                    <FormLabel>Duração (min) *</FormLabel>
                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue
                                                    placeholder="Selecione a duração"
                                                />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="15"
                                                >15 minutos</SelectItem
                                            >
                                            <SelectItem value="30"
                                                >30 minutos</SelectItem
                                            >
                                            <SelectItem value="45"
                                                >45 minutos</SelectItem
                                            >
                                            <SelectItem value="60"
                                                >1 hora</SelectItem
                                            >
                                            <SelectItem value="90"
                                                >1h 30min</SelectItem
                                            >
                                            <SelectItem value="120"
                                                >2 horas</SelectItem
                                            >
                                            <SelectItem value="180"
                                                >3 horas</SelectItem
                                            >
                                            <SelectItem value="240"
                                                >4 horas</SelectItem
                                            >
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField
                                v-slot="{ componentField }"
                                name="calendar_event_type_id"
                            >
                                <FormItem>
                                    <FormLabel>Tipo *</FormLabel>
                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue
                                                    placeholder="Selecione o tipo"
                                                />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="type in types"
                                                :key="type.id"
                                                :value="type.id.toString()"
                                            >
                                                {{ type.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>

                        <!-- Ação e Entidade -->
                        <div class="grid grid-cols-2 gap-4">
                            <FormField
                                v-slot="{ componentField }"
                                name="calendar_action_id"
                            >
                                <FormItem>
                                    <FormLabel>Ação *</FormLabel>
                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue
                                                    placeholder="Selecione a ação"
                                                />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="action in actions"
                                                :key="action.id"
                                                :value="action.id.toString()"
                                            >
                                                {{ action.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField
                                v-slot="{ componentField }"
                                name="entity_id"
                            >
                                <FormItem>
                                    <FormLabel>Entidade</FormLabel>
                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue
                                                    placeholder="Nenhuma entidade"
                                                />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="entity in entities"
                                                :key="entity.id"
                                                :value="entity.id.toString()"
                                            >
                                                {{ entity.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <FormDescription
                                        >Opcional - associar este evento a uma
                                        entidade</FormDescription
                                    >
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>

                        <!-- Descrição -->
                        <FormField
                            v-slot="{ componentField }"
                            name="description"
                        >
                            <FormItem>
                                <FormLabel>Descrição *</FormLabel>
                                <FormControl>
                                    <Textarea
                                        placeholder="Descrição do evento"
                                        v-bind="componentField"
                                        rows="3"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Partilhado Com -->
                        <FormField name="shared_with">
                            <FormItem>
                                <FormLabel>Partilhado com</FormLabel>
                                <FormControl>
                                    <div class="space-y-2">
                                        <div
                                            v-for="user in users"
                                            :key="user.id"
                                            class="flex items-center space-x-2"
                                        >
                                            <input
                                                type="checkbox"
                                                :id="`share-${user.id}`"
                                                :value="user.id.toString()"
                                                :checked="
                                                    form.values.shared_with?.includes(
                                                        user.id.toString(),
                                                    )
                                                "
                                                @change="
                                                    handleSharedWithChange(
                                                        user.id.toString(),
                                                        $event,
                                                    )
                                                "
                                                class="h-4 w-4 shrink-0 cursor-pointer rounded-sm border border-primary ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                            />
                                            <label
                                                :for="`share-${user.id}`"
                                                class="cursor-pointer text-sm"
                                            >
                                                {{ user.name }}
                                            </label>
                                        </div>
                                    </div>
                                </FormControl>
                                <FormDescription
                                    >Selecione os utilizadores que terão acesso
                                    a este evento</FormDescription
                                >
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <!-- Conhecimento -->
                        <div
                            class="flex items-center space-x-3 rounded-lg border p-4"
                        >
                            <input
                                type="checkbox"
                                id="knowledge"
                                :checked="form.values.knowledge"
                                @change="handleKnowledgeChange"
                                class="peer h-4 w-4 shrink-0 cursor-pointer rounded-sm border border-primary ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <div class="grid gap-1.5 leading-none">
                                <label
                                    for="knowledge"
                                    class="cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    Conhecimento
                                </label>
                                <p class="text-sm text-muted-foreground">
                                    Marcar este evento como conhecimento
                                    importante
                                </p>
                            </div>
                        </div>

                        <!-- Estado (apenas em edição) -->
                        <FormField
                            v-if="isEditMode"
                            v-slot="{ componentField }"
                            name="status"
                        >
                            <FormItem>
                                <FormLabel>Estado</FormLabel>
                                <Select v-bind="componentField">
                                    <FormControl>
                                        <SelectTrigger>
                                            <SelectValue />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectItem value="scheduled"
                                            >Agendado</SelectItem
                                        >
                                        <SelectItem value="completed"
                                            >Concluído</SelectItem
                                        >
                                        <SelectItem value="cancelled"
                                            >Cancelado</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <DialogFooter class="gap-2">
                            <Button
                                v-if="isEditMode"
                                type="button"
                                variant="destructive"
                                @click="deleteEvent"
                            >
                                Eliminar
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="showEventDialog = false"
                            >
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="isSubmitting">
                                <Loader2Icon
                                    v-if="isSubmitting"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                {{ isEditMode ? 'Atualizar' : 'Criar' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>

<style>
/* FullCalendar Styles */
:root {
    --fc-border-color: hsl(var(--border));
    --fc-button-bg-color: hsl(var(--primary));
    --fc-button-border-color: hsl(var(--primary));
    --fc-button-hover-bg-color: hsl(var(--primary) / 0.9);
    --fc-button-hover-border-color: hsl(var(--primary) / 0.9);
    --fc-button-active-bg-color: hsl(var(--primary) / 0.8);
    --fc-button-active-border-color: hsl(var(--primary) / 0.8);
    --fc-event-bg-color: hsl(var(--primary));
    --fc-event-border-color: hsl(var(--primary));
    --fc-event-text-color: hsl(var(--primary-foreground));
    --fc-today-bg-color: hsl(var(--accent));
}

.fc {
    font-family: inherit;
}

.fc .fc-button {
    text-transform: capitalize;
    font-weight: 500;
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
}

.fc .fc-button:focus {
    outline: none;
    box-shadow: 0 0 0 2px hsl(var(--ring));
}

.fc .fc-toolbar-title {
    font-size: 1.5rem;
    font-weight: 600;
}

.fc-theme-standard td,
.fc-theme-standard th {
    border-color: hsl(var(--border));
}

.fc .fc-daygrid-day-number {
    color: hsl(var(--foreground));
}

.fc .fc-col-header-cell-cushion {
    color: hsl(var(--muted-foreground));
    font-weight: 600;
}

.fc-event {
    cursor: pointer;
    border-radius: 0.25rem;
}

.fc-event:hover {
    opacity: 0.9;
}
</style>
