<script setup>
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import EditorJSHTML from 'editorjs-html'
import { computed } from 'vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'
import FilterableTour from '@/Components/Frontend/FilterableTour.vue'

const props = defineProps({
    tour: {
        type: Object,
        required: true,
    },
    tags: Array,
    towns: Array,
    initialItems: {
        type: Object,
    }
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
    if (!props.tour.description) return ''
    try {
        const json = typeof props.tour.description === 'string'
            ? JSON.parse(props.tour.description)
            : props.tour.description

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

        <Head title="Tours" />
        <SeoMeta :title="`Explore ${tour.name} - Venture Up North`" :description="tour.summary"
            :image="tour.seo_image" :canonical="`https://venturedownsouth.com/tours`"
            :index="true" :follow="true" />

        <!-- Hero Section -->
        <section class="relative w-full h-screen overflow-hidden text-white">
            <!-- Background Image -->
            <img :src="tour.big_hero_image" :alt="tour.name"
                class="absolute inset-0 w-full h-full object-cover z-0" />

            <!-- Dark overlay (optional for better text readability) -->
            <div class="absolute inset-0 bg-black/40 z-10"></div>

            <!-- Content Wrapper -->
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                        {{ tour.name }}
                    </h1>
                </div>
            </div>
        </section>
        <!-- Description Section -->
        <section class="container mx-auto px-4 mb-16" data-aos="fade-up">
            <div class="component component-text component-text-introduction lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0">
                <div class="text-content mt-8 lg:mt-10 text-xl text-gray-700 font-medium">
                    <p class="text-lg text-bison leading-relaxed max-w-3xl font-bold tracking-wide">
                        {{ tour.summary }}
                    </p>
                </div>
            </div>
            <div class="component component-text lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0 mt-8 lg:mt-10">
                <article
                    class="prose prose-lg text-heavy text-lg font-semibold tracking-small leading-6"
                    v-html="renderedDescription">
                </article>
            </div>
        </section>
        <FilterableTour :api-route="`/api/tours`" :tags="tags" :towns="towns"
            :tour="tour" :initialItems="initialItems" />
    </Layout>
</template>
