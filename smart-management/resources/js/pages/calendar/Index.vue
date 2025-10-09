<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader
                title="Calendário"
                description="Gerir eventos e atividades"
            >
                <div class="flex gap-2">
                    <!-- Filtros -->
                    <Select
                        v-model="userFilter"
                        @update:modelValue="handleFilterChange"
                    >
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Utilizador" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Todos</SelectItem>
                            <SelectItem
                                v-for="user in users"
                                :key="user.id"
                                :value="String(user.id)"
                            >
                                {{ user.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        v-model="entityFilter"
                        @update:modelValue="handleFilterChange"
                    >
                        <SelectTrigger class="w-[200px]">
                            <SelectValue placeholder="Entidade" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Todas</SelectItem>
                            <SelectItem
                                v-for="entity in entities"
                                :key="entity.id"
                                :value="String(entity.id)"
                            >
                                {{ entity.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Button @click="openCreateDialog">
                        <PlusIcon class="mr-2 h-4 w-4" />
                        Novo Evento
                    </Button>
                </div>
            </PageHeader>

            <Card>
                <CardContent class="p-6">
                    <CalendarWrapper :options="calendarOptions" />
                </CardContent>
            </Card>
        </div>

        <!-- Dialog para Criar/Editar Evento -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{
                        editingEvent ? 'Editar Evento' : 'Novo Evento'
                    }}</DialogTitle>
                </DialogHeader>

                <form @submit.prevent="submitForm">
                    <div class="grid gap-4 py-4">
                        <!-- Data e Hora -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Data *</label>
                                <DatePicker
                                    v-model="formData.event_date"
                                    placeholder="Selecione a data"
                                />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Hora *</label>
                                <Input
                                    type="time"
                                    v-model="formData.event_time"
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium"
                                    >Duração (min) *</label
                                >
                                <Input
                                    type="number"
                                    min="1"
                                    v-model.number="formData.duration"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Entidade -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Entidade</label>
                            <select
                                v-model="formData.entity_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">Sem entidade</option>
                                <option
                                    v-for="entity in entities"
                                    :key="entity.id"
                                    :value="entity.id"
                                >
                                    {{ entity.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Tipo e Ação -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Tipo *</label>
                                <select
                                    v-model="formData.calendar_event_type_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option
                                        v-for="type in types"
                                        :key="type.id"
                                        :value="type.id"
                                    >
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium">Ação *</label>
                                <select
                                    v-model="formData.calendar_action_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option
                                        v-for="action in actions"
                                        :key="action.id"
                                        :value="action.id"
                                    >
                                        {{ action.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Descrição *</label>
                            <Textarea
                                v-model="formData.description"
                                placeholder="Descrição do evento"
                                rows="3"
                                required
                            />
                        </div>

                        <!-- Partilha e Conhecimento -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium"
                                    >Partilhar com</label
                                >
                                <div class="space-y-2">
                                    <div
                                        v-for="user in users"
                                        :key="user.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox
                                            :id="`user-${user.id}`"
                                            :checked="
                                                formData.shared_with.includes(
                                                    String(user.id),
                                                )
                                            "
                                            @update:checked="
                                                (checked: boolean) =>
                                                    toggleUser(String(user.id), checked)
                                            "
                                        />
                                        <label
                                            :for="`user-${user.id}`"
                                            class="text-sm cursor-pointer"
                                        >
                                            {{ user.name }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        id="knowledge"
                                        v-model:checked="formData.knowledge"
                                    />
                                    <label
                                        for="knowledge"
                                        class="text-sm font-medium cursor-pointer"
                                    >
                                        Conhecimento
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Estado *</label>
                            <select
                                v-model="formData.status"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="scheduled">Agendado</option>
                                <option value="completed">Concluído</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeDialog"
                        >
                            Cancelar
                        </Button>
                        <Button
                            v-if="editingEvent"
                            type="button"
                            variant="destructive"
                            @click="deleteEvent"
                        >
                            Eliminar
                        </Button>
                        <Button type="submit" :disabled="isSubmitting">
                            <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
                            <LoaderIcon
                                v-else
                                class="mr-2 h-4 w-4 animate-spin"
                            />
                            {{ isSubmitting ? 'A guardar...' : 'Guardar' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import CalendarWrapper from '@/components/calendar/CalendarWrapper.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import DatePicker from '@/components/ui/date-picker/DatePicker.vue';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
import { router } from '@inertiajs/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';
import { LoaderIcon, PlusIcon, SaveIcon } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

interface Props {
    events: any[];
    filters: {
        user_id?: string;
        entity_id?: string;
    };
    users: Array<{ id: number; name: string }>;
    entities: Array<{ id: number; name: string }>;
    types: Array<{ id: number; name: string; color: string }>;
    actions: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

// State
const isDialogOpen = ref(false);
const isSubmitting = ref(false);
const editingEvent = ref<any>(null);
const userFilter = ref(props.filters.user_id || 'all');
const entityFilter = ref(props.filters.entity_id || 'all');

// Form Data
const formData = reactive({
    event_date: '',
    event_time: '',
    duration: 60,
    shared_with: [] as string[],
    knowledge: false,
    entity_id: '',
    calendar_event_type_id: '',
    calendar_action_id: '',
    description: '',
    status: 'scheduled' as 'scheduled' | 'completed' | 'cancelled',
});

// Methods - Declarar antes de usar em calendarOptions
const handleFilterChange = () => {
    const params: any = {};

    if (userFilter.value && userFilter.value !== 'all') {
        params.user_id = userFilter.value;
    }

    if (entityFilter.value && entityFilter.value !== 'all') {
        params.entity_id = entityFilter.value;
    }

    router.get('/calendar', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const openCreateDialog = () => {
    resetForm();
    editingEvent.value = null;
    isDialogOpen.value = true;
};

const handleDateSelect = (selectInfo: any) => {
    resetForm();
    formData.event_date = selectInfo.startStr;
    formData.event_time = new Date(selectInfo.start).toLocaleTimeString('pt-PT', {
        hour: '2-digit',
        minute: '2-digit',
    });
    editingEvent.value = null;
    isDialogOpen.value = true;
};

const handleEventClick = async (clickInfo: any) => {
    try {
        const response = await fetch(`/calendar/${clickInfo.event.id}`);
        const data = await response.json();

        editingEvent.value = data.event;

        formData.event_date = data.event.event_date;
        formData.event_time = data.event.event_time.substring(0, 5);
        formData.duration = data.event.duration;
        formData.shared_with = data.event.shared_with || [];
        formData.knowledge = data.event.knowledge;
        formData.entity_id = String(data.event.entity_id || '');
        formData.calendar_event_type_id = String(
            data.event.calendar_event_type_id,
        );
        formData.calendar_action_id = String(data.event.calendar_action_id);
        formData.description = data.event.description;
        formData.status = data.event.status;

        isDialogOpen.value = true;
    } catch (error) {
        console.error('Erro ao carregar evento:', error);
    }
};

const handleEventDrop = (dropInfo: any) => {
    const event = dropInfo.event;
    updateEventDateTime(event.id, event.start);
};

const handleEventResize = (resizeInfo: any) => {
    const event = resizeInfo.event;
    const duration = Math.round(
        (event.end.getTime() - event.start.getTime()) / (1000 * 60),
    );
    updateEventDuration(event.id, duration);
};

const updateEventDateTime = (eventId: number, newDate: Date) => {
    router.put(
        `/calendar/${eventId}`,
        {
            event_date: newDate.toISOString().split('T')[0],
            event_time: newDate.toTimeString().substring(0, 5),
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};

const updateEventDuration = (eventId: number, duration: number) => {
    router.put(
        `/calendar/${eventId}`,
        { duration },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};

const toggleUser = (userId: string, checked: boolean | any) => {
    if (checked) {
        if (!formData.shared_with.includes(userId)) {
            formData.shared_with.push(userId);
        }
    } else {
        formData.shared_with = formData.shared_with.filter((id) => id !== userId);
    }
};

const resetForm = () => {
    formData.event_date = new Date().toISOString().split('T')[0];
    formData.event_time = '09:00';
    formData.duration = 60;
    formData.shared_with = [];
    formData.knowledge = false;
    formData.entity_id = '';
    formData.calendar_event_type_id = '';
    formData.calendar_action_id = '';
    formData.description = '';
    formData.status = 'scheduled';
};

const closeDialog = () => {
    isDialogOpen.value = false;
    editingEvent.value = null;
    resetForm();
};

const submitForm = () => {
    isSubmitting.value = true;

    if (editingEvent.value) {
        router.put(`/calendar/${editingEvent.value.id}`, formData, {
            preserveScroll: true,
            onSuccess: () => {
                isSubmitting.value = false;
                closeDialog();
            },
            onError: () => {
                isSubmitting.value = false;
            },
        });
    } else {
        router.post('/calendar', formData, {
            preserveScroll: true,
            onSuccess: () => {
                isSubmitting.value = false;
                closeDialog();
            },
            onError: () => {
                isSubmitting.value = false;
            },
        });
    }
};

const deleteEvent = () => {
    if (confirm('Tem certeza que deseja eliminar este evento?')) {
        router.delete(`/calendar/${editingEvent.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeDialog();
            },
        });
    }
};

// FullCalendar Options - Declarar após as funções
const calendarOptions = {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
    initialView: 'dayGridMonth',
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
    events: props.events,
    editable: true,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    select: handleDateSelect,
    eventClick: handleEventClick,
    eventDrop: handleEventDrop,
    eventResize: handleEventResize,
    height: 'auto',
};
</script>

