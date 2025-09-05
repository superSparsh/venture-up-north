<template>
    <AuthenticatedLayout>

        <Head title="Venture Magazine" />

        <div class="pt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Venture Magazine</h2>
                <Link :href="route('user.magazines.create')"
                    class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                + Add New Magazine
                </Link>
            </div>

            <DataTable :items="magazines.data" :columns="columns" :meta="magazines" :pagination="true"
                :searchable="true" :downloadable="true" @next="goToPage(magazines.current_page + 1)"
                @prev="goToPage(magazines.current_page - 1)">

                <!-- Published Badge -->
                <template #item-is_published="{ item }">
                    <span :class="[
                        'px-2 py-1 rounded text-xs font-semibold inline-block',
                        item.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-bison text-heavy'
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
                    <Link :href="route('user.magazines.edit', item.id)"
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
    { key: 'is_published', label: 'Status' },
]

function goToPage(page) {
    router.visit(route('user.magazines.index', { page }))
}

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteMagazine() {
    if (deleteId.value) {
        router.delete(route('user.magazines.destroy', deleteId.value), {
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