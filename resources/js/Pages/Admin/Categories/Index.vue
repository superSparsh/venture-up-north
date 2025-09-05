<template>
    <AdminLayout>

        <Head title="Manage Contents" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Contents</h2>
                <Link :href="route('admin.categories.create')"
                    class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                + Add Content
                </Link>
            </div>

            <DataTable :items="categories.data" :meta="categories" :columns="columns" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(categories.current_page + 1)"
                @prev="goToPage(categories.current_page - 1)" @search="doSearch">
                <template #item-icon="{ item }">
                    <img :src="item.icon ? `/public/storage/${item.icon}` : ''" alt=""
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
                    <button @click="previewCategory(item.slug)"
                        class="text-indigo-600 hover:underline text-sm font-semibold">
                        Preview
                    </button>
                    |
                    <button @click="editCategory(item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                    |
                    <button @click="confirmDelete(item.id)"
                        class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteCategory">
                <p>Are you sure you want to delete this Content?</p>
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
    categories: Object,
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

function deleteCategory() {
    if (deleteId.value) {
        router.delete(route('admin.categories.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Content deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete Content')
        })
    }
}

function editCategory(id) {
    router.visit(route('admin.categories.edit', id))
}

function previewCategory(slug) {
    router.visit(route('show.category', slug))
}

function toggleStatus(id) {
    router.put(route('admin.categories.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated!'),
        onError: () => toast.error('Failed to update status')
    })
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('admin.categories.index'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}


function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.categories.index', { page }))
}
</script>