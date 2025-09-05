<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import EditorJSHTML from 'editorjs-html'
import { computed } from 'vue'
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
console.log(props.magazine)
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

</script>

<template>
    <Layout>

        <Head :title="magazine.name" />
        <SeoMeta :title="`Explore ${magazine.name} - Venture Up North`" :description="magazine.summary"
            :image="magazine.seo_image" :canonical="`https://venturedownsouth.com/experiences/${magazine.slug}`"
            :index="true" :follow="true" :site_name="magazine.site_name" />

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
                <div v-if="magazine.contributor || magazine.real_contributor"
                    class="mt-10 flex justify-end items-center gap-4">
                    <img :src="(magazine.contributor && magazine.contributor.photo)
                        ? magazine.contributor.photo
                        : (magazine.real_contributor && magazine.real_contributor.photo)
                            ? magazine.real_contributor.photo
                            : ''" :alt="magazine.contributor?.name ||
                magazine.real_contributor?.name || '-'" class="w-14 h-14 object-cover rounded-full shadow" />
                    <div>
                        <p class="text-sm text-heavy font-semibold">Written by</p>
                        <p class="text-md font-bold text-bison"> {{ magazine.contributor?.name ||
                            magazine.real_contributor?.name || '-' }}
                        </p>
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
