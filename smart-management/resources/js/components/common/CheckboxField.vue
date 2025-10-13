<script setup lang="ts">
import { FormControl, FormDescription, FormField, FormItem, FormLabel } from '@/components/ui/form'
import { computed } from 'vue'

interface Props {
    name: string
    label: string
    description?: string
    disabled?: boolean
}

const props = defineProps<Props>()

// Generate unique ID for accessibility
const fieldId = computed(() => `checkbox-${props.name}`)
</script>

<template>
    <FormField v-slot="{ value, handleChange }" :name="name">
        <FormItem class="flex flex-row items-start space-y-0 space-x-3 rounded-lg border p-4">
            <FormControl>
                <input
                    :id="fieldId"
                    type="checkbox"
                    :name="name"
                    :checked="Boolean(value)"
                    :disabled="disabled"
                    @change="(event: Event) => handleChange((event.target as HTMLInputElement).checked)"
                    class="peer mt-1 h-4 w-4 shrink-0 cursor-pointer rounded-sm border border-primary ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                />
            </FormControl>
            <div class="grid gap-1.5 leading-none">
                <FormLabel
                    :for="fieldId"
                    class="cursor-pointer text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                >
                    {{ label }}
                </FormLabel>
                <FormDescription v-if="description" class="text-sm text-muted-foreground">
                    {{ description }}
                </FormDescription>
            </div>
        </FormItem>
    </FormField>
</template>

