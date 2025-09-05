<template>
    <ContributorLayout>

        <Head title="Manage Events" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <!-- Title on the left -->
                <h2 class="text-2xl font-semibold text-white">Events</h2>

                <!-- Buttons on the right -->
                <div class="flex gap-2">
                    <a :href="route('contributor.events.create')"
                        class="bg-emerald-500 text-white px-4 py-2 rounded hover:bg-emerald-600 shadow">
                        + Add Event
                    </a>
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
                            class="px-2 py-1 rounded text-xs font-semibold bg-heavy text-white">
                            {{ town.name }}
                        </span>
                    </div>
                </template>

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
                        <a :href="route('contributor.events.edit', item.id)"
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

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteItem">
                <p>Are you sure you want to delete this event?</p>
            </Modal>
        </div>
    </ContributorLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import ContributorLayout from '@/Layouts/ContributorLayout.vue'
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
    { key: 'status', label: 'Status' }
]

const searchQuery = ref(props.filters?.search || '')

const showModal = ref(false)
const deleteId = ref(null)
const toast = useToast()

const page = usePage()

const isAdmin = computed(() => page.props.auth?.user?.role === 'admin')
const canPreview = (item) => item.status === 'approved'
const canEdit = (item) => isAdmin.value || ['draft', 'rejected'].includes(item.status)
// ^ approved edit should create a pending review server-side (no direct publish)
const canDelete = (item) => isAdmin.value || ['draft', 'rejected'].includes(item.status)

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function deleteItem() {
    if (deleteId.value) {
        router.delete(route('contributor.events.destroy', deleteId.value), {
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
    router.visit(route('contributor.events.edit', id))
}

function previewItem(slug) {
    router.visit(route('event.show', slug))
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('contributor.events.index'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

function goToPage(page) {
    if (!page || page < 1) return
    router.visit(route('contributor.events.index', { page }))
}
</script>