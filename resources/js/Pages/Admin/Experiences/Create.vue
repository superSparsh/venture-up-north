<template>
    <AdminLayout>

        <Head title="Add Experience" />

        <div class="w-full mx-auto p-6 bg-white rounded-xl shadow-xl">
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Add New Experience</h2>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- LEFT COLUMN -->
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Title <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image size:
                            738×500px (width × height)<span class="text-red-500">*</span></label>
                        <input type="file" @change="handleImage" />
                        <p v-if="form.errors.hero_image" class="text-red-500 text-sm mt-1">{{ form.errors.hero_image }}
                        </p>
                    </div>

                    <!-- Big Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Big Hero Image (Minimum image
                            size:
                            1200×600px (width × height)<span class="text-red-600">*</span></label>
                        <input type="file" @change="handleBigHero" />
                        <p v-if="form.errors.big_hero_image" class="text-red-500 text-sm mt-1">{{
                            form.errors.big_hero_image }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Description <span
                                class="text-red-500">*</span></label>
                        <EditorBlock v-model="editorContent" />
                        <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description
                        }}</p>
                    </div>

                    <!-- Rezdy URL -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Rezdy URL <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.rezdy_url" type="url"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.rezdy_url" class="text-red-500 text-sm mt-1">{{ form.errors.rezdy_url }}
                        </p>
                    </div>


                    <!-- Embedded Tags -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Tags</label>
                        <Multiselect v-model="form.tag_ids" :options="tags" :multiple="true" :close-on-select="false"
                            track-by="id" label="name" placeholder="Select tags" />
                    </div>
                </div>

                <!-- RIGHT COLUMN - SEO + Status -->
                <div class="space-y-6">
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
                        <textarea v-model="form.seo_description" rows="3"
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

                    <!-- Active Toggle -->
                    <div class="flex items-center space-x-3">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                            class="form-checkbox h-5 w-5 text-emerald-500" />
                        <label for="is_active" class="text-sm text-blueGray-700">Mark as active</label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('admin.experiences.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
                    Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Save Experience
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'
import EditorBlock from '@/Components/EditorBlock.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const toast = useToast()
const editorContent = ref({})

const props = defineProps({
    tags: Array,
})

const form = useForm({
    name: '',
    hero_image: null,
    big_hero_image: null,
    rezdy_url: '',
    description: null,
    seo_title: '',
    seo_description: '',
    seo_image: null,
    is_active: true,
    tag_ids: []
})

watch(editorContent, (val) => {
    form.description = val
})

function handleImage(e) {
    form.hero_image = e.target.files[0]
}
function handleBigHero(e) {
    form.big_hero_image = e.target.files[0]
}
function handleSeo(e) {
    form.seo_image = e.target.files[0]
}

function submit() {
    form.post(route('admin.experiences.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Experience created successfully!'),
        onError: () => toast.error('Failed to create experience'),
    })
}
</script>