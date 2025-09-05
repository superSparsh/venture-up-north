<template>
    <AdminLayout>

        <Head title="Manage Towns" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Towns</h2>
                <Link :href="route('admin.towns.create')"
                    class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                + Add Town
                </Link>
            </div>

            <DataTable :items="towns.data" :meta="towns" :columns="columns" :pagination="true" :searchable="true"
                :downloadable="true" @next="goToPage(towns.current_page + 1)" @prev="goToPage(towns.current_page - 1)"
                @search="doSearch">
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
                    <button @click="previewTown(item.slug)"
                        class="text-indigo-600 hover:underline text-sm font-semibold">
                        Preview
                    </button>
                    |
                    <button @click="editTown(item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                    |
                    <button @click="confirmDelete(item.id)"
                        class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteTown">
                <p>Are you sure you want to delete this town?</p>
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
    towns: Object,
    filters: Object,
})

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'slug', label: 'Slug' },
    { key: 'is_active', label: 'Status' }
]

const searchQuery = ref(props.filters?.search || '')

const showModal = ref(false)
const deleteId = ref(null)
const toast = useToast()

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteTown() {
    if (deleteId.value) {
        router.delete(route('admin.towns.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Town deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete town')
        })
    }
}

function editTown(id) {
    router.visit(route('admin.towns.edit', id))
}

function previewTown(slug) {
    router.visit(route('show.town', slug))
}

function toggleStatus(id) {
    router.put(route('admin.towns.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated!'),
        onError: () => toast.error('Failed to update status')
    })
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('admin.towns.index'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.towns.index', { page }))
}
</script>