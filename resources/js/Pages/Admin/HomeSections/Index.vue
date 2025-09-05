<template>

    <Head title="Manage Home Sections" />

    <AdminLayout>
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Home Sections</h2>
                <Link :href="route('admin.home-sections.create')"
                    class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                + Add Section
                </Link>
            </div>

            <div class="block w-full overflow-x-auto bg-white rounded shadow-lg">
                <DataTable :items="sections.data" :columns="[
                    { key: 'title', label: 'Title' },
                    { key: 'order', label: 'Order' },
                    { key: 'is_active', label: 'Status' }
                ]" :meta="sections" :pagination="true" :searchable="true" :downloadable="true"
                    @next="goToPage(sections.current_page + 1)" @prev="goToPage(sections.current_page - 1)">
                    <template #item-is_active="{ item }">
                        <button @click="toggleStatus(item.id)" :class="[
                            'px-2 py-1 rounded text-xs font-semibold inline-block transition',
                            item.is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200'
                        ]">
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </template>

                    <template #actions="{ item }">
                        <button @click="editSection(item.id)"
                            class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                        |
                        <button @click="confirmDelete(item.id)"
                            class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                    </template>
                </DataTable>
            </div>
        </div>
        <Modal v-model="showModal" title="Delete Section" @confirm="deleteSection">
            <p>Are you sure you want to delete this section? This action cannot be undone.</p>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { Head } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import { useToast } from 'vue-toastification'
import DataTable from '@/Components/DataTable.vue'

defineProps({ sections: Object })

const showModal = ref(false)
const deleteId = ref(null)
const toast = useToast()


function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function editSection(id) {
    router.visit(route('admin.home-sections.edit', id))
}

function deleteSection() {
    if (deleteId.value) {
        router.delete(route('admin.home-sections.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Section deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => {
                toast.error('Failed to delete section')
            }
        })
    }
}
function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.home-sections.index', { page }))
}
function toggleStatus(id) {
    router.put(route('admin.home-sections.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated.'),
        onError: () => toast.error('Failed to update status.'),
    })
}

</script>