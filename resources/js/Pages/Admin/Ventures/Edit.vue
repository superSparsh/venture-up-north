<template>
    <AdminLayout>

        <Head :title="`Edit Venture: ${form.title}`" />

        <div class="w-full mx-auto p-6 bg-white rounded-xl shadow-xl">
            <!-- Header -->
            <div class="mb-6 border-b pb-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-blueGray-800">
                    Edit Venture ({{ form.title }})
                </h2>
                <Link :href="route('admin.ventures.index')"
                    class="px-4 py-2 text-sm border rounded-md text-gray-600 hover:bg-gray-100">
                Back
                </Link>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- LEFT COLUMN -->
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Title <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.title" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                    </div>

                    <!-- Owner -->
                    <!-- <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Owner User ID</label>
                        <input v-model="form.owner_user_id" type="number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                    </div> -->

                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Owner Name</label>
                        <input v-model="form.owner_guest_name" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                    </div>


                    <!-- Visibility -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Visibility</label>
                        <select v-model="form.visibility"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option v-for="opt in visibilityOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>


                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Status</label>
                        <select v-model="form.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image size:
                            738×500px (width × height)</label>
                        <input type="file" @change="handleHero" />
                        <p v-if="form.errors.cover_image_url" class="text-red-500 text-sm mt-1">{{ form.errors.cover_image_url }}
                        </p>
                        <div v-if="venture.cover_image_url" class="mt-2">
                            <img :src="`/public/storage/${venture.cover_image_url}`" class="h-24 rounded shadow" />
                        </div>
                    </div>



                </div>

                <!-- RIGHT COLUMN -->
                <div class="space-y-6">
                    <!-- SEO Title -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Title</label>
                        <input v-model="form.seo_title" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                    </div>

                    <!-- SEO Description -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Description</label>
                        <textarea v-model="form.seo_description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"></textarea>
                    </div>

                    <!-- SEO Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Image (min 1200×630px)</label>
                        <input type="file" @change="handleSeo" />
                        <div v-if="venture.og_image_url" class="mt-2">
                            <img :src="`/public/storage/${venture.og_image_url}`" class="h-24 rounded shadow" />
                        </div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('admin.ventures.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">Cancel</Link>
                    <Link type="button" :href="route('ventures.show', venture.slug)"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                    Preview
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { ref } from 'vue'
import EditorBlock from '@/Components/EditorBlock.vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
    venture: Object
})

const toast = useToast()
const editorContent = ref(JSON.parse(props.venture.content || '{}'))
const editorRef = ref(null)

const visibilityOptions = [
    { value: 'public', label: 'Public' },
    { value: 'unlisted', label: 'Unlisted' },
    { value: 'private', label: 'Private' },
]

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'submitted', label: 'Submitted' },
    { value: 'approved', label: 'Approved' },
    { value: 'published', label: 'Published' },
    { value: 'archived', label: 'Archived' },
]

const form = useForm({
    _method: 'PUT',
    title: props.venture.title,
    owner_guest_name: props.venture.owner_guest_name,
    seo_title: props.venture.seo_title,
    seo_description: props.venture.seo_description,
    og_image_url: null,
    cover_image_url: null,
    visibility: props.venture.visibility || 'unlisted',
    status: props.venture.status || 'approved',
    is_published: Boolean(props.venture.is_published),
})

function handleSeo(e) {
    form.og_image_url = e.target.files[0]
}

function handleHero(e) {
    form.cover_image_url = e.target.files[0]
}

async function submit() {
    form.post(route('admin.ventures.update', props.venture.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Venture updated successfully!'),
        onError: () => toast.error('Failed to update venture'),
    })
}
</script>
