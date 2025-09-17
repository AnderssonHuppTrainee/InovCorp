<template>
  <div class="relative">
    <textarea
      ref="textareaRef"
      v-model="inputValue"
      :placeholder="placeholder"
      class="textarea textarea-bordered w-full resize-none"
      @input="handleInput"
      @keydown="handleKeydown"
      @keyup="handleKeyup"
    ></textarea>

    <!-- Sugestões de @mentions -->
    <div
      v-if="showSuggestions && suggestions.length > 0"
      class="absolute bottom-full left-0 z-50 mb-2 w-full rounded-lg border border-base-300 bg-base-100 shadow-lg"
    >
      <div class="max-h-40 overflow-y-auto">
        <div
          v-for="(user, index) in suggestions"
          :key="user.id"
          @click="selectUser(user)"
          class="flex items-center gap-3 p-3 cursor-pointer"
          :class="{
            'bg-primary text-primary-content': index === selectedIndex,
            'hover:bg-base-200': index !== selectedIndex
          }"
        >
          <div class="avatar">
            <div class="w-8 rounded-full bg-primary text-primary-content flex items-center justify-center">
              <span class="text-xs font-bold">
                {{ user.name.charAt(0).toUpperCase() }}
              </span>
            </div>
          </div>
          <div>
            <p class="font-medium">{{ user.name }}</p>
            <p class="text-xs opacity-70">{{ user.email }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';

interface User {
  id: number;
  name: string;
  email: string;
}

const props = defineProps<{
  modelValue: string;
  placeholder?: string;
  users?: User[];
}>();

const emit = defineEmits<{
  'update:modelValue': [value: string];
  'mention': [user: User];
}>();

const textareaRef = ref<HTMLTextAreaElement>();
const inputValue = ref(props.modelValue);
const showSuggestions = ref(false);
const selectedIndex = ref(0);
const mentionStart = ref(-1);

const suggestions = computed(() => {
  if (!props.users || !showSuggestions.value) return [];
  
  const query = inputValue.value.substring(mentionStart.value + 1).toLowerCase();
  return props.users.filter(user => 
    user.name.toLowerCase().includes(query) || 
    user.email.toLowerCase().includes(query)
  );
});

function handleInput() {
  emit('update:modelValue', inputValue.value);
  
  const cursorPos = textareaRef.value?.selectionStart || 0;
  const textBeforeCursor = inputValue.value.substring(0, cursorPos);
  const lastAtIndex = textBeforeCursor.lastIndexOf('@');
  
  if (lastAtIndex !== -1) {
    const textAfterAt = textBeforeCursor.substring(lastAtIndex + 1);
    const hasSpace = textAfterAt.includes(' ');
    
    if (!hasSpace) {
      mentionStart.value = lastAtIndex;
      showSuggestions.value = true;
      selectedIndex.value = 0;
      return;
    }
  }
  
  showSuggestions.value = false;
}

function handleKeydown(event: KeyboardEvent) {
  if (!showSuggestions.value) return;

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault();
      selectedIndex.value = Math.min(selectedIndex.value + 1, suggestions.value.length - 1);
      break;
    case 'ArrowUp':
      event.preventDefault();
      selectedIndex.value = Math.max(selectedIndex.value - 1, 0);
      break;
    case 'Enter':
      event.preventDefault();
      if (suggestions.value[selectedIndex.value]) {
        selectUser(suggestions.value[selectedIndex.value]);
      }
      break;
    case 'Escape':
      showSuggestions.value = false;
      break;
  }
}

function handleKeyup(event: KeyboardEvent) {
  if (event.key === '@' && !showSuggestions.value) {
    nextTick(() => {
      const cursorPos = textareaRef.value?.selectionStart || 0;
      mentionStart.value = cursorPos - 1;
      showSuggestions.value = true;
      selectedIndex.value = 0;
    });
  }
}

function selectUser(user: User) {
  const beforeMention = inputValue.value.substring(0, mentionStart.value);
  const afterMention = inputValue.value.substring(textareaRef.value?.selectionStart || 0);
  
  inputValue.value = `${beforeMention}@${user.name} ${afterMention}`;
  emit('update:modelValue', inputValue.value);
  emit('mention', user);
  
  showSuggestions.value = false;
  
  nextTick(() => {
    const newCursorPos = beforeMention.length + user.name.length + 2;
    textareaRef.value?.setSelectionRange(newCursorPos, newCursorPos);
    textareaRef.value?.focus();
  });
}
</script>

