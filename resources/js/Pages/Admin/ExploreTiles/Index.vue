<template>
    <AdminLayout>

        <Head title="Manage Image Tiles" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Image Tiles</h2>
                <Link :href="route('admin.explore-tiles.create')"
                    class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                + Add New Image Tile
                </Link>
            </div>

            <DataTable :items="tiles.data" :meta="tiles" :columns="columns" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(tiles.current_page + 1)"
                @prev="goToPage(towns.current_page - 1)">
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
                    <button @click="editTile(item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                    |
                    <button @click="confirmDelete(item.id)"
                        class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteTown">
                <p>Are you sure you want to delete this image tile?</p>
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
    tiles: Object
})

const columns = [
    { key: 'title', label: 'Title' },
    { key: 'subtitle', label: 'Subtitle' },
    { key: 'source', label: 'Source' },
    { key: 'sort_order', label: 'Sort Order' },
    { key: 'is_active', label: 'Status' }
]

const showModal = ref(false)
const deleteId = ref(null)
const toast = useToast()

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteTown() {
    if (deleteId.value) {
        router.delete(route('admin.explore-tiles.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Image Tile deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete image tile')
        })
    }
}

function editTile(id) {
    router.visit(route('admin.explore-tiles.edit', id))
}


function toggleStatus(id) {
    router.put(route('admin.explore-tiles.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated!'),
        onError: () => toast.error('Failed to update status')
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.explore-tiles.index', { page }))
}
</script>