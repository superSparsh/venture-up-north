<template>
    <AdminLayout>

        <Head :title="`Edit Classifications: ${form.name}`" />

        <div class="w-full mx-auto bg-white rounded-xl shadow-xl p-8">
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Edit Classifications ({{ form.name }})</h2>
            </div>

            <form @submit.prevent="submit" >
                <div class="space-y-6 mb-10" >

                    <!-- Tag Name -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Tag Name <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Summary -->
                    <!-- <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Summary <span
                                class="text-red-600">*</span></label>
                        <textarea v-model="form.summary" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.summary" class="text-red-500 text-sm mt-1">{{ form.errors.summary }}</p>
                    </div> -->

                    <!-- Hero Image -->
                    <!-- <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image size:
                            738×500px (width × height)</label>
                        <input type="file" @change="handleHero" />
                        <p v-if="form.errors.hero_image" class="text-red-500 text-sm mt-1">{{ form.errors.hero_image }}
                        </p>
                        <div v-if="tag.hero_image" class="mt-2">
                            <img :src="`/public/storage/${tag.hero_image}`" class="h-24 rounded shadow" />
                        </div>
                    </div> -->

                    <!--Big Hero Image -->
                    <!-- <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Big Hero Image (Minimum image
                            size:
                            1200×600px (width × height)</label>
                        <input type="file" @change="handleBigHero" />
                        <p v-if="form.errors.big_hero_image" class="text-red-500 text-sm mt-1">{{
                            form.errors.big_hero_image }}
                        </p>
                        <div v-if="tag.big_hero_image" class="mt-2">
                            <img :src="`/public/storage/${tag.big_hero_image}`" class="h-24 rounded shadow" />
                        </div>
                    </div> -->


                    <!-- Description -->
                    <!-- <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Description</label>
                        <EditorBlock v-model="editorContent" />
                        <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description
                        }}
                        </p>
                    </div> -->

                    <!-- Is Active Toggle -->
                    <div class="flex items-center">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                            class="form-checkbox h-5 w-5 text-emerald-500 rounded" />
                        <label for="is_active" class="ml-3 block text-sm text-blueGray-700">Mark as active</label>
                    </div>
                </div>
                <!-- SEO Title -->
                <!-- <div class="space-y-6"> -->

                <!-- <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Title</label>
                        <input v-model="form.seo_title" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.seo_title" class="text-red-500 text-sm mt-1">{{ form.errors.seo_title }}
                        </p>
                    </div> -->

                <!-- SEO Description -->
                <!-- <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Description</label>
                        <textarea v-model="form.seo_description" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.seo_description" class="text-red-500 text-sm mt-1">{{
                            form.errors.seo_description }}</p>
                    </div> -->

                <!-- SEO Image -->
                <!-- <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Image (Minimum image size:
                            1200×630px (width × height))</label>
                        <input type="file" @change="handleSeo" />
                        <p v-if="form.errors.seo_image" class="text-red-500 text-sm mt-1">{{ form.errors.seo_image }}
                        </p>
                        <div v-if="tag.seo_image" class="mt-2">
                            <img :src="`/public/storage/${tag.seo_image}`" class="h-24 rounded shadow" />
                        </div>
                    </div> -->
                <!-- Actions -->
                <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('admin.tags.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">Cancel</Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Update Classification
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

const props = defineProps({
    tag: Object,
})

const toast = useToast()
const editorContent = ref(JSON.parse(props.tag.description))

const form = useForm({
    _method: 'PUT',
    name: props.tag.name,
    // hero_image: null,
    // big_hero_image: null,
    // description: props.tag.description,
    // summary: props.tag.summary,
    is_active: Boolean(props.tag.is_active),
    // seo_title: props.tag.seo_title,
    // seo_description: props.tag.seo_description,
    // seo_image: null,
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


function submit() {
    // form.description = editorContent.value

    form.post(route('admin.tags.update', props.tag.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Classification updated successfully!'),
        onError: () => toast.error('Failed to update classification'),
    })
}
</script>
