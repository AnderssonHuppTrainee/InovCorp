<script setup lang="ts">
import logo from '@/images/logo.png';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img :src="logo" alt="logo" class="h-10 w-auto rounded" />
</template>
