<template>
    <AppLayout>
        <div class="space-y-6 p-4">
            <PageHeader title="Países" description="Gerir países disponíveis no sistema">
                <Button @click="handleCreate">
                    <PlusIcon class="mr-2 h-4 w-4" />
                    Novo País
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex flex-1 gap-2">
                            <div class="relative flex-1 max-w-sm">
                                <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input
                                    type="search"
                                    placeholder="Buscar país..."
                                    class="pl-8"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                />
                            </div>

                            <Select v-model="statusFilter" @update:modelValue="handleFilterChange">
                                <SelectTrigger class="w-[150px]">
                                    <SelectValue placeholder="Estado" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="active">Ativos</SelectItem>
                                    <SelectItem value="inactive">Inativos</SelectItem>
                                </SelectContent>
                            </Select>

                            <Button variant="ghost" @click="clearFilters" v-if="hasFilters">
                                <XIcon class="mr-2 h-4 w-4" />
                                Limpar
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <DataTable :columns="columns" :data="countriesData.data" />

                    <div class="flex items-center justify-between px-2 py-4" v-if="countriesData.data.length > 0">
                        <div class="text-sm text-muted-foreground">
                            Mostrando <strong>{{ countriesData.from }}</strong> a <strong>{{ countriesData.to }}</strong> de <strong>{{ countriesData.total }}</strong> resultados
                        </div>
                        <div class="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="handlePreviousPage"
                                :disabled="!countriesData.prev_page_url"
                            >
                                Anterior
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="handleNextPage"
                                :disabled="!countriesData.next_page_url"
                            >
                                Próxima
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import DataTable from '@/components/ui/data-table/DataTable.vue'
import PageHeader from '@/components/PageHeader.vue'
import { PlusIcon, SearchIcon, XIcon } from 'lucide-vue-next'
import { columns } from './columns'
import countries from '@/routes/countries'

interface Props {
    countriesData: {
        data: Array<any>
        current_page: number
        total: number
        from: number
        to: number
        prev_page_url: string | null
        next_page_url: string | null
    }
    filters: {
        search?: string
        status?: string
    }
}

const props = defineProps<Props>()

const searchQuery = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || 'all')

const hasFilters = computed(() => {
    return searchQuery.value !== '' || statusFilter.value !== 'all'
})

const handleSearch = () => {
    router.get(
        countries.index().url,
        {
            search: searchQuery.value,
            status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
        },
        {
            preserveState: true,
            replace: true,
        },
    )
}

const handleFilterChange = () => {
    handleSearch()
}

const clearFilters = () => {
    searchQuery.value = ''
    statusFilter.value = 'all'
    router.get(countries.index().url, {}, { preserveState: true, replace: true })
}

const handleCreate = () => {
    router.visit(countries.create().url)
}

const handlePreviousPage = () => {
    if (props.countriesData.prev_page_url) {
        router.visit(props.countriesData.prev_page_url)
    }
}

const handleNextPage = () => {
    if (props.countriesData.next_page_url) {
        router.visit(props.countriesData.next_page_url)
    }
}
</script>

