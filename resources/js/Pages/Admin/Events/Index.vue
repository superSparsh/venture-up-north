<template>
    <AdminLayout>

        <Head title="Manage Events" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <!-- Title on the left -->
                <h2 class="text-2xl font-semibold text-blueGray-800">Events</h2>

                <!-- Buttons on the right -->
                <div class="flex gap-2">
                    <Link :href="route('admin.events.settings')"
                        class="bg-heavy text-white px-4 py-2 rounded hover:bg-bison shadow">
                    Event Page Settings
                    </Link>
                    
                    <Link :href="route('admin.events.contributors')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 shadow">
                    Contributor Events
                    </Link>

                    <Link :href="route('admin.events.create')"
                        class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                    + Add Events
                    </Link>
                </div>
            </div>


            <DataTable :items="events.data" :meta="events" :columns="columns" :pagination="true" :searchable="true"
                :downloadable="true" @next="goToPage(listings.current_page + 1)"
                @prev="goToPage(events.current_page - 1)" @search="doSearch">
                <template #item-photo="{ item }">
                    <img :src="item.hero_image ? `/public/storage/${item.hero_image}` : ''" alt=""
                        class="w-10 h-10 object-cover rounded-full" />
                </template>
                <template #item-town="{ item }">
                    <div class="flex flex-wrap gap-1">
                        <span v-for="town in item.towns" :key="town.id"
                            class="px-2 py-1 rounded text-xs font-semibold bg-heavy text-bison">
                            {{ town.name }}
                        </span>
                    </div>
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
                    <button @click="previewItem(item.slug)"
                        class="text-indigo-600 hover:underline text-sm font-semibold">
                        Preview
                    </button>
                    |
                    <button @click="editItem(item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">Edit</button>
                    |
                    <button @click="confirmDelete(item.id)"
                        class="text-red-600 hover:underline text-sm font-semibold">Delete</button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteItem">
                <p>Are you sure you want to delete this event?</p>
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
    events: Object,
    filters: Object,
})

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'slug', label: 'Slug' },
    { key: 'town', label: 'Town' },
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

function deleteItem() {
    if (deleteId.value) {
        router.delete(route('admin.events.destroy', deleteId.value), {
            onFinish: () => {
                toast.success('Event deleted successfully!')
                showModal.value = false
                deleteId.value = null
            },
            onError: () => toast.error('Failed to delete event')
        })
    }
}

function editItem(id) {
    router.visit(route('admin.events.edit', id))
}

function previewItem(slug) {
    router.visit(route('event.show', slug))
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('admin.events.index'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

function toggleStatus(id) {
    router.put(route('admin.events.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Status updated!'),
        onError: () => toast.error('Failed to update status')
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('admin.events.index', { page }))
}
</script>