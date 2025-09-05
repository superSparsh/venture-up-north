<script setup>
import { Head, usePage, router, Link } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import EditorJSHTML from 'editorjs-html'
import { ref, computed } from 'vue'
import ExploreOtherMagazines from '@/Components/Frontend/ExploreOtherMagazines.vue'
import TownSection from '@/Components/Frontend/VentureMagazineExtras/TownSection.vue'
import ExperienceSection from '@/Components/Frontend/VentureMagazineExtras/Experience.vue'
import TourTileSection from '@/Components/Frontend/VentureMagazineExtras/TourTileSection.vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'

const props = defineProps({
    magazine: {
        type: Object,
        required: true,
    },
    towns: {
        type: Array,
        default: () => [],
    },
    experiences: {
        type: Array,
        default: () => [],
    },
    tour_tiles: {
        type: Array,
        default: () => [],
    },
    tourClicks: Object,
    allMagazines: Object
})

const edjsParser = EditorJSHTML({
    linkTool: (block) => {
        const { link, meta } = block.data
        const title = meta?.title || link
        const description = meta?.description || ''
        const imageUrl = meta?.image?.url

        return `
      <a href="${link}" target="_blank" rel="noopener noreferrer" class="block p-4 rounded hover:shadow transition bg-white no-underline">
        <div class="text-lg font-semibold text-blue-600">${title}</div>
        ${imageUrl ? `<img src="${imageUrl}" class="mt-2 object-contain rounded" />` : ''}
      </a>
    `
    }
})

const renderedDescription = computed(() => {
    if (!props.magazine.description) return ''
    try {
        const json = typeof props.magazine.description === 'string'
            ? JSON.parse(props.magazine.description)
            : props.magazine.description

        const parsed = edjsParser.parse(json)

        return Array.isArray(parsed) ? parsed.join('') : parsed
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})

const tourClicks = computed(() => usePage().props.tourClicks || [])


// --- Approve/Reject state ---
const showReject = ref(false)
const rejectReason = ref('')

const showApprove = ref(false)
const approveNote = ref('')
// Convenience flags
const canModerate = computed(() => props.magazine.status === 'pending') // show actions only when pending

function openApprove() {
    showApprove.value = !showApprove.value
    if (showApprove.value) {
        // close the other panel if open
        showReject.value = false
    }
}

function approve() {
    router.post(
        route('admin.venture-magazines.approve', props.magazine.id),
        { note: approveNote.value || null },
        {
            preserveScroll: true,
            onSuccess: () => {
                showApprove.value = false
                approveNote.value = ''
            }
        }
    )
}

function openReject() {
    showReject.value = !showReject.value
}

function reject() {
    if (!rejectReason.value.trim()) {
        alert('Please enter a brief reason for rejection.')
        return
    }
    router.post(
        route('admin.venture-magazines.reject', props.magazine.id),
        { reason: rejectReason.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                showReject.value = false
                rejectReason.value = ''
            }
        }
    )
}
</script>

<template>
    <Layout>

        <Head :title="magazine.name" />


        <!-- Hero Section -->
        <section class="relative w-full h-screen overflow-hidden text-white">
            <!-- Background Image -->
            <img :src="magazine.big_hero_image" :alt="magazine.name"
                class="absolute inset-0 w-full h-full object-cover z-0" />

            <!-- Dark overlay (optional for better text readability) -->
            <div class="absolute inset-0 bg-black/40 z-10"></div>

            <!-- Content Wrapper -->
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                        {{ magazine.name }}
                    </h1>
                </div>
            </div>
        </section>
        <!-- Moderation action bar (visible only when pending) -->
        <div v-if="canModerate" class="sticky top-0 z-30 bg-bison backdrop-blur border-b">
            <div class="container mx-auto px-4 py-3 flex flex-wrap items-center gap-3 justify-between">
                <div class="flex items-center gap-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-amber-100 text-amber-700">
                        Pending Review
                    </span>
                    <span class="text-sm text-gray-600">
                        Review and approve to publish, or reject with a short note.
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="openApprove"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md shadow">
                        Approve & Publish
                    </button>
                    <button @click="openReject"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-md shadow">
                        Reject
                    </button>
                </div>
            </div>

            <!-- Inline approve panel -->
            <div v-if="showApprove" class="border-t bg-white">
                <div class="container mx-auto px-4 py-3 flex items-start gap-3">
                    <textarea v-model="approveNote" rows="2" class="w-full px-3 py-2 border rounded-md"
                        placeholder="Optional note to share with the contributor (e.g., small edits requested before publishing)"></textarea>
                    <button @click="approve"
                        class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md shadow">
                        Publish
                    </button>
                </div>
            </div>

            <!-- Inline reject panel -->
            <div v-if="showReject" class="border-t bg-white">
                <div class="container mx-auto px-4 py-3 flex items-start gap-3">
                    <textarea v-model="rejectReason" rows="2" class="w-full px-3 py-2 border rounded-md"
                        placeholder="Brief reason to share with the contributor (required)"></textarea>
                    <button @click="reject"
                        class="shrink-0 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-md shadow">
                        Send Rejection
                    </button>
                </div>
            </div>
        </div>
        <div v-if="tourClicks.length">
            <h2 class="text-lg font-semibold mt-8 mb-4">Tour Click Stats</h2>
            <ul class="space-y-2 text-sm text-gray-700">
                <li v-for="click in tourClicks" :key="click.event_label">
                    <strong>{{ click.event_label }}</strong>: {{ click.click_count }} clicks
                </li>
            </ul>
        </div>
        <!-- Description Section -->
        <section class="container mx-auto px-4" data-aos="fade-up">
            <div class="component component-text lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0 mt-8 lg:mt-10">
                <article
                    class="prose prose-lg text-heavy first-letter:text-6xl first-letter:leading-none first-letter:float-left first-letter:pr-2 first-letter:font-serif text-md tracking-small leading-6"
                    v-html="renderedDescription">
                </article>
                <div v-if="magazine.contributor" class="mt-10 flex justify-end items-center gap-4">
                    <img :src="magazine.contributor.photo" :alt="magazine.contributor.name"
                        class="w-14 h-14 object-cover rounded-full shadow" />
                    <div>
                        <p class="text-sm text-heavy font-semibold">Written by</p>
                        <p class="text-md font-bold text-bison">{{ magazine.contributor.name }}</p>
                    </div>
                </div>
            </div>
        </section>
        <TownSection v-if="towns?.length" :towns="towns" :magazine="magazine" />
        <ExperienceSection v-if="experiences?.length" :experiences="experiences" :magazine="magazine" />
        <TourTileSection v-if="tour_tiles?.length" :tourTiles="tour_tiles" :magazine="magazine" />
        <ExploreOtherMagazines :allMagazines="allMagazines" :currentSlug="magazine.slug" />
    </Layout>
</template>
