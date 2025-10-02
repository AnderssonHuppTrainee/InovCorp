<script setup lang="ts">
import type { ReturnTypeUseTaskFilters } from '@/composables/useTaskFilters';
import { Search } from 'lucide-vue-next';

const props = defineProps<{
    filters: ReturnTypeUseTaskFilters['filters'];
    pushFilters: ReturnTypeUseTaskFilters['pushFilters'];
}>();
</script>

<template>
    <div class="rounded-lg bg-white p-4 shadow-lg">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
            <div class="relative">
                <Search
                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400"
                />
                <input
                    type="text"
                    v-model="props.filters.search"
                    placeholder="Buscar por título..."
                    class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label class="mr-1 block text-sm text-gray-600">Estado:</label>
                <select
                    v-model="props.filters.status"
                    @update:modelValue="props.pushFilters"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Todos</option>
                    <option value="pending">Pendente</option>
                    <option value="completed">Concluída</option>
                </select>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label class="mr-1 block text-sm text-gray-600"
                    >Prioridade:</label
                >
                <select
                    v-model="props.filters.priority"
                    @update:modelValue="props.pushFilters"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Todas</option>
                    <option value="low">Baixa</option>
                    <option value="medium">Média</option>
                    <option value="high">Alta</option>
                </select>
            </div>
        </div>

        <div
            class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-[1fr_2fr_auto]"
        >
            <div
                class="flex w-full flex-wrap gap-2 sm:flex-nowrap sm:items-center"
            >
                <input
                    type="date"
                    v-model="props.filters.due_from"
                    @update:modelValue="props.pushFilters"
                    class="min-w-[140px] flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    placeholder="De"
                />
                <input
                    type="date"
                    v-model="props.filters.due_to"
                    @update:modelValue="props.pushFilters"
                    class="min-w-[140px] flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    placeholder="Até"
                />
            </div>

            <div
                class="flex flex-col justify-center gap-2 sm:flex-row sm:items-center"
            >
                <label class="mr-1 block text-sm text-gray-600"
                    >Ordenar por</label
                >
                <div class="flex flex-wrap gap-2">
                    <select
                        v-model="props.filters.sort_by"
                        @update:modelValue="props.pushFilters"
                        class="rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="due_date">Data Limite</option>
                        <option value="priority">Prioridade</option>
                        <option value="title">Título</option>
                        <option value="created_at">Data de Criação</option>
                    </select>
                    <select
                        v-model="props.filters.sort_dir"
                        @update:modelValue="props.pushFilters"
                        class="rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="asc">Ascendente</option>
                        <option value="desc">Descendente</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label class="mr-1 block text-sm text-gray-600">
                    Por página
                </label>
                <select
                    v-model.number="props.filters.per_page"
                    @update:modelValue="props.pushFilters"
                    class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="15">15</option>
                    <option :value="25">25</option>
                </select>
            </div>
        </div>
    </div>
</template>
