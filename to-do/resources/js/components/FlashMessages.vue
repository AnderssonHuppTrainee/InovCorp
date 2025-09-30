<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const flash = computed(() => (page.props.flash || {}) as Record<string, string | undefined>);
</script>

<template>
    <div class="sr-only" aria-live="polite" role="status">
        <span v-if="flash.success">{{ flash.success }}</span>
        <span v-if="flash.info">{{ flash.info }}</span>
    </div>
    <div class="sr-only" aria-live="assertive" role="alert">
        <span v-if="flash.error">{{ flash.error }}</span>
        <span v-if="flash.warning">{{ flash.warning }}</span>
    </div>
</template>


