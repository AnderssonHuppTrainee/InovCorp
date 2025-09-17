<template>
  <div class="form-control relative">
    <div class="relative">
      <input
        v-model="query"
        type="text"
        :placeholder="placeholder"
        class="input input-bordered w-full pl-10"
        @input="handleSearch"
        @focus="showResults = true"
        @blur="hideResults"
      />
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-base-content/50"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
        />
      </svg>
    </div>

    <!-- Resultados da Busca -->
    <div
      v-if="showResults && results.length > 0"
      class="absolute top-full z-50 mt-1 w-full rounded-lg border border-base-300 bg-base-100 shadow-lg"
    >
      <div class="max-h-60 overflow-y-auto">
        <div
          v-for="result in results"
          :key="result.id"
          @click="selectResult(result)"
          class="flex items-center gap-3 p-3 hover:bg-base-200 cursor-pointer"
        >
          <div class="avatar">
            <div class="w-8 rounded-full bg-primary text-primary-content flex items-center justify-center">
              <span class="text-xs font-bold">
                {{ result.type === 'room' ? '🏠' : '👤' }}
              </span>
            </div>
          </div>
          <div class="flex-1">
            <p class="font-medium">{{ result.name }}</p>
            <p class="text-xs text-base-content/70">
              {{ result.type === 'room' ? 'Sala' : 'Usuário' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Estado vazio -->
    <div
      v-if="showResults && query && results.length === 0"
      class="absolute top-full z-50 mt-1 w-full rounded-lg border border-base-300 bg-base-100 shadow-lg p-4 text-center"
    >
      <p class="text-base-content/50">Nenhum resultado encontrado</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

interface SearchResult {
  id: number;
  name: string;
  type: 'room' | 'user';
}

const props = defineProps<{
  placeholder?: string;
  rooms?: any[];
  users?: any[];
}>();

const emit = defineEmits<{
  select: [result: SearchResult];
  search: [query: string];
}>();

const query = ref('');
const results = ref<SearchResult[]>([]);
const showResults = ref(false);

function handleSearch() {
  if (!query.value.trim()) {
    results.value = [];
    return;
  }

  const searchTerm = query.value.toLowerCase();
  const searchResults: SearchResult[] = [];

  // Buscar salas
  if (props.rooms) {
    props.rooms
      .filter(room => room.name.toLowerCase().includes(searchTerm))
      .forEach(room => {
        searchResults.push({
          id: room.id,
          name: room.name,
          type: 'room'
        });
      });
  }

  // Buscar usuários
  if (props.users) {
    props.users
      .filter(user => user.name.toLowerCase().includes(searchTerm))
      .forEach(user => {
        searchResults.push({
          id: user.id,
          name: user.name,
          type: 'user'
        });
      });
  }

  results.value = searchResults;
  emit('search', query.value);
}

function selectResult(result: SearchResult) {
  emit('select', result);
  query.value = '';
  showResults.value = false;
}

function hideResults() {
  // Delay para permitir clique nos resultados
  setTimeout(() => {
    showResults.value = false;
  }, 200);
}

watch(query, () => {
  if (!query.value) {
    results.value = [];
  }
});
</script>

