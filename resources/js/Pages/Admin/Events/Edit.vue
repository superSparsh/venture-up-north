<template>
    <AdminLayout>

        <Head :title="`Edit Event: ${form.name}`" />

        <div class="w-full mx-auto p-6 bg-white rounded-xl shadow-xl">
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Edit Event ({{ form.name }})</h2>
            </div>
            <div class="mb-6">
                <div class="flex gap-4 pb-2">
                    <button v-for="tab in tabs" :key="tab" :class="[
                        'px-4 py-2 font-semibold',
                        currentTab === tab ? 'border-b-2 border-emerald-600 text-emerald-600' : 'text-gray-400'
                    ]" @click="currentTab = tab">
                        {{ tab }}
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="space-y-6">
                    <div v-show="currentTab === 'Basic Info'" class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                        <div class="space-y-6">

                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Event Name
                                    <span class="text-red-600">*</span></label>
                                <input v-model="form.name" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                                <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Embedded Town -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Towns</label>
                                <Multiselect v-model="form.town_ids" :options="towns" :multiple="true"
                                    :close-on-select="false" track-by="id" label="name" placeholder="Select Towns" />
                            </div>

                            <!-- Summary -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Summary <span
                                        class="text-red-600">*</span></label>
                                <textarea v-model="form.summary" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                                <p v-if="form.errors.summary" class="text-red-500 text-sm mt-1">{{ form.errors.summary
                                }}
                                </p>
                            </div>

                            <!-- Embedded Tags -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Tags</label>
                                <Multiselect v-model="form.tag_ids" :options="tags" :multiple="true"
                                    :close-on-select="false" track-by="id" label="name" placeholder="Select Tags" />
                            </div>


                        </div>
                        <div class="space-y-6">
                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Description <span
                                        class="text-red-600">*</span></label>
                                <EditorBlock v-model="editorContent" />
                                <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content
                                }}
                                </p>
                            </div>
                            <!-- Is Active Toggle -->
                            <div class="flex items-center">
                                <input v-model="form.is_active" type="checkbox" id="is_active"
                                    class="form-checkbox h-5 w-5 text-emerald-500 rounded" />
                                <label for="is_active" class="ml-3 block text-sm text-blueGray-700">Mark as
                                    active</label>
                            </div>
                        </div>
                    </div>

                    <div v-show="currentTab === 'Event Info'" class="space-y-6 mb-10">
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">
                                Event Date Label
                                <span class="text-sm text-gray-500 block font-normal mt-1">
                                    (Optional — e.g. “1st Friday each month”, “2025 to be announced”)
                                </span>
                            </label>
                            <input type="text" v-model="form.event_date_label" placeholder="e.g. 1st Friday each month"
                                class="form-input w-full" />
                            <p v-if="form.errors.event_date_label" class="text-red-500 text-sm mt-1">{{
                                form.errors.event_date_label }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1">Start Date</label>
                            <Datepicker v-model="form.start_date" :enable-time-picker="false" :min-date="today"
                                :format="dateFormat" placeholder="Select start date" />
                            <p v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">{{ form.errors.start_date
                                }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1">End Date</label>
                            <Datepicker v-model="form.end_date" :enable-time-picker="false"
                                :min-date="form.start_date || today" :format="dateFormat"
                                placeholder="Select end date" />
                            <p v-if="form.errors.end_date" class="text-red-500 text-sm mt-1">{{ form.errors.end_date }}
                            </p>
                        </div>

                    </div>

                    <div v-show="currentTab === 'Media'" class="space-y-6 mb-10">
                        <!-- Hero Image -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image
                                size:
                                738×500px (width × height)<span class="text-red-600">*</span></label>
                            <input type="file" @change="handleHero" />
                            <p v-if="form.errors.hero_image" class="text-red-500 text-sm mt-1">{{ form.errors.hero_image
                                }}
                            </p>
                            <div v-if="event.hero_image" class="mt-2">
                                <img :src="`/public/storage/${event.hero_image}`" class="h-24 rounded shadow" />
                            </div>
                        </div>

                        <!-- Big Hero Image -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Big Hero Image (Minimum image
                                size: 1200×800px (width × height)<span class="text-red-600">*</span></label>
                            <input type="file" @change="handleBigHero" />
                            <p v-if="form.errors.big_hero_image" class="text-red-500 text-sm mt-1">{{
                                form.errors.big_hero_image }}
                            </p>
                            <div v-if="event.big_hero_image" class="mt-2">
                                <img :src="`/public/storage/${event.big_hero_image}`" class="h-24 rounded shadow" />
                            </div>
                        </div>
                    </div>
                    <div v-show="currentTab === 'Location & Embed'" class="space-y-6 mb-10">
                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Map Embed URL</label>
                            <input v-model="form.location" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}
                            </p>
                        </div>
                        <!-- Video -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Video Embed URL</label>
                            <input v-model="form.video" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.video" class="text-red-500 text-sm mt-1">{{ form.errors.video }}</p>
                        </div>
                    </div>
                    <div v-show="currentTab === 'Contact Info'" class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                        <div class="space-y-6">
                            <!-- Address -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Address</label>
                                <textarea v-model="form.address" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                                <p v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address
                                    }}
                                </p>
                            </div>
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Email</label>
                                <textarea v-model="form.email" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                                <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email
                                    }}</p>
                            </div>
                            <!-- Contact -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Contact</label>
                                <textarea v-model="form.phone_number" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                                <p v-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">{{
                                    form.errors.phone_number
                                    }}</p>
                            </div>
                        </div>
                        <div class="space-y-6">

                            <!-- Opening times -->
                            <div>
                                <label class="block text-sm font-bold text-blueGray-700 mb-1">Opening Times</label>
                                <EditorBlock v-model="editorTimes" />
                                <p v-if="form.errors.opening_times" class="text-red-500 text-sm mt-1">{{
                                    form.errors.opening_times
                                }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-show="currentTab === 'Ticket Link'" class="space-y-6 mb-10">
                        <!-- Add Field -->
                        <div class="w-full text-right">
                            <button type="button" @click="form.custom_buttons.push({ label: '', value: '' })"
                                class="mt-2 text-md px-3 py-1 bg-gray text-white rounded hover:bg-gray-300">
                                + Add Button
                            </button>
                        </div>

                        <draggable v-model="form.custom_buttons" :item-key="index" handle=".handle" class="space-y-2">
                            <template #item="{ element: field, index }">
                                <div class="flex gap-2 items-center bg-gray-100 p-2 rounded">
                                    <!-- Drag Handle -->
                                    <span class="cursor-move text-gray-400 handle">☰</span>

                                    <!-- Label -->
                                    <input v-model="field.label" placeholder="Label"
                                        class="w-1/4 border px-2 py-1 rounded" />

                                    <!-- Value -->
                                    <input v-model="field.value" placeholder="Value"
                                        class="flex-1 border px-2 py-1 rounded" />

                                    <!-- Show on frontend -->
                                    <!-- <label class="flex items-center gap-1 text-xs text-gray-600">
                                        <input type="checkbox" v-model="field.show" />
                                        Show
                                    </label> -->

                                    <!-- Remove button -->
                                    <button type="button" @click="form.custom_buttons.splice(index, 1)"
                                        class="text-red-600 text-xl leading-none">
                                        &times;
                                    </button>
                                </div>
                            </template>
                        </draggable>
                    </div>
                    <div v-show="currentTab === 'Social Links'" class="space-y-6 mb-10">
                        <!-- Add Field -->
                        <div class="w-full text-right">
                            <button type="button" @click="form.social_links.push({ label: '', value: '' })"
                                class="mt-2 text-md px-3 py-1 bg-gray text-white rounded hover:bg-gray-300">
                                + Add Social Link
                            </button>
                        </div>
                        <draggable v-model="form.social_links" :item-key="index" handle=".handle" class="space-y-2">
                            <template #item="{ element: field, index }">
                                <div class="flex gap-2 items-center bg-gray-100 p-2 rounded">
                                    <!-- Drag Handle -->
                                    <span class="cursor-move text-gray-400 handle">☰</span>

                                    <!-- Label -->
                                    <input v-model="field.label" placeholder="Label"
                                        class="w-1/4 border px-2 py-1 rounded" />

                                    <!-- Value -->
                                    <input v-model="field.value" placeholder="Value"
                                        class="flex-1 border px-2 py-1 rounded" />

                                    <!-- Remove button -->
                                    <button type="button" @click="form.social_links.splice(index, 1)"
                                        class="text-red-600 text-xl leading-none">
                                        &times;
                                    </button>
                                </div>
                            </template>
                        </draggable>


                    </div>
                    <div v-show="currentTab === 'SEO'" class="space-y-6 mb-10">
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Title</label>
                            <input v-model="form.seo_title" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.seo_title" class="text-red-500 text-sm mt-1">{{ form.errors.seo_title
                                }}
                            </p>
                        </div>

                        <!-- SEO Description -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Description</label>
                            <textarea v-model="form.seo_description" rows="2"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.seo_description" class="text-red-500 text-sm mt-1">{{
                                form.errors.seo_description }}</p>
                        </div>

                        <!-- SEO Image -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Image (Minimum image size:
                                1200×630px (width × height))</label>
                            <input type="file" @change="handleSeo" />
                            <p v-if="form.errors.seo_image" class="text-red-500 text-sm mt-1">{{ form.errors.seo_image
                                }}
                            </p>
                            <div v-if="event.seo_image" class="mt-2">
                                <img :src="`/public/storage/${event.seo_image}`" class="h-24 rounded shadow" />
                            </div>
                        </div>
                    </div>
                    <div v-show="currentTab === 'Other Information'" class="space-y-6 mb-10">
                        <!-- Add Field -->
                        <div class="w-full text-right">
                            <button type="button"
                                @click="form.custom_fields.push({ label: '', value: '', show: false })"
                                class="mt-2 text-md px-3 py-1 bg-gray text-white rounded hover:bg-gray-300">
                                + Add Field
                            </button>
                        </div>
                        <draggable v-model="form.custom_fields" :item-key="index" handle=".handle" class="space-y-2">
                            <template #item="{ element: field, index }">
                                <div class="flex gap-2 items-center bg-gray-100 p-2 rounded">
                                    <!-- Drag Handle -->
                                    <span class="cursor-move text-gray-400 handle">☰</span>

                                    <!-- Label -->
                                    <input v-model="field.label" placeholder="Label"
                                        class="w-1/4 border px-2 py-1 rounded" />

                                    <!-- Value -->
                                    <input v-model="field.value" placeholder="Value"
                                        class="flex-1 border px-2 py-1 rounded" />

                                    <!-- Show on frontend -->
                                    <label class="flex items-center gap-1 text-xs text-gray-600">
                                        <input type="checkbox" v-model="field.show" />
                                        Show
                                    </label>

                                    <!-- Remove button -->
                                    <button type="button" @click="form.custom_fields.splice(index, 1)"
                                        class="text-red-600 text-xl leading-none">
                                        &times;
                                    </button>
                                </div>
                            </template>
                        </draggable>

                    </div>

                </div>

                <!-- Actions -->
                <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('admin.events.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
                    Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Update Event
                    </button>
                </div>
            </form>
        </div>
        <div class="space-y-6 mt-5">
            <!-- Contributor Agreements Panel -->
            <div v-if="props.agreements && props.agreements.length"
                class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-900">Contributor Agreements</h3>
                    <span class="text-xs text-gray-500">
                        {{props.agreements.filter(a => a.accepted).length}} / {{ props.agreements.length }} accepted
                    </span>
                </div>

                <ul class="space-y-2">
                    <li v-for="(a, idx) in props.agreements" :key="idx"
                        class="flex items-start justify-between gap-3 rounded-lg border border-gray-100 px-3 py-2">
                        <div class="text-sm text-gray-800">
                            <span class="font-medium">{{ a.label }}</span>
                            <span v-if="a.required"
                                class="ml-2 inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-600"
                                title="Required by submitter terms">
                                Required
                            </span>
                        </div>

                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="a.accepted
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'bg-rose-50 text-rose-700'">
                            {{ a.accepted ? 'Accepted' : 'Not Accepted' }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import EditorBlock from '@/Components/EditorBlock.vue'
import { useToast } from 'vue-toastification'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import draggable from 'vuedraggable'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const toast = useToast()
const editorRef = ref(null)

const tabs = ['Basic Info', 'Event Info', 'Media', 'Location & Embed', 'Contact Info', 'Ticket Link', 'Social Links', 'SEO', 'Other Information']
const currentTab = ref('Basic Info')

const props = defineProps({
    event: Object,
    tags: Array,
    towns: Array,
    agreements: { type: Array, default: () => [] },
})
const editorContent = ref(JSON.parse(props.event.content))
const editorTimes = ref(JSON.parse(props.event.opening_times))

const form = useForm({
    _method: 'PUT',
    name: props.event.name,
    hero_image: null,
    big_hero_image: null,
    content: props.event.content,
    opening_times: props.event.opening_times,
    summary: props.event.summary,
    address: props.event.address,
    email: props.event.email,
    phone_number: props.event.phone_number,
    location: props.event.location,
    video: props.event.video,
    event_date_label: props.event.event_date_label,
    start_date: props.event.start_date,
    end_date: props.event.end_date,
    is_active: Boolean(props.event.is_active),
    seo_title: props.event.seo_title,
    seo_description: props.event.seo_description,
    tag_ids: props.event.tags || [],
    town_ids: props.event.towns || [],
    seo_image: null,
    custom_fields: (props.event.custom_fields || []).map(field => ({
        label: field.label || '',
        value: field.value || '',
        show: field.show === '1' || field.show === true
    })),
    custom_buttons: (props.event.custom_buttons || []).map(field => ({
        label: field.label || '',
        value: field.value || '',
    })),
    social_links: (props.event.social_links || []).map(field => ({
        label: field.label || '',
        value: field.value || '',
    }))
})

function handleHero(e) {
    form.hero_image = e.target.files[0]
}

function handleBigHero(e) {
    form.big_hero_image = e.target.files[0]
}

function handleSeo(e) {
    form.seo_image = e.target.files[0]
}

async function submit() {
    form.content = editorContent.value
    form.opening_times = editorTimes.value

    form.post(route('admin.events.update', props.event.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Event updated successfully'),
        onError: () => toast.error('Failed to update event'),
    })
}

const dateFormat = (date) => {
    const d = new Date(date)
    const day = String(d.getDate()).padStart(2, '0')
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const year = d.getFullYear()
    return `${day}/${month}/${year}`
}
</script>
<style scoped>
.mb-10 {
    margin-bottom: 2.5rem !important;
}
</style>