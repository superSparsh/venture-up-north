<template>
    <AdminLayout>

        <Head :title="`Edit Content: ${form.name}`" />

        <div class="w-full mx-auto bg-white rounded-xl shadow-xl p-8">
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Edit Content ({{ form.name }})</h2>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">

                    <!-- Content Name -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Content Name <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Summary -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Summary <span
                                class="text-red-600">*</span></label>
                        <textarea v-model="form.summary" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.summary" class="text-red-500 text-sm mt-1">{{ form.errors.summary }}</p>
                    </div>

                    <!-- Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image size:
                            738×500px (width × height)</label>
                        <input type="file" @change="handleHero" />
                        <p v-if="form.errors.icon" class="text-red-500 text-sm mt-1">{{ form.errors.icon }}
                        </p>
                        <div v-if="category.icon" class="mt-2">
                            <img :src="`/public/storage/${category.icon}`" class="h-24 rounded shadow" />
                        </div>
                    </div>

                    <!--Big Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Big Hero Image (Minimum image
                            size:
                            1200×600px (width × height)</label>
                        <input type="file" @change="handleBigHero" />
                        <p v-if="form.errors.big_image" class="text-red-500 text-sm mt-1">{{
                            form.errors.big_image }}
                        </p>
                        <div v-if="category.big_image" class="mt-2">
                            <img :src="`/public/storage/${category.big_image}`" class="h-24 rounded shadow" />
                        </div>
                    </div>

                    <!-- Embedded Tags -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Tags</label>
                        <Multiselect v-model="form.tag_ids" :options="tags" :multiple="true" :close-on-select="false"
                            track-by="id" label="name" placeholder="Select tags" value-prop="id" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Description</label>
                        <EditorBlock v-model="editorContent" />
                        <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description
                        }}
                        </p>
                    </div>
                </div>
                <!-- SEO Title -->
                <div class="space-y-6">

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
                        <div v-if="category.seo_image" class="mt-2">
                            <img :src="`/public/storage/${category.seo_image}`" class="h-24 rounded shadow" />
                        </div>
                    </div>

                    <!-- Is Active Toggle -->
                    <div class="flex items-center">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                            class="form-checkbox h-5 w-5 text-emerald-500 rounded" />
                        <label for="is_active" class="ml-3 block text-sm text-blueGray-700">Mark as active</label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('admin.categories.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">Cancel</Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Update Content
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import EditorBlock from '@/Components/EditorBlock.vue'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
    category: Object,
    tags: Array,
})

const toast = useToast()
const editorContent = ref(JSON.parse(props.category.description))

const form = useForm({
    _method: 'PUT',
    name: props.category.name,
    icon: null,
    big_image: null,
    summary: props.category.summary,
    description: props.category.description,
    is_active: Boolean(props.category.is_active),
    seo_title: props.category.seo_title,
    seo_description: props.category.seo_description,
    seo_image: null,
    tag_ids: props.category.tags || [],
})

function handleHero(e) {
    form.icon = e.target.files[0]
}

function handleBigHero(e) {
    form.big_image = e.target.files[0]
}

function handleSeo(e) {
    form.seo_image = e.target.files[0]
}


function submit() {
    form.description = editorContent.value

    form.post(route('admin.categories.update', props.category.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Category updated successfully!'),
        onError: () => toast.error('Failed to update category'),
    })
}
</script>
