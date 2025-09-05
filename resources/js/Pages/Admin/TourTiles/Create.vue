<template>
    <AdminLayout>

        <Head title="Add Tour Tile" />

        <div class="w-full mx-auto p-6 bg-white rounded-xl shadow-xl">
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Add Tour Tile</h2>
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
                <div v-show="currentTab === 'Basic Info'" class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Title <span
                                    class="text-red-600">*</span></label>
                            <input v-model="form.title" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                        </div>

                        <!-- Rezdy URL -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Rezdy Widget URL <span
                                    class="text-red-600">*</span></label>
                            <input v-model="form.rezdy_url" type="url"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.rezdy_url" class="text-red-500 text-sm mt-1">{{ form.errors.rezdy_url
                                }}
                            </p>
                        </div>

                        <!-- Embedded Tags -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Tags</label>
                            <Multiselect v-model="form.tag_ids" :options="tags" :multiple="true"
                                :close-on-select="false" track-by="id" label="name" placeholder="Select tags" />
                        </div>

                        <!-- Embedded Town -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Towns</label>
                            <Multiselect v-model="form.town_ids" :options="towns" :multiple="true"
                                :close-on-select="false" track-by="id" label="name" placeholder="Select Towns" />
                        </div>

                        <!-- Is Active Toggle -->
                        <div class="flex items-center">
                            <input v-model="form.is_active" type="checkbox" id="is_active"
                                class="form-checkbox h-5 w-5 text-emerald-500 rounded" />
                            <label for="is_active" class="ml-3 block text-sm text-blueGray-700">Mark as active</label>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Description</label>
                            <EditorBlock ref="editorRef" />
                            <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content
                                }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Summary </label>
                            <textarea v-model="form.summary" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.summary" class="text-red-500 text-sm mt-1">{{ form.errors.summary
                                }}</p>
                        </div>
                    </div>
                </div>

                <div v-show="currentTab === 'Media'" class="space-y-6 mb-10">
                    <!-- Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image
                            size:
                            738×500px (width × height)<span class="text-red-600">*</span></label>
                        <input type="file" @change="handleHero" />
                        <p v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}
                        </p>
                    </div>

                    <!-- Big Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Big Hero Image (Minimum image
                            size: 1200×800px (width × height)<span class="text-red-600">*</span></label>
                        <input type="file" @change="handleBigHero" />
                        <p v-if="form.errors.big_hero_image" class="text-red-500 text-sm mt-1">{{
                            form.errors.big_hero_image }}
                        </p>
                    </div>
                </div>

                <div v-show="currentTab === 'SEO'" class="space-y-6 mb-10">
                    <!-- SEO Title -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Title</label>
                        <input v-model="form.seo_title" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.seo_title" class="text-red-500 text-sm mt-1">{{ form.errors.seo_title }}
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
                        <p v-if="form.errors.seo_image" class="text-red-500 text-sm mt-1">{{ form.errors.seo_image }}
                        </p>
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
                            <EditorBlock ref="editorTimes" />
                            <p v-if="form.errors.opening_times" class="text-red-500 text-sm mt-1">{{
                                form.errors.opening_times
                            }}
                            </p>
                        </div>
                    </div>
                </div>

                <div v-show="currentTab === 'Custom Buttons'" class="space-y-6 mb-10">
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

                <div v-show="currentTab === 'Other Information'" class="space-y-6 mb-10">
                    <div class="space-y-4">
                        <!-- Add Field Button -->
                        <div class="w-full text-right">
                            <button type="button"
                                @click="form.custom_fields.push({ label: '', value: '', show: false })"
                                class="mt-2 text-md px-3 py-1 bg-gray text-white rounded hover:bg-gray-300">
                                + Add Field
                            </button>
                        </div>
                        <draggable v-model="form.custom_fields" :item-key="index" handle=".handle">
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

                                    <!-- Show Toggle -->
                                    <label class="flex items-center gap-1 text-xs text-gray-600">
                                        <input type="checkbox" v-model="field.show" />
                                        Show
                                    </label>

                                    <!-- Delete Button -->
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
                    <Link :href="route('admin.tour-tiles.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
                    Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Save Tour Tile
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import draggable from 'vuedraggable'
import EditorBlock from '@/Components/EditorBlock.vue'

const tabs = ['Basic Info', 'Media', 'SEO', 'Contact Info', 'Custom Buttons', 'Social Links', 'Location & Embed', 'Other Information']
const currentTab = ref('Basic Info')

const toast = useToast()
const editorRef = ref(null)
const editorTimes = ref(null)

const props = defineProps({
    tags: Array,
    towns: Array,
})

const form = useForm({
    title: '',
    image: null,
    big_hero_image: null,
    rezdy_url: '',
    is_active: true,
    seo_title: '',
    seo_description: '',
    seo_image: null,
    tag_ids: [],
    summary: '',
    content: null,
    opening_times: null,
    location: '',
    video: '',
    address: '',
    email: '',
    phone_number: '',
    custom_fields: [],
    custom_buttons: [],
    social_links: [],
    town_ids: [],
})

const handleHero = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file
    }
}

const handleBigHero = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.big_hero_image = file
    }
}

function handleSeo(e) {
    form.seo_image = e.target.files[0]
}

async function submit() {
    const content = await editorRef.value.getContent()
    const times = await editorTimes.value.getContent()
    form.content = content
    form.opening_times = times

    form.post(route('admin.tour-tiles.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Tour Tile created successfully'),
        onError: () => toast.error('Failed to create tour tile'),
    })
}
</script>
<style scoped>
.mb-10 {
    margin-bottom: 2.5rem !important;
}
</style>