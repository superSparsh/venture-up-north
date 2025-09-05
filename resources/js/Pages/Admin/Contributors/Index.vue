<template>
    <AdminLayout>

        <Head title="Manage Contributors" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Contributors</h2>
                <!-- <Link :href="route('admin.team-members.create')"
                    class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                + Add Contributor
                </Link> -->
            </div>

            <DataTable :items="contributors.data" :meta="contributors" :columns="columns" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(contributors.current_page + 1)"
                @prev="goToPage(contributors.current_page - 1)">
                <template #item-photo="{ item }">
                    <img :src="item.photo ? `/public/storage/${item.photo}` : ''" alt=""
                        class="w-10 h-10 object-cover rounded-full" />
                </template>
                <template #item-display_name="{ item }">
                    {{ item.user?.name || '-' }} / {{ item.author?.display_name || '-' }}
                </template>
                <template #item-status="{ item }">
                    <button @click="toggleStatus(item.id)" :class="[
                        'px-2 py-1 rounded text-xs font-semibold inline-block transition',
                        item.status == 'active' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200'
                    ]">
                        {{ item.status == 'active' ? 'Active' : 'Blocked' }}
                    </button>
                </template>

                <template #actions="{ item }">
                    <button @click="openProfile(item)" class="text-indigo-600 hover:underline text-sm font-semibold">
                        Show Profile
                    </button>
                    |
                    <button @click="editMember(item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                    |
                    <button @click="confirmDelete(item.id)"
                        class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteContributor">
                <p>Are you sure you want to delete this contributor?</p>
            </Modal>

            <!-- Show Modal -->
            <ShowContributorProfileModal :show="showProfileModal" :contributor="selectedContributor"
                @close="showProfileModal = false" />
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
import ShowContributorProfileModal from '@/Components/ShowContributorProfileModal.vue'


const props = defineProps({
    contributors: Object
})

const columns = [
    { key: 'photo', label: 'Photo' },
    { key: 'display_name', label: 'Name / Display Name' },
    { key: 'user.email', label: 'Email' },
    { key: 'status', label: 'Status' }
]

const showModal = ref(false)
const deleteId = ref(null)
const toast = useToast()
const selectedContributor = ref(null)
const showProfileModal = ref(false)

function openProfile(contributor) {
    selectedContributor.value = { ...contributor }
    showProfileModal.value = true
}

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteContributor() {
    if (deleteId.value) {
        router.delete(route('admin.contributors.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Contributor deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete contributor')
        })
    }
}

function editMember(id) {
    router.visit(route('admin.contributors.edit', id))
}

function toggleStatus(id) {
    const row = props.contributors.data.find(c => c.id === id)
    router.put(route('admin.contributors.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            if (row) row.status = row.status === 'active' ? 'blocked' : 'active'
            toast.success('Status updated!')
        },
        onError: () => toast.error('Failed to update status')
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.contributors.index', { page }))
}
</script>