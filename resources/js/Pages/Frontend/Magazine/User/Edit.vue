<template>
    <AuthenticatedLayout>

        <Head :title="`Edit: ${form.title}`" />
        <section class="pb-20">
            <div class="w-full bg-white p-8 my-8 rounded-xl shadow-xl">
                <div class="mb-6 border-b pb-4">
                    <h2 class="text-2xl font-semibold text-blueGray-800">Edit Magazine Post ({{ form.title }})</h2>
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

                        <!-- Embedded Towns -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Featured Towns</label>
                            <Multiselect v-model="form.town_ids" :options="towns" :multiple="true"
                                :close-on-select="false" track-by="id" label="name" placeholder="Select towns" />
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

                        <!-- Hero Image -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image <span
                                    class="text-red-500">*</span></label>
                            <input type="file" @change="handleHero" />
                            <p v-if="form.errors.hero_image" class="text-red-500 text-sm mt-1">{{ form.errors.hero_image
                            }}
                            </p>
                            <div v-if="magazine.hero_image" class="mt-2">
                                <img :src="`/public/storage/${magazine.hero_image}`" class="h-24 rounded shadow" />
                            </div>
                        </div>


                        <!--Big Hero Image -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Big Hero Image</label>
                            <input type="file" @change="handleBigHero" />
                            <p v-if="form.errors.big_hero_image" class="text-red-500 text-sm mt-1">{{
                                form.errors.big_hero_image }}
                            </p>
                            <div v-if="magazine.big_hero_image" class="mt-2">
                                <img :src="`/public/storage/${magazine.big_hero_image}`" class="h-24 rounded shadow" />
                            </div>
                        </div>
                        <!-- Content -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Content <span
                                    class="text-red-500">*</span></label>
                            <EditorBlock v-model="editorContent" ref="editorRef" />
                            <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}
                            </p>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN - SEO -->
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
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Image</label>
                            <input type="file" @change="handleSeo" />
                            <p v-if="form.errors.seo_image" class="text-red-500 text-sm mt-1">{{ form.errors.seo_image
                            }}
                            </p>
                            <div v-if="magazine.seo_image" class="mt-2">
                                <img :src="`/public/storage/${magazine.seo_image}`" class="h-24 rounded shadow" />
                            </div>
                        </div>

                        <!-- Published -->
                        <div class="flex items-center space-x-3">
                            <input v-model="form.is_published" type="checkbox" id="is_published"
                                class="form-checkbox h-5 w-5 text-emerald-500" />
                            <label for="is_published" class="text-sm text-blueGray-700">Publish now</label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                        <Link :href="route('user.magazines.index')"
                            class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
                        Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                            Update Post
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue'
import EditorBlock from '@/Components/EditorBlock.vue'
import { useToast } from 'vue-toastification'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
    magazine: Object,
    towns: Array,
    experiences: Array,
    tourTiles: Array,
})

const toast = useToast()

const editorContent = ref(JSON.parse(props.magazine.content))

const editorRef = ref(null)

const form = useForm({
    title: props.magazine.title,
    contributor_id: props.magazine.contributor_id,
    content: props.magazine.content,
    hero_image: null,
    big_hero_image: null,
    seo_title: props.magazine.seo_title,
    seo_description: props.magazine.seo_description,
    seo_image: null,
    is_published: Boolean(props.magazine.is_published),
    town_ids: props.magazine.town_ids || [],
    experience_ids: props.magazine.experience_ids || [],
    tour_tile_ids: props.magazine.tour_tile_ids || [],
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
    const content = await editorRef.value.getContent()
    if (!content || !content.blocks?.length) {
        toast.error('Content is empty')
        return
    }

    form.content = content

    form.post(route('user.magazines.update', props.magazine.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Magazine post updated successfully!'),
        onError: () => toast.error('Failed to update magazine post'),
    })
}

</script>