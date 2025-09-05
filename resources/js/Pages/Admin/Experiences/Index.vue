<template>
    <AdminLayout>

        <Head title="Manage Experiences" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Experiences</h2>
                <Link :href="route('admin.experiences.create')"
                    class="bg-emerald-600 text-white px-4 py-2 rounded shadow hover:bg-emerald-700">
                + Add Experience
                </Link>
            </div>
        </div>
        <DataTable :items="experiences.data" :meta="experiences" :columns="columns" :pagination="true"
            :searchable="true" :downloadable="true" @next="goToPage(experiences.current_page + 1)"
            @prev="goToPage(experiences.current_page - 1)">
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
                <button @click="previewExperience(item.slug)" class="text-indigo-600 hover:underline text-sm font-semibold">
                    Preview
                </button>
                |
                <button @click="editExperience(item.id)"
                    class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                |
                <button @click="confirmDelete(item.id)"
                    class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
            </template>
        </DataTable>
        <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteExperience">
            <p>Are you sure you want to delete this experience?</p>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'
import Modal from '@/Components/Modal.vue'
import DataTable from '@/Components/DataTable.vue'


const props = defineProps({
    experiences: Array
})

const toast = useToast()

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'slug', label: 'Slug' },
    { key: 'is_active', label: 'Status' }
]

const showModal = ref(false)
const deleteId = ref(null)

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteExperience() {
    if (deleteId.value) {
        router.delete(route('admin.experiences.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Experience deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete experience')
        })
    }
}

function editExperience(id) {
    router.visit(route('admin.experiences.edit', id))
}

function previewExperience(slug) {
    router.visit(route('show.experience', slug))
}

function toggleStatus(id) {
    router.put(route('admin.experiences.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated!'),
        onError: () => toast.error('Failed to update status')
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.experiences.index', { page }))
}
</script>