<template>
    <template>
        <div v-if="$page.props.flash?.success" class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ $page.props.flash.success }}
        </div>
    </template>
    <AdminLayout>

        <Head title="Contributor's Events" />

        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Contributor's Events</h2>
                <div class="flex space-x-3">
                    <!-- Contributor Posts -->
                    <Link :href="route('admin.events.index')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 shadow">
                    Back
                    </Link>
                </div>
            </div>

            <DataTable :items="events.data" :columns="columns" :meta="events" :pagination="true" :searchable="true"
                :downloadable="true" @next="goToPage(events.current_page + 1)" @prev="goToPage(events.current_page - 1)"
                @search="doSearch">
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
                <!-- Contributor Name -->
                <template #item-contributor="{ item }">
                    {{ item.user?.name || '-' }}
                </template>
                <template #item-status="{ item }">
                    <span :class="[
                        'px-2 py-1 rounded text-xs font-semibold inline-block',
                        item.status === 'approved'
                            ? 'bg-emerald-100 text-emerald-700'
                            : item.status === 'pending'
                                ? (item.pending_payload ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700')
                                : item.status === 'rejected'
                                    ? 'bg-rose-100 text-rose-700'
                                    : 'bg-gray-100 text-gray-700'
                    ]" :title="statusTitle(item)">
                        {{
                            item.status === 'approved'
                                ? 'Published'
                                : item.status === 'pending'
                                    ? (item.pending_payload ? 'Resubmission' : 'Pending Review')
                                    : item.status === 'rejected'
                                        ? 'Rejected'
                                        : 'Draft'
                        }}
                    </span>
                </template>

                <!-- Featured Badge -->
                <template #item-is_featured="{ item }">
                    <button @click="toggleStatus(item.id)" class="px-2 py-1 rounded text-xs font-semibold inline-block"
                        :class="item.is_featured ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-500 text-white'">
                        {{ item.is_featured ? '🔥 Featured' : 'Standard' }}
                    </button>
                </template>

                <!-- Actions -->
                <template #actions="{ item }">
                    <!-- Preview only when approved -->
                    <template v-if="item.status === 'approved'">
                        <Link :href="route('event.show', item.slug)"
                            class="text-indigo-600 hover:underline text-sm font-semibold">
                        Preview
                        </Link>
                        <span class="mx-1 text-gray-300">|</span>
                    </template>
                    <template v-if="item.status === 'pending'">
                        <Link :href="route('admin.events.review', item.slug)"
                            class="text-amber-600 hover:underline text-sm font-semibold">
                        Review
                        </Link>
                        <span class="mx-1 text-gray-300">|</span>
                    </template>

                    <Link :href="route('admin.events.edit', item.id)"
                        class="text-blue-600 hover:underline text-sm font-semibold">
                    Edit
                    </Link>

                    <span class="mx-1 text-gray-300">|</span>

                    <button @click="confirmDelete(item.id)" class="text-red-600 hover:underline text-sm font-semibold">
                        Delete
                    </button>
                </template>
            </DataTable>

            <Modal v-model="showModal" title="Confirm Delete" @confirm="deleteEvent">
                <p>Are you sure you want to delete this event?</p>
            </Modal>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router, Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '../../Layout.vue'
import DataTable from '@/Components/DataTable.vue'
import { useToast } from 'vue-toastification'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    events: Object,
    filters: Object,
})
console.log(props.events)
const toast = useToast()
const page = usePage()
const showModal = ref(false)
const deleteId = ref(null)

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'contributor', label: 'Contributor' },
    { key: 'town', label: 'Town' },
    { key: 'status', label: 'Status' },
    { key: 'is_featured', label: 'Featured' },
]

const searchQuery = ref(props.filters?.search || '')

function goToPage(page) {
    router.visit(route('admin.events.index', { page }))
}

function confirmDelete(id) {
    deleteId.value = id
    showModal.value = true
}

function toggleStatus(id) {
    router.put(route('admin.magazines.toggle-status', id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('The magazine article has been marked as Featured.'),
        onError: () => toast.error('Could not toggle Featured status.')
    })
}

function doSearch(query) {
    searchQuery.value = query
    router.get(route('admin.events.contributors'), { search: query }, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

function deleteEvent() {
    if (!deleteId.value) return
    router.delete(route('admin.events.destroy', deleteId.value), {
        onFinish: () => {
            toast.success('Event deleted successfully!')
            showModal.value = false
            deleteId.value = null
        },
        onError: () => toast.error('Failed to delete event')
    })
}

// Tooltip helper for status
function statusTitle(item) {
    if (item.status === 'approved') return 'Published'
    if (item.status === 'pending') return item.pending_payload ? 'Changes to a live post are awaiting review' : 'Awaiting admin review'
    if (item.status === 'rejected') return 'Rejected by admin'
    return 'Draft (not submitted)'
}

onMounted(() => {
    if (page.props.flash?.success) {
        toast.success(page.props.flash.success)
    }
    if (page.props.flash?.error) {
        toast.error(page.props.flash.error)
    }
})
</script>