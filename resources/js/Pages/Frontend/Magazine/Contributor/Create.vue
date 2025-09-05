<template>
    <ContributorLayout>

        <Head title="Add Magazine" />
        <section class="pb-20">
            <div class="w-full bg-white p-8 my-8 rounded-xl shadow-xl">
                <div class="mb-6 border-b pb-4">
                    <h2 class="text-2xl font-semibold text-blueGray-800">Add New Magazine</h2>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Hero Image (Minimum image
                                size:
                                738×500px (width × height)<span class="text-red-500">*</span></label>
                            <input type="file" @change="handleHero" />
                            <p v-if="form.errors.hero_image" class="text-red-500 text-sm mt-1">{{ form.errors.hero_image
                            }}
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
                            <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}
                            </p>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN - CONTENT + SEO -->
                    <div class="space-y-6">
                        <!-- SEO Title -->
                        <!-- <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Title</label>
                            <input v-model="form.seo_title" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.seo_title" class="text-red-500 text-sm mt-1">{{ form.errors.seo_title
                            }}
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
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Social Share Image (Minimum image
                                size:
                                1200×600px (width × height)</label>
                            <input type="file" @change="handleSeo" />
                            <p v-if="form.errors.seo_image" class="text-red-500 text-sm mt-1">{{ form.errors.seo_image
                            }}
                            </p>
                        </div>
                        <input ref="imageInput" type="file" class="hidden" @change="insertImage" accept="image/*" />
                    </div>
                    <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4">
                        <TermsAgreement :terms="props.terms" v-model="form.agreements" class="mt-6" />
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                        <!-- Cancel -->
                        <Link :href="route('contributor.magazines.index')"
                            class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
                        Cancel
                        </Link>

                        <!-- Save as Draft -->
                        <button type="button" @click="submitMagazine('draft')" :disabled="form.processing"
                            class="bg-cyan-500 text-white hover:bg-cyan-700 font-semibold px-6 py-2 rounded-md shadow">
                            Save as Draft
                        </button>

                        <!-- Submit for Review -->
                        <button type="button" @click="submitMagazine('pending')" :disabled="form.processing"
                            class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2 rounded-md shadow">
                            Submit for Review
                        </button>
                    </div>

                </form>
            </div>
        </section>
    </ContributorLayout>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import ContributorLayout from '@/Layouts/ContributorLayout.vue'
import { useToast } from 'vue-toastification'
import { ref, watchEffect } from 'vue'
import EditorBlock from '@/Components/EditorBlock.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import TermsAgreement from '@/Components/Frontend/TermsAgreement.vue'

const props = defineProps({
    towns: { type: Array, default: () => [] },
    experiences: { type: Array, default: () => [] },
    tourTiles: { type: Array, default: () => [] },
    real_contributor_id: { type: Number, default: null },
    terms: Object
})

const toast = useToast()

const form = useForm({
    title: '',
    real_contributor_id: props.real_contributor_id,
    content: null,
    hero_image: null,
    big_hero_image: null, // remove if not supported server-side
    seo_title: '',
    seo_description: '',
    seo_image: null,
    town_ids: [],
    experience_ids: [],
    tour_tile_ids: [],
    status: 'draft',
    agreements: []
})

function handleHero(e) { form.hero_image = e.target.files?.[0] ?? null }
function handleBigHero(e) { form.big_hero_image = e.target.files?.[0] ?? null }
function handleSeo(e) { form.seo_image = e.target.files?.[0] ?? null }

function submitMagazine(status) {
    form.status = status === 'pending' ? 'pending' : 'draft'
    form
        .transform((data) => ({
            ...data,
            town_ids: (data.town_ids || []).filter(Boolean).map(t => t.id),
            experience_ids: (data.experience_ids || []).filter(Boolean).map(x => x.id),
            tour_tile_ids: (data.tour_tile_ids || []).filter(Boolean).map(t => t.id),
        }))
        .post(route('contributor.magazines.store'), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                toast.success(form.status === 'pending'
                    ? 'Submitted for review. Admin will verify it shortly.'
                    : 'Saved as draft.'
                )
            },
            onError: () => toast.error('Failed to create magazine post'),
        })
}
</script>

<!-- <script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import ContributorLayout from '@/Layouts/ContributorLayout.vue';
import { useToast } from 'vue-toastification'
import { ref, watchEffect } from 'vue'
import EditorBlock from '@/Components/EditorBlock.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
    towns: Array,
    experiences: Array,
    tourTiles: Array,
    real_contributor_id: Number
})
const toast = useToast()

const form = useForm({
    title: '',
    real_contributor_id: props.real_contributor_id,
    content: null,
    hero_image: null,
    big_hero_image: null,
    seo_title: '',
    seo_description: '',
    seo_image: null,
    town_ids: [],
    experience_ids: [],
    tour_tile_ids: [],
    status: 'draft',
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

function submitMagazine(status) {
    const nextStatus = status === 'pending' ? 'pending' : 'draft';
    form.status = nextStatus;
    form.transform((data) => ({
        ...data,
        town_ids: data.town_ids.map(t => t.id),
        experience_ids: data.experience_ids.map(e => e.id),
        tour_tile_ids: data.tour_tile_ids.map(t => t.id),
    })).post(route('contributor.magazines.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            if (nextStatus === 'pending') {
                toast.success('Submitted for review. Admin will verify it shortly.');
            } else {
                toast.success('Saved as draft.');
            }
        },
        onError: () => toast.error('Failed to create magazine post'),
    })
}

</script> -->