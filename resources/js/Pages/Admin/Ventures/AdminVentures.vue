<template>
    <AdminLayout>

        <Head title="Manage Ventures" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Ventures</h2>
                <!-- Buttons on the right -->
                <div class="flex gap-2">
                    <Link :href="route('admin.ventures.index')"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 shadow">
                    Back
                    </Link>
                </div>
            </div>

            <DataTable :items="collections.data" :meta="collections" :columns="columns" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(collections.current_page + 1)"
                @prev="goToPage(collections.current_page - 1)" @search="doSearch">
                <template #item-photo="{ item }">
                    <img :src="item.hero_image ? `/public/storage/${item.hero_image}` : ''" alt=""
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
                    <a :href="route('show.collection', item.slug)"
                        class="text-indigo-600 hover:underline text-sm font-semibold">
                        Preview
                    </a>
                    |
                    <button @click="editCollection(item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                    |
                    <button @click="confirmDelete(item.id)"
                        class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteTag">
                <p>Are you sure you want to delete this collection?</p>
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
    collections: Object,
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

function deleteTag() {
    if (deleteId.value) {
        router.delete(route('admin.collections.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Collection deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete collection')
        })
    }
}

function editCollection(id) {
    router.visit(route('admin.collections.edit', id))
}

function previewCollection(slug) {
    alert('slug: ' + slug);
    console.log('slug:', slug);
    console.log('route:', route('show.collection', slug));
    window.location.href = route('show.collection', slug);
}

function toggleStatus(id) {
    router.put(route('admin.collections.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated!'),
        onError: () => toast.error('Failed to update status')
    })
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('admin.ventures.ours.index'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.ventures.ours.index', { page }))
}
</script>