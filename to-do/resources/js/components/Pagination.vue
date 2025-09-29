
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import routerTasks from '@/routes/tasks';
import { computed } from 'vue';

interface PaginationProps {
    paginator: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    filters?: Record<string, any>;
}

const props = defineProps<PaginationProps>();

const emit = defineEmits<{
    pageChanged: [page: number];
}>();

const goToPage = (page: number) => {
    router.get(routerTasks.index().url, { 
        page,
        ...props.filters
    }, {
        preserveState: true,
        preserveScroll: true
    });
    emit('pageChanged', page);
};


const pageRange = computed(() => {
    const current = props.paginator.current_page;
    const last = props.paginator.last_page;
    const delta = 2;
    
    const range = [];
    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        range.push(i);
    }
    
    if (current - delta > 2) range.unshift('...');
    if (current + delta < last - 1) range.push('...');
    
    range.unshift(1);
    if (last !== 1) range.push(last);
    
    return range;
});
</script>

<template>
    <div class="flex items-center justify-between mt-6">
        <div class="text-sm text-gray-600">
            Mostrando {{ paginator.from }} a {{ paginator.to }} de {{ paginator.total }} resultados
        </div>
        
        <div class="flex space-x-1">
            <button
                @click="goToPage(paginator.current_page - 1)"
                :disabled="paginator.current_page === 1"
                class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50"
            >
                ‹
            </button>
            
            <button
                v-for="page in pageRange"
                :key="page"
                @click="page !== '...' && goToPage(page as number)"
                :class="[
                    'px-3 py-1 border rounded',
                    page === paginator.current_page 
                        ? 'bg-blue-500 text-white border-blue-500' 
                        : 'hover:bg-gray-100',
                    page === '...' && 'cursor-default'
                ]"
                :disabled="page === '...'"
            >
                {{ page }}
            </button>
            
            <button
                @click="goToPage(paginator.current_page + 1)"
                :disabled="paginator.current_page === paginator.last_page"
                class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50"
            >
                ›
            </button>
        </div>
    </div>
</template>