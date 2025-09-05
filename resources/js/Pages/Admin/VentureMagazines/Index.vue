<template>
    <AdminLayout>

        <Head title="Venture Magazine" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Venture Magazine</h2>
                <div class="flex space-x-3">
                    <!-- New Contributor Posts Button -->
                    <Link :href="route('admin.venture-magazines.contributors')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 shadow">
                    Contributor Magazines
                    </Link>

                    <!-- Existing Add New Magazine -->
                    <Link :href="route('admin.venture-magazines.create')"
                        class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                    + Add New Magazine
                    </Link>


                </div>
            </div>


            <DataTable :items="magazines.data" :columns="columns" :meta="magazines" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(magazines.current_page + 1)"
                @prev="goToPage(magazines.current_page - 1)" @search="doSearch">
                <!-- Contributor Name -->
                <template #item-contributor="{ item }">
                    {{ item.contributor?.name || '-' }}
                </template>


                <!-- Featured Badge -->
                <template #item-is_featured="{ item }">
                    <button @click="toggleStatus(item.id)" class="px-2 py-1 rounded text-xs font-semibold inline-block"
                        :class="item.is_featured ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-500 text-white'">
                        {{ item.is_featured ? '🔥 Featured' : 'Standard' }}
                    </button>
                </template>


                <!-- Published Badge -->
                <template #item-is_published="{ item }">
                    <span :class="[
                        'px-2 py-1 rounded text-xs font-semibold inline-block',
                        item.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-500 text-white'
                    ]">
                        {{ item.is_published ? 'Published' : 'Draft' }}
                    </span>
                </template>

                <!-- Actions -->
                <template #actions="{ item }">
                    <Link :href="route('show.magazine', item.slug)"
                        class="text-indigo-600 hover:underline text-sm font-semibold">
                    Preview
                    </Link>
                    |
                    <Link :href="route('admin.venture-magazines.edit', item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">
                    Edit
                    </Link>
                    |
                    <button @click="confirmDelete(item.id)" class="text-red-600 hover:underline text-sm font-semibold">
                        Delete
                    </button>
                </template>
            </DataTable>
            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteMagazine">
                <p>Are you sure you want to delete this magazine?</p>
            </Modal>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import DataTable from '@/Components/DataTable.vue'
import { useToast } from 'vue-toastification'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    magazines: Object,
    filters: Object,
})

const toast = useToast()
const showModal = ref(false)
const deleteId = ref(null)

const columns = [
    { key: 'title', label: 'Title' },
    { key: 'contributor', label: 'Contributor' },
    { key: 'is_featured', label: 'Featured' },
    { key: 'is_published', label: 'Status' },
]

const searchQuery = ref(props.filters?.search || '')

function goToPage(page) {
    router.visit(route('admin.venture-magazines.index', { page }))
}

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function toggleStatus(id) {
    router.put(route('admin.magazines.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('The magazine article has been marked as Featured.'),
        onError: () => toast.error('The magazine article is no longer marked as Featured.')
    })
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('admin.venture-magazines.index'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}


function deleteMagazine() {
    if (deleteId.value) {
        router.delete(route('admin.venture-magazines.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Magazine deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete magazine')
        })
    }
}
</script>