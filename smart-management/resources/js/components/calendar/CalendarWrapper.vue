<script setup lang="ts">
import FullCalendar from '@fullcalendar/vue3'
import type { CalendarOptions } from '@fullcalendar/core'
import ptLocale from '@fullcalendar/core/locales/pt'

interface Props {
    options: CalendarOptions
}

const props = defineProps<Props>()

// Merge locale
const mergedOptions = {
    ...props.options,
    locale: ptLocale,
}
</script>

<template>
    <FullCalendar :options="mergedOptions" />
</template>

<style>
/* FullCalendar Core Styles - Inline para evitar problemas de build */
.fc {
    font-family: inherit;
}

/* Toolbar */
.fc .fc-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.fc .fc-toolbar-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: hsl(var(--foreground));
}

/* Buttons */
.fc .fc-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: calc(var(--radius) - 2px);
    font-size: 0.875rem;
    font-weight: 500;
    height: 2.5rem;
    padding: 0 1rem;
    background-color: hsl(var(--primary));
    color: hsl(var(--primary-foreground));
    border: 1px solid hsl(var(--primary));
    cursor: pointer;
    transition: all 0.2s;
    text-transform: capitalize;
}

.fc .fc-button:hover {
    background-color: hsl(var(--primary) / 0.9);
    opacity: 0.9;
}

.fc .fc-button:active,
.fc .fc-button-active {
    background-color: hsl(var(--primary) / 0.8);
}

.fc .fc-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.fc .fc-button-group {
    display: inline-flex;
    gap: 0.25rem;
}

/* Calendar Grid */
.fc-theme-standard .fc-scrollgrid {
    border: 1px solid hsl(var(--border));
    border-radius: var(--radius);
}

.fc-theme-standard td,
.fc-theme-standard th {
    border-color: hsl(var(--border));
}

.fc-col-header-cell {
    background-color: hsl(var(--muted));
    font-weight: 600;
    padding: 0.75rem;
}

.fc-daygrid-day {
    background-color: hsl(var(--background));
}

.fc-daygrid-day:hover {
    background-color: hsl(var(--accent));
}

.fc-day-today {
    background-color: hsl(var(--accent)) !important;
}

.fc-day-other {
    background-color: hsl(var(--muted) / 0.3);
}

/* Events */
.fc-event {
    cursor: pointer;
    border-radius: calc(var(--radius) - 2px);
    padding: 0.125rem 0.25rem;
    margin-bottom: 2px;
    border: 1px solid;
}

.fc-event:hover {
    opacity: 0.9;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
}

.fc-event-title {
    font-weight: 500;
}

/* Time Grid */
.fc-timegrid-slot {
    height: 3rem;
}

.fc-timegrid-slot-label {
    border-color: hsl(var(--border));
}

/* List View */
.fc-list-event:hover td {
    background-color: hsl(var(--accent));
}

.fc-list-event-title {
    color: hsl(var(--foreground));
}

/* Scrollbars */
.fc-scroller::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.fc-scroller::-webkit-scrollbar-track {
    background: hsl(var(--muted));
}

.fc-scroller::-webkit-scrollbar-thumb {
    background: hsl(var(--muted-foreground) / 0.3);
    border-radius: 4px;
}

.fc-scroller::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--muted-foreground) / 0.5);
}
</style>

