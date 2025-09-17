<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import logo from '@/images/logo_trans.png';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img :src="logo" alt="logo" class="w-32 h-auto" />
</template>
