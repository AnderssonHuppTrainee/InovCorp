<script setup lang="ts">
import { useFormField } from '@/components/ui/form/useFormField'
import { computed } from 'vue'

interface Props {
    name: string
    label: string
    description?: string
    disabled?: boolean
}

const props = defineProps<Props>()

// Get form context for the field
const { value, handleChange } = useFormField(() => props.name)

// Ensure value is always boolean
const isChecked = computed(() => Boolean(value.value))

const handleInputChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    handleChange(target.checked)
}

// Generate unique ID for accessibility
const fieldId = computed(() => `checkbox-${props.name}`)
</script>

<template>
    <div class="flex flex-row items-start space-y-0 space-x-3 rounded-lg border p-4">
        <input
            :id="fieldId"
            type="checkbox"
            :name="name"
            :checked="isChecked"
            :disabled="disabled"
            @change="handleInputChange"
            class="peer h-4 w-4 shrink-0 cursor-pointer rounded-sm border border-primary ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
        />
        <div class="grid gap-1.5 leading-none">
            <label
                :for="fieldId"
                class="cursor-pointer text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
            >
                {{ label }}
            </label>
            <p v-if="description" class="text-sm text-muted-foreground">
                {{ description }}
            </p>
        </div>
    </div>
</template>

