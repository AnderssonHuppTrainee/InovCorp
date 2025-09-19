<script setup lang="ts">
import { PanelLeft } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';
import { useSidebar } from './utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();

const { toggleSidebar } = useSidebar();
</script>

<template>
    <div>
        <button class="btn relative btn-circle btn-ghost" @click="toggleSidebar">
            <PanelLeft />
            <span class="sr-only">Toggle Sidebar</span>
        </button>
    </div>
</template>
