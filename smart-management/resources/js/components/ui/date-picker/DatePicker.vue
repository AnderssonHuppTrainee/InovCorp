<script setup lang="ts">
import { Calendar } from '@/components/ui/calendar'
import { Button } from '@/components/ui/button'
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'
import { cn } from '@/lib/utils'
import { CalendarIcon } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import { DateFormatter, type DateValue, parseDate } from '@internationalized/date'

const props = defineProps<{
    modelValue?: string
    placeholder?: string
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void
}>()

const df = new DateFormatter('pt-PT', {
    dateStyle: 'long',
})

const value = ref<DateValue | undefined>()

// Initialize from modelValue
if (props.modelValue) {
    try {
        value.value = parseDate(props.modelValue)
    } catch (e) {
        console.error('Invalid date format:', props.modelValue)
    }
}

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        try {
            value.value = parseDate(newValue)
        } catch (e) {
            console.error('Invalid date format:', newValue)
        }
    } else {
        value.value = undefined
    }
})

const handleSelect = (date: DateValue | undefined) => {
    value.value = date
    if (date) {
        // Convert to YYYY-MM-DD format
        const year = date.year
        const month = String(date.month).padStart(2, '0')
        const day = String(date.day).padStart(2, '0')
        emit('update:modelValue', `${year}-${month}-${day}`)
    }
}
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="
                    cn(
                        'w-full justify-start text-left font-normal',
                        !value && 'text-muted-foreground',
                    )
                "
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ value ? df.format(value.toDate('UTC')) : (placeholder || 'Selecione uma data') }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar v-model="value" @update:model-value="handleSelect" initial-focus />
        </PopoverContent>
    </Popover>
</template>

