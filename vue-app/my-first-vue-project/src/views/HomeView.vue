<script setup>
import TheWelcome from '../components/TheWelcome.vue'
import { useFlash } from '@/composables/useFlash'

let { flash } = useFlash()
</script>

<template>
  <main>
    <TheWelcome />

    <p>
      <button @click="flash('Test', 'It works!')">Click me</button>
    </p>
  </main>
</template>
