<script setup>
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

import { Input } from '@/components/ui/input';

// Removido form prop - agora usa FormField do vee-validate
</script>

<template>
    <div class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <FormField v-slot="{ componentField }" name="phone">
                <FormItem>
                    <FormLabel>Telefone</FormLabel>
                    <FormControl>
                        <Input
                            placeholder="+351 123 456 789"
                            v-bind="componentField"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="mobile">
                <FormItem>
                    <FormLabel>Telemóvel</FormLabel>
                    <FormControl>
                        <Input
                            placeholder="+351 912 345 678"
                            v-bind="componentField"
                        />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
        </div>

        <FormField v-slot="{ componentField }" name="email">
            <FormItem>
                <FormLabel>Email</FormLabel>
                <FormControl>
                    <Input
                        type="email"
                        placeholder="empresa@exemplo.pt"
                        v-bind="componentField"
                    />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="website">
            <FormItem>
                <FormLabel>Website</FormLabel>
                <FormControl>
                    <Input
                        placeholder="https://exemplo.pt"
                        v-bind="componentField"
                    />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>
    </div>
</template>
