<template>
  <div class="p-4">
    <li
      class="rounded-lg bg-white p-6 shadow-md transition hover:shadow-lg"
      :class="{
        'line-through opacity-60': props.task.status === 'completed',
      }"
    >

      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
        <h3 class="font-bold text-lg text-gray-900">
          {{ props.task.title }}
        </h3>
        <span
          class="mt-2 sm:mt-0 inline-block px-3 py-1 rounded-full text-xs font-semibold text-white"
          :class="{
            'bg-red-500': props.task.priority === 'high',
            'bg-yellow-500': props.task.priority === 'medium',
            'bg-green-500': props.task.priority === 'low',
          }"
        >
          {{ props.task.priority }}
        </span>
      </div>

    
      <p class="mt-2 text-gray-600 text-sm">{{ props.task.description }}</p>

     
      <div class="mt-4">
        <label class="text-gray-700 font-medium">Data Limite:</label>
        <p class="text-gray-800 mt-1">{{ props.task.due_date }}</p>
      </div>

    
      <div class="mt-5 flex flex-wrap justify-end gap-3">
        <button
          v-if="props.task.status !== 'completed'"
          class="flex items-center gap-1 text-green-600 hover:text-green-800 transition"
          @click="$emit('complete', props.task.id)"
          title="Marcar como concluída"
        >
          <SquareCheck />
          Concluir
        </button>

        <Link
          :href="routeTasks.edit(props.task.id).url"
          class="flex items-center gap-1 text-yellow-600 hover:text-yellow-800 transition"
          title="Editar tarefa"
        >
          <SquarePen />
          Editar
        </Link>

        <button
          class="flex items-center gap-1 text-red-600 hover:text-red-800 transition"
          @click="$emit('delete', props.task.id)"
          title="Deletar tarefa"
        >
          <Trash2 />
          Excluir
        </button>
      </div>
    </li>
  </div>
</template>

<script setup lang="ts">
import routeTasks from '@/routes/tasks';
import { Task } from '@/types';
import { Link } from '@inertiajs/vue3';
import { SquareCheck, SquarePen, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    task: Task; //array de tasks
}>();
</script>
