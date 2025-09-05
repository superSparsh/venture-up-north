<template>
    <AdminLayout>

        <Head title="Ventures" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Ventures</h2>

                <!-- Filters / Quick actions row -->
                <!-- <form class="flex gap-3 items-center" @submit.prevent="doSearch(searchQuery)">
                    <input v-model="searchQuery" type="text" placeholder="Search title, slug, user…"
                        class="rounded-lg bg-white/10 border border-white/20 px-3 py-2 text-white min-w-[260px]" />
                    <select v-model="status" class="rounded-lg bg-white/10 border border-white/20 px-3 py-2 text-white">
                        <option value="">Status</option>
                        <option value="draft">draft</option>
                        <option value="submitted">submitted</option>
                        <option value="approved">approved</option>
                        <option value="published">published</option>
                        <option value="archived">archived</option>
                    </select>
                    <select v-model="visibility"
                        class="rounded-lg bg-white/10 border border-white/20 px-3 py-2 text-white">
                        <option value="">Visibility</option>
                        <option value="public">public</option>
                        <option value="unlisted">unlisted</option>
                        <option value="private">private</option>
                    </select>
                    <button class="bg-bison text-heavy px-4 py-2 rounded hover:bg-white shadow">
                        Apply
                    </button>
                </form> -->
                <div class="flex justify-end mb-4 gap-3">
                    <Link :href="route('admin.ventures.ours.index')" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                    Our Ventures
                    </Link>
                    <a :href="route('admin.collections.create')" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow transition">
                        + Add Venture
                    </a>
                </div>

            </div>

            <DataTable :items="ventures.data" :columns="columns" :meta="ventures" :pagination="true" :searchable="true"
                :downloadable="true" @next="goToPage(ventures.current_page + 1)"
                @prev="goToPage(ventures.current_page - 1)" @search="doSearch">
                <!-- Owner (user / guest) -->
                <template #item-owner="{ item }">
                    <div class="flex flex-col">
                        <span v-if="item.owner_user_id" class="font-semibold">User #{{ item.owner_user_id }}</span>
                        <span v-else class="font-semibold">{{ item.owner_guest_name || 'Guest' }}</span>
                        <span v-if="item.created_by_admin_id" class="text-xs text-white/60">Admin-authored</span>
                    </div>
                </template>

                <!-- Featured toggle -->
                <template #item-is_featured="{ item }">
                    <button @click="toggleFeatured(item.id)"
                        class="px-2 py-1 rounded text-xs font-semibold inline-block"
                        :class="item.is_featured ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-500 text-white'">
                        {{ item.is_featured ? '🔥 Featured' : 'Standard' }}
                    </button>
                </template>

                <!-- Visibility badge -->
                <template #item-visibility="{ item }">
                    <span class="px-2 py-1 rounded text-xs font-semibold inline-block capitalize"
                        :class="badgeClass(item.visibility)">
                        {{ item.visibility }}
                    </span>
                </template>

                <!-- Status badge -->
                <template #item-status="{ item }">
                    <span class="px-2 py-1 rounded text-xs font-semibold inline-block capitalize"
                        :class="statusClass(item.status)">
                        {{ item.status }}
                    </span>
                </template>

                <!-- Items count -->
                <template #item-items_count="{ item }">
                    <span
                        class="px-2 py-1 rounded text-xs font-semibold inline-block bg-white/10 border border-white/10">
                        {{ item.items_count }}
                    </span>
                </template>

                <!-- Actions -->
                <template #actions="{ item }">
                    <Link :href="route('ventures.show', item.slug)"
                        class="text-indigo-600 hover:underline text-sm font-semibold" target="_blank">
                    Preview
                    </Link>
                    |
                    <Link :href="route('admin.ventures.edit', item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">
                    Edit
                    </Link>
                    <template v-if="allowDelete">
                        |
                        <button @click="confirmDelete(item.id)"
                            class="text-red-600 hover:underline text-sm font-semibold">
                            Delete
                        </button>
                    </template>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteVenture">
                <p>Are you sure you want to delete this venture?</p>
            </Modal>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import DataTable from '@/Components/DataTable.vue'
import Modal from '@/Components/Modal.vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
    ventures: Object,
    filters: Object, // { search, status, visibility }
})

const toast = useToast()
const showModal = ref(false)
const deleteId = ref(null)
const allowDelete = true // set false if you don't want delete

// Table columns (keys must match your item fields or slot names)
const columns = [
    { key: 'title', label: 'Title' },
    { key: 'owner', label: 'User' },          // custom slot
    { key: 'is_featured', label: 'Featured' },      // custom slot toggle
    { key: 'visibility', label: 'Visibility' },    // custom slot badge
    { key: 'status', label: 'Status' },        // custom slot badge
    { key: 'items_count', label: 'Items' },         // custom slot
]

// local filter state synced with props
const searchQuery = ref(props.filters?.q || props.filters?.search || '')
const status = ref(props.filters?.status || '')
const visibility = ref(props.filters?.visibility || '')

// Keep URL in sync when dropdown filters change
watch([status, visibility], () => {
    router.get(
        route('admin.ventures.index'),
        { q: searchQuery.value || '', status: status.value || '', visibility: visibility.value || '' },
        { preserveScroll: true, preserveState: true, replace: true }
    )
})

// Helpers
function badgeClass(vis) {
    switch (vis) {
        case 'public': return 'bg-emerald-100 text-emerald-700'
        case 'private': return 'bg-rose-100 text-rose-700'
        default: return 'bg-white/10 text-white'
    }
}
function statusClass(st) {
    switch (st) {
        case 'published': return 'bg-emerald-100 text-emerald-700'
        case 'approved': return 'bg-sky-100 text-sky-700'
        case 'submitted': return 'bg-amber-100 text-amber-700'
        case 'draft': return 'bg-white/10 text-white'
        case 'archived': return 'bg-zinc-200 text-zinc-700'
        default: return 'bg-white/10 text-white'
    }
}

function goToPage(page) {
    if (!page) return
    router.visit(route('admin.ventures.index', {
        page,
        q: searchQuery.value || '',
        status: status.value || '',
        visibility: visibility.value || '',
    }))
}

function doSearch(query) {
    // DataTable emits plain string for @search, or our form uses model
    const q = typeof query === 'string' ? query : (searchQuery.value || '')
    searchQuery.value = q
    router.get(
        route('admin.ventures.index'),
        { q, status: status.value || '', visibility: visibility.value || '' },
        { preserveScroll: true, preserveState: true, replace: true }
    )
}

function toggleFeatured(id) {
    router.put(route('admin.ventures.toggle-featured', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Venture featured status updated.'),
        onError: () => toast.error('Failed to update featured status.'),
    })
}

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteVenture() {
    if (!deleteId.value) return
    router.delete(route('admin.ventures.destroy', deleteId.value), {
        onSuccess: () => {
            toast.success('Venture deleted successfully.')
            showModal.value = false
            deleteId.value = null
        },
        onError: () => toast.error('Failed to delete venture.'),
    })
}
</script>