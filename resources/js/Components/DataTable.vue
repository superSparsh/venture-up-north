<template>
    <div class="overflow-x-auto rounded shadow bg-white">
        <!-- Search Bar -->
        <div v-if="searchable" class="p-4">
            <input v-model="search" type="text" placeholder="Search..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
        </div>

        <!-- Table -->
        <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th v-for="col in visibleColumns" :key="col.key" class="px-6 py-3 cursor-pointer select-none"
                        @click="toggleSort(col.key)">
                        <div class="flex items-center justify-between">
                            <span>{{ col.label }}</span>
                            <span v-if="sortBy === col.key">
                                <svg v-if="sortDirection === 'asc'" class="w-3 h-3 ml-2 inline-block" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                </svg>
                                <svg v-else class="w-3 h-3 ml-2 inline-block" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </th>
                    <th v-if="$slots.actions" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <tr v-for="item in sortedItems" :key="item.id" class="hover:bg-gray-50">
                    <td v-for="col in visibleColumns" :key="col.key" class="px-6 py-4">
                        <slot :name="`item-${col.key}`" :item="item">
                            {{ getNestedValue(item, col.key) }}
                        </slot>
                    </td>
                    <td v-if="$slots.actions" class="px-6 py-4 text-right">
                        <slot name="actions" :item="item" />
                    </td>
                </tr>
                <tr v-if="sortedItems.length === 0">
                    <td :colspan="columns.length + 1" class="px-6 py-6 text-center text-gray-400">
                        No results found.
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Footer Controls -->
        <div v-if="pagination || downloadable"
            class="flex justify-between items-center p-4 bg-gray-50 border-t text-sm">
            <!-- Safe pagination info block -->
            <div v-if="pagination && meta">
                Showing
                <strong>{{ meta.from }}</strong>
                to
                <strong>{{ meta.to }}</strong>
                of
                <strong>{{ meta.total }}</strong>
                results
            </div>

            <!-- Buttons -->
            <div class="flex space-x-2 items-center">
                <button v-if="pagination && meta" @click="$emit('prev')" :disabled="!meta.prev_page_url"
                    class="px-3 py-1 rounded border text-sm">
                    Prev
                </button>
                <button v-if="pagination && meta" @click="$emit('next')" :disabled="!meta.next_page_url"
                    class="px-3 py-1 rounded border text-sm">
                    Next
                </button>
                <button v-if="downloadable" @click="downloadCSV"
                    class="ml-3 px-3 py-1 rounded border bg-blue-50 text-blue-600 hover:bg-blue-100 text-sm">
                    Export CSV
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
    items: { type: Array, default: () => [] },
    columns: Array,
    meta: { type: Object, default: () => null },
    pagination: Boolean,
    searchable: Boolean,
    downloadable: Boolean,
})

const emit = defineEmits(['prev', 'next', 'search'])

const search = ref('')
const sortBy = ref(null)
const sortDirection = ref('asc')

watch(search, (val) => {
    emit('search', val)
})
const visibleColumns = computed(() => props.columns || [])

const filteredItems = computed(() => {
    if (!Array.isArray(props.items)) return []
    if (!props.searchable || !search.value) return props.items
    const keyword = search.value.toLowerCase()
    return props.items.filter((item) =>
        visibleColumns.value.some((col) =>
            String(item[col.key] || '')
                .toLowerCase()
                .includes(keyword)
        )
    )
})

const sortedItems = computed(() => {
    const items = [...filteredItems.value]
    if (!sortBy.value) return items
    return items.sort((a, b) => {
        const valA = a[sortBy.value]?.toString().toLowerCase()
        const valB = b[sortBy.value]?.toString().toLowerCase()
        if (valA === valB) return 0
        const comparison = valA > valB ? 1 : -1
        return sortDirection.value === 'asc' ? comparison : -comparison
    })
})

function toggleSort(key) {
    if (sortBy.value === key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = key
        sortDirection.value = 'asc'
    }
}
console.log('📦 meta:', props.meta.from)

function downloadCSV() {
    const rows = [visibleColumns.value.map((col) => col.label)]
    props.items.forEach((item) => {
        rows.push(visibleColumns.value.map((col) => item[col.key]))
    })

    const csv = rows.map((r) => r.join(',')).join('\n')
    const blob = new Blob([csv], { type: 'text/csv' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'export.csv'
    a.click()
    URL.revokeObjectURL(url)
}

function getNestedValue(obj, path) {
    return path.split('.').reduce((acc, key) => acc?.[key], obj) || '-'
}
</script>

<style scoped>
table {
    font-variant-numeric: tabular-nums;
}
</style>