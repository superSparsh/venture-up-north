<template>
    <AdminLayout>

        <Head title="Manage Tour Tiles" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Tour Tiles</h2>
                <!-- Buttons on the right -->
                <div class="flex gap-2">
                    <Link :href="route('admin.tour.settings')"
                        class="bg-heavy text-white px-4 py-2 rounded hover:bg-bison shadow">
                    Tour Page Settings
                    </Link>

                    <Link :href="route('admin.tour-tiles.create')"
                        class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                    + Add Tour Tile
                    </Link>
                </div>
            </div>

            <DataTable :items="tourTiles.data" :meta="tourTiles" :columns="columns" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(tourTiles.current_page + 1)"
                @prev="goToPage(tourTiles.current_page - 1)" @search="doSearch">
                <template #item-photo="{ item }">
                    <img :src="item.photo ? `/public/storage/${item.photo}` : ''" alt=""
                        class="w-10 h-10 object-cover rounded-full" />
                </template>

                <template #item-is_active="{ item }">
                    <button @click="toggleStatus(item.id)" :class="[
                        'px-2 py-1 rounded text-xs font-semibold inline-block transition',
                        item.is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200'
                    ]">
                        {{ item.is_active ? 'Active' : 'Inactive' }}
                    </button>
                </template>

                <template #actions="{ item }">
                    <a :href="route('booking.tour', item.slug)"
                        class="text-indigo-600 hover:underline text-sm font-semibold" type="button">
                        Preview
                    </a>
                    |
                    <button @click="editTourTile(item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                    |
                    <button @click="confirmTourTile(item.id)"
                        class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteTourTile">
                <p>Are you sure you want to delete this tour tile?</p>
            </Modal>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import Modal from '@/Components/Modal.vue'
import { useToast } from 'vue-toastification'
import DataTable from '@/Components/DataTable.vue'


const props = defineProps({
    tourTiles: Object,
    filters: Object,
})

const columns = [
    { key: 'title', label: 'title' },
    { key: 'slug', label: 'Slug' },
    { key: 'is_active', label: 'Status' }
]

const searchQuery = ref(props.filters?.search || '')

const showModal = ref(false)
const deleteId = ref(null)
const toast = useToast()

function confirmTourTile(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteTourTile() {
    if (deleteId.value) {
        router.delete(route('admin.tour-tiles.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Tour Tile deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete Tour Tile')
        })
    }
}

function editTourTile(id) {
    router.visit(route('admin.tour-tiles.edit', id))
}

function toggleStatus(id) {
    router.put(route('admin.tour-tiles.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated!'),
        onError: () => toast.error('Failed to update status')
    })
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('admin.tour-tiles.index'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.tour-tiles.index', { page }))
}
</script>