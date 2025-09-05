<template>
    <AdminLayout>

        <Head title="Add Magazine" />

        <div class="w-full bg-white p-8 rounded-xl shadow-xl">
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Add New Magazine</h2>
            </div>

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

                    <!-- Contributor -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Contributor <span
                                class="text-red-500">*</span></label>
                        <select v-model="form.contributor_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none">
                            <option value="" disabled>Select contributor</option>
                            <option v-for="user in contributors" :key="user.id" :value="user.id">
                                {{ user.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.contributor_id" class="text-red-500 text-sm mt-1">{{
                            form.errors.contributor_id }}</p>
                    </div>

                    <!-- Embedded Towns -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Featured Towns</label>
                        <Multiselect v-model="form.town_ids" :options="towns" :multiple="true" :close-on-select="false"
                            track-by="id" label="name" placeholder="Select towns" />
                    </div>

                    <!-- Embedded Experiences -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Featured Experiences</label>
                        <Multiselect v-model="form.experience_ids" :options="experiences" :multiple="true"
                            :close-on-select="false" track-by="id" label="name" placeholder="Select Experiences" />
                    </div>

                    <!-- Embedded Tour Tiles -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Tour Tiles</label>
                        <Multiselect v-model="form.tour_tile_ids" :options="tourTiles" :multiple="true"
                            :close-on-select="false" track-by="id" label="title" placeholder="Select Tour Tiles" />
                    </div>

                    <!-- Embedded Tags -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Tags</label>
                        <Multiselect v-model="form.tag_ids" :options="tags" :multiple="true" :close-on-select="false"
                            track-by="id" label="name" placeholder="Select tags" />
                    </div>

                    <!-- Hero Image -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image size:
                            738×500px (width × height)<span class="text-red-500">*</span></label>
                        <input type="file" @change="handleHero" />
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

                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Content <span
                                class="text-red-500">*</span></label>
                        <EditorBlock v-model="form.content" />
                        <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</p>
                    </div>
                </div>

                <!-- RIGHT COLUMN - CONTENT + SEO -->
                <div class="space-y-6">
                    <!-- SEO Title -->
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
                    </div>

                    <!-- Published toggle -->
                    <div class="flex items-center space-x-3">
                        <input v-model="form.is_published" type="checkbox" id="is_published"
                            class="form-checkbox h-5 w-5 text-emerald-500" />
                        <label for="is_published" class="text-sm text-blueGray-700">Publish now</label>
                    </div>
                    <input ref="imageInput" type="file" class="hidden" @change="insertImage" accept="image/*" />
                </div>

                <!-- Submit Buttons -->
                <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('admin.venture-magazines.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
                    Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Publish Post
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'
import { ref, watchEffect } from 'vue'
import EditorBlock from '@/Components/EditorBlock.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
    contributors: Array,
    towns: Array,
    experiences: Array,
    tourTiles: Array,
    tags: Array,
})

const toast = useToast()

const form = useForm({
    title: '',
    contributor_id: '',
    content: null,
    hero_image: null,
    big_hero_image: null,
    is_published: false,
    seo_title: '',
    seo_description: '',
    seo_image: null,
    town_ids: [],
    experience_ids: [],
    tour_tile_ids: [],
    tag_ids: []
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
    form.transform((data) => ({
        ...data,
        town_ids: data.town_ids.map(t => t.id),
        experience_ids: data.experience_ids.map(e => e.id),
        tour_tile_ids: data.tour_tile_ids.map(t => t.id),
    })).post(route('admin.venture-magazines.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Magazine post created successfully!'),
        onError: () => toast.error('Failed to create magazine post'),
    })
}

</script>