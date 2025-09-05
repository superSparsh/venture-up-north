<script setup>
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import EditorJSHTML from 'editorjs-html'
import { computed } from 'vue'
import ExploreOtherExperiences from '@/Components/Frontend/ExploreOtherExperiences.vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'

const props = defineProps({
    experience: {
        type: Object,
        required: true,
    },
    allExperiences: Object
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
    if (!props.experience.description) return ''
    try {
        const json = typeof props.experience.description === 'string'
            ? JSON.parse(props.experience.description)
            : props.experience.description

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

        <Head :title="experience.name" />
        <SeoMeta :title="`Explore ${experience.name} - Venture Up North`" :description="experience.summary"
            :image="experience.seo_image" :canonical="`https://venturedownsouth.com/experiences/${experience.slug}`" :index="true"
            :follow="true" />

        <!-- Hero Section -->
        <section class="relative w-full h-screen overflow-hidden text-white">
            <!-- Background Image -->
            <img :src="experience.big_hero_image" :alt="experience.name" class="absolute inset-0 w-full h-full object-cover z-0" />

            <!-- Dark overlay (optional for better text readability) -->
            <div class="absolute inset-0 bg-black/40 z-10"></div>

            <!-- Content Wrapper -->
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                        {{ experience.name }}
                    </h1>
                </div>
            </div>
        </section>
        <!-- Description Section -->
        <section class="container mx-auto px-4" data-aos="fade-up">
            <div class="component component-text lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0 mt-8 lg:mt-10">
                <article
                    class="prose prose-lg text-heavy first-letter:text-6xl first-letter:leading-none first-letter:float-left first-letter:pr-2 first-letter:font-serif text-md tracking-small leading-6"
                    v-html="renderedDescription">
                </article>
            </div>
        </section>
        <ExploreOtherExperiences :allExperiences="allExperiences" :currentSlug="experience.slug" />
    </Layout>
</template>
