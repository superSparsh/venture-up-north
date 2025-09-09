<script setup>
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import EditorJSHTML from 'editorjs-html'
import { computed } from 'vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'
import CollectionItems from '@/Components/Frontend/CollectionItems.vue'

const props = defineProps({
    collection: {
        type: Object,
        required: true,
    },
    events: {
        type: Array,
        default: () => [],
    },
    content_listings: {
        type: Array,
        default: () => [],
    },
    tour_tiles: {
        type: Array,
        default: () => [],
    },
    tags: {
        type: Array,
        default: () => [],
    },
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
    if (!props.collection.description) return ''
    try {
        const json = typeof props.collection.description === 'string'
            ? JSON.parse(props.collection.description)
            : props.collection.description

        const parsed = edjsParser.parse(json)

        return Array.isArray(parsed) ? parsed.join('') : parsed
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})
</script>

<template>
    <Layout>

        <Head :title="collection.name" />
        <SeoMeta :title="`Explore ${collection.name} - Venture Up North`" :description="collection.summary"
            :image="collection.seo_image" :canonical="`https://ventureupnorth.com/collection/${collection.slug}`"
            :index="true" :follow="true" />

        <!-- Hero Section -->
        <section class="relative w-full h-screen overflow-hidden text-white">
            <!-- Background Image -->
            <img :src="collection.big_hero_image" :alt="collection.name"
                class="absolute inset-0 w-full h-full object-cover z-0" />

            <!-- Dark overlay (optional for better text readability) -->
            <div class="absolute inset-0 bg-black/40 z-10"></div>

            <!-- Content Wrapper -->
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                        {{ collection.name }}
                    </h1>
                </div>
            </div>
        </section>
        <!-- Description Section -->
        <section class="container mx-auto px-4 mb-16" data-aos="fade-up">
            <div class="component component-text component-text-introduction lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0">
                <div class="text-content mt-8 lg:mt-10 text-xl text-gray-700 font-medium">
                    <p class="text-lg text-bison leading-relaxed max-w-3xl font-bold tracking-wide">
                        {{ collection.summary }}
                    </p>
                </div>
            </div>
            <div class="component component-text lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0 mt-8 lg:mt-10">
                <article class="prose prose-lg text-heavy text-lg font-semibold tracking-small leading-6"
                    v-html="renderedDescription">
                </article>
                <div class="mt-10 flex justify-end items-center gap-4">
                    <div>
                        <p class="text-sm text-heavy font-semibold">By</p>
                        <p class="text-md font-bold text-bison"> Admin
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <CollectionItems :events="events" :listings="content_listings" :tours="tour_tiles" :tags="tags"
            :collection="collection" />
    </Layout>
</template>
