<template>
    <AdminLayout>

        <Head title="Manage Contacts" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Contact Submissions</h2>
            </div>

            <DataTable :items="contacts.data" :meta="contacts" :columns="columns" :pagination="true" :searchable="true"
                :downloadable="true" @next="goToPage(contacts.current_page + 1)"
                @prev="goToPage(contacts.current_page - 1)" @search="doSearch">
                <template #actions="{ item }">
                    <button @click="openContact(item)" class="text-indigo-600 hover:underline text-sm font-semibold">
                        Show
                    </button>
                    |
                    <button @click="confirmDelete(item.id)"
                        class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteContact">
                <p>Are you sure you want to delete this contact submission?</p>
            </Modal>

            <!-- Show Modal -->
            <ShowContactModal :show="showContactModal" :contact="selectedContact" @close="showContactModal = false" />
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
import ShowContactModal from '@/Components/ShowContactModal.vue'

const props = defineProps({
    contacts: Object,
    filters: Object,
})

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'subject', label: 'Subject' }
]

const searchQuery = ref(props.filters?.search || '')

const showModal = ref(false)
const deleteId = ref(null)
const toast = useToast()
const selectedContact = ref(null)
const showContactModal = ref(false)

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteContact() {
    if (deleteId.value) {
        router.delete(route('admin.contacts.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Contact Submission deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete contact submission')
        })
    }
}


function openContact(contact) {
    selectedContact.value = { ...contact }
    showContactModal.value = true
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('admin.contacts.index'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.contacts.index', { page }))
}
</script>