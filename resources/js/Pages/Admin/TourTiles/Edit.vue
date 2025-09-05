<template>
    <AdminLayout>

        <Head :title="`Edit Tour Tile: ${form.title}`" />

        <div class="w-full mx-auto bg-white rounded-xl shadow-xl p-8">
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Edit Tour Tile ({{ form.title }})</h2>
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
                                    class="text-red-500">*</span></label>
                            <input v-model="form.title" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                        </div>

                        <!-- Rezdy Category URL -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Rezdy URL <span
                                    class="text-red-600">*</span></label>
                            <input v-model="form.rezdy_url" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.rezdy_url" class="text-red-500 text-sm mt-1">{{ form.errors.rezdy_url
                                }}
                            </p>
                        </div>

                        <!-- Embedded Tags -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Tags</label>
                            <Multiselect v-model="form.tag_ids" :options="tags" :multiple="true"
                                :close-on-select="false" track-by="id" label="name" placeholder="Select tags"
                                value-prop="id" />
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
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Description <span
                                    class="text-red-600">*</span></label>
                            <EditorBlock v-model="editorContent" />
                            <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content
                            }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Summary <span
                                    class="text-red-600">*</span></label>
                            <textarea v-model="form.summary" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.summary" class="text-red-500 text-sm mt-1">{{ form.errors.summary
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <div v-show="currentTab === 'Media'" class="space-y-6 mb-10">
                    <!-- Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image size:
                            738×500px (width × height) <span class="text-red-600">*</span></label>
                        <input type="file" @change="handleHero" />
                        <p v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}
                        </p>
                        <div v-if="tourTile.image" class="mt-2">
                            <img :src="`/public/storage/${tourTile.image}`" class="h-24 rounded shadow" />
                        </div>
                    </div>

                    <!--Big Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Big Hero Image (Minimum image
                            size:
                            1200×600px (width × height) <span class="text-red-600">*</span></label>
                        <input type="file" @change="handleBigHero" />
                        <p v-if="form.errors.big_hero_image" class="text-red-500 text-sm mt-1">{{
                            form.errors.big_hero_image }}
                        </p>
                        <div v-if="tourTile.big_hero_image" class="mt-2">
                            <img :src="`/public/storage/${tourTile.big_hero_image}`" class="h-24 rounded shadow" />
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
                        <div v-if="tourTile.seo_image" class="mt-2">
                            <img :src="`/public/storage/${tourTile.seo_image}`" class="h-24 rounded shadow" />
                        </div>
                    </div>
                </div>

                <div v-show="currentTab === 'Other Information'" class="space-y-6 mb-10">
                    <!-- Add Field -->
                    <div class="w-full text-right">
                        <button type="button" @click="form.custom_fields.push({ label: '', value: '', show: false })"
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

                <!-- Actions -->
                <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('admin.tour-tiles.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">Cancel</Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Update Tour Tile
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'
import { ref } from 'vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import EditorBlock from '@/Components/EditorBlock.vue'
import draggable from 'vuedraggable'


const props = defineProps({
    tourTile: Object,
    tags: Array,
    towns: Array,
})


const editorRef = ref(null)

const tabs = ['Basic Info', 'Media', 'SEO', 'Contact Info', 'Custom Buttons', 'Social Links', 'Location & Embed', 'Other Information']
const currentTab = ref('Basic Info')

const editorContent = ref(JSON.parse(props.tourTile.content))
const editorTimes = ref(JSON.parse(props.tourTile.opening_times))
const toast = useToast()

const form = useForm({
    _method: 'PUT',
    title: props.tourTile.title,
    rezdy_url: props.tourTile.rezdy_url,
    image: null,
    big_hero_image: null,
    is_active: Boolean(props.tourTile.is_active),
    seo_title: props.tourTile.seo_title,
    seo_description: props.tourTile.seo_description,
    seo_image: null,
    tag_ids: props.tourTile.tags || [],
    content: props.tourTile.content,
    opening_times: props.tourTile.opening_times,
    summary: props.tourTile.summary,
    address: props.tourTile.address,
    email: props.tourTile.email,
    phone_number: props.tourTile.phone_number,
    location: props.tourTile.location,
    video: props.tourTile.video,
    town_ids: props.tourTile.towns || [],
    custom_fields: (props.tourTile.custom_fields || []).map(field => ({
        label: field.label || '',
        value: field.value || '',
        show: field.show === '1' || field.show === true
    })),
    custom_buttons: (props.tourTile.custom_buttons || []).map(field => ({
        label: field.label || '',
        value: field.value || '',
    })),
    social_links: (props.tourTile.social_links || []).map(field => ({
        label: field.label || '',
        value: field.value || '',
    }))
})

function handleHero(e) {
    form.image = e.target.files[0]
}

function handleBigHero(e) {
    form.big_hero_image = e.target.files[0]
}

function handleSeo(e) {
    form.seo_image = e.target.files[0]
}

function submit() {
    form.content = editorContent.value
    form.opening_times = editorTimes.value
    form.post(route('admin.tour-tiles.update', props.tourTile.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Tour Tile updated successfully!'),
        onError: () => toast.error('Failed to update tour tile'),
    })
}
</script>
<style scoped>
.mb-10 {
    margin-bottom: 2.5rem !important;
}
</style>
