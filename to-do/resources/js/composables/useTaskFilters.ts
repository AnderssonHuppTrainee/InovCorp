import { reactive, watch, toRaw } from 'vue';
import { router } from '@inertiajs/vue3';
import routeTasks from '@/routes/tasks';
import type { Filters } from '@/types';

function normalizeDate(date: unknown): string {
  if (!date) return '';
  if (typeof date === 'string') return date;
  if (date instanceof Date) return date.toISOString().split('T')[0];
  return '';
}

export function useTaskFilters(initialFilters: Filters) {
  const filters = reactive<Filters>({
    search: initialFilters.search || '',
    status: initialFilters.status || '',
    priority: initialFilters.priority || '',
    due_from: normalizeDate(initialFilters.due_from) || '',
    due_to: normalizeDate(initialFilters.due_to) || '',
    sort_by: initialFilters.sort_by || 'due_date',
    sort_dir: initialFilters.sort_dir || 'asc',
    per_page: initialFilters.per_page || 10,
  });

  // debounce para o search
  let searchTimeout: number;
  watch(
    () => filters.search,
    (newSearch) => {
      clearTimeout(searchTimeout);
      searchTimeout = window.setTimeout(() => {
        pushFilters({ search: newSearch || undefined });
      }, 400);
    }
  );

  // watch nos outros filtros para garantir refresh
  watch(
    [
      () => filters.status,
      () => filters.priority,
      () => filters.due_from,
      () => filters.due_to,
      () => filters.sort_by,
      () => filters.sort_dir,
      () => filters.per_page,
    ],
    () => {
      pushFilters();
    }
  );

  function pushFilters(extra: Record<string, unknown> = {}) {
    router.get(
      routeTasks.index().url,
      { ...toRaw(filters), page: 1, ...extra },
      {
        preserveState: true,
        preserveScroll: false,
        replace: true,
      }
    );
  }

  return { filters, pushFilters };
}

export type ReturnTypeUseTaskFilters = ReturnType<typeof useTaskFilters>;
