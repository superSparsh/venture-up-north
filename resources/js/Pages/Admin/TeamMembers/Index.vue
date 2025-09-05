<template>
    <AdminLayout>

        <Head title="Manage Team Members" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Team Members</h2>
                <Link :href="route('admin.team-members.create')"
                    class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                + Add Member
                </Link>
            </div>

            <DataTable :items="teamMembers.data" :meta="teamMembers" :columns="columns" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(teamMembers.current_page + 1)"
                @prev="goToPage(teamMembers.current_page - 1)">
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
                    <button @click="openProfile(item)"
                        class="text-indigo-600 hover:underline text-sm font-semibold">
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

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteMember">
                <p>Are you sure you want to delete this member?</p>
            </Modal>

            <!-- Show Modal -->
            <ShowProfileModal :show="showProfileModal" :member="selectedMember" @close="showProfileModal = false" />
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
import ShowProfileModal from '@/Components/ShowProfileModal.vue'


const props = defineProps({
    teamMembers: Object
})

const columns = [
    { key: 'photo', label: 'Photo' },
    { key: 'name', label: 'Name' },
    { key: 'user.email', label: 'Email' },
    { key: 'designation', label: 'Designation' },
    { key: 'order', label: 'Order' },
    { key: 'is_active', label: 'Status' }
]

const showModal = ref(false)
const deleteId = ref(null)
const toast = useToast()
const selectedMember = ref(null)
const showProfileModal = ref(false)

function openProfile(member) {
    selectedMember.value = { ...member }
    showProfileModal.value = true
}

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteMember() {
    if (deleteId.value) {
        router.delete(route('admin.team-members.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Member deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete member')
        })
    }
}

function editMember(id) {
    router.visit(route('admin.team-members.edit', id))
}

function toggleStatus(id) {
    router.put(route('admin.team-members.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated!'),
        onError: () => toast.error('Failed to update status')
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.team-members.index', { page }))
}
</script>