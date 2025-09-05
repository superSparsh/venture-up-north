<template>
    <ContributorLayout>

        <Head title="Venture Magazine" />

        <div class="pt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-white">Venture Magazine</h2>
                <a :href="route('contributor.magazines.create')"
                    class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                    + Add New Magazine
                </a>
            </div>

            <DataTable :items="magazines.data" :columns="columns" :meta="magazines" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(magazines.current_page + 1)"
                @prev="goToPage(magazines.current_page - 1)">

                <!-- Published Badge -->
                <template #item-status="{ item }">
                    <span :class="[
                        'px-2 py-1 rounded text-xs font-semibold inline-block',
                        item.status === 'approved' ? 'bg-emerald-100 text-emerald-700' :
                            item.status === 'pending' ? 'bg-amber-100 text-amber-700' :
                                item.status === 'rejected' ? 'bg-rose-100 text-rose-700' :
                                    'bg-cyan-500 text-white'
                    ]">
                        {{
                            item.status === 'approved'
                                ? 'Published'
                                : item.status === 'pending'
                                    ? 'Awaiting Admin Approval'
                                    : item.status === 'rejected'
                                        ? 'Rejected by Admin'
                                        : 'Draft'
                        }}
                    </span>
                </template>

                <!-- Actions -->
                <template #actions="{ item }">
                    <!-- Preview (approved only) -->
                    <template v-if="canPreview(item)">
                        <Link :href="route('show.magazine', item.slug)"
                            class="text-indigo-600 hover:underline text-sm font-semibold">
                        Preview
                        </Link>
                    </template>

                    <!-- Edit -->
                    <template v-if="canEdit(item)">
                        <span class="mx-1 text-gray-400">|</span>
                        <a :href="route('contributor.magazines.edit', item.id)"
                            class="text-blue-600 hover:underline text-sm font-semibold" title="Edit">
                            Edit
                        </a>
                    </template>
                    <!-- Delete -->
                    <template v-if="canDelete(item)">
                        <span class="mx-1 text-gray-400">|</span>
                        <button @click="confirmDelete(item.id)"
                            class="text-red-600 hover:underline text-sm font-semibold">
                            Delete
                        </button>
                    </template>

                </template>
            </DataTable>
            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteMagazine">
                <p>Are you sure you want to delete this magazine?</p>
            </Modal>
        </div>
    </ContributorLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Head, Link, usePage } from '@inertiajs/vue3'
import ContributorLayout from '@/Layouts/ContributorLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import { useToast } from 'vue-toastification'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    magazines: Object
})

const toast = useToast()
const showModal = ref(false)
const deleteId = ref(null)

const columns = [
    { key: 'title', label: 'Title' },
    { key: 'status', label: 'Status' },
]

const page = usePage()

const isAdmin = computed(() => page.props.auth?.user?.role === 'admin')
const canPreview = (item) => item.status === 'approved'
const canEdit = (item) => isAdmin.value || ['draft', 'rejected'].includes(item.status)
const canDelete = (item) => isAdmin.value || ['draft', 'rejected'].includes(item.status)

function goToPage(page) {
    router.visit(route('contributor.magazines.index', { page }))
}

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteMagazine() {
    if (deleteId.value) {
        router.delete(route('contributor.magazines.destroy', deleteId.value), {
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