<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'
import EditorJSHTML from 'editorjs-html'

const props = defineProps({
    title: String,
    rezdy_url: String,
    big_hero_image: String,
    seo_title: String,
    seo_description: String,
    seo_image: String,
    slug: String,
    base_path: String,
    affiliates: {
        type: String,
        default: 'rezdy'
    },
    // New props for FareHarbour content
    summary: String,
    content: [String, Object], // Could be JSON from editor
    price: String, // Price information
    location: String,
    address: String,
    opening_times: [String, Array, Object],
    email: String,
    phone_number: String,
    video: String,
    custom_fields: Array,
    social_links: Array
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
    if (!props.content) return ''
    try {
        const json = typeof props.content === 'string'
            ? JSON.parse(props.content)
            : props.content

        const parsed = edjsParser.parse(json)

        return Array.isArray(parsed) ? parsed.join('') : parsed
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})

// Extract price from custom_fields array
const displayPrice = computed(() => {
    if (!props.custom_fields || !Array.isArray(props.custom_fields)) return null

    const priceField = props.custom_fields.find(field =>
        field.label && field.label.toLowerCase().includes('price')
    )

    return priceField ? priceField.value : null
})

</script>

<style scoped>
.min-h-screen {
    background-color: #C3BBA4 !important;
}
</style>
<template>
    <Layout>

        <Head :title="'Book - ' + title" />
        <SeoMeta :title="`Explore ${props.seo_title} - Venture Up North`" :description="props.seo_description"
            :image="props.seo_image" :canonical="`https://ventureupnorth.com/${props.base_path}/${props.slug}`"
            :index="true" :follow="true" />

        <div class="min-h-screen flex flex-col bg-white text-heavy">
            <!-- Header -->

            <!-- Hero Section -->
            <section class="relative w-full h-screen overflow-hidden text-white">
                <!-- Background Image -->
                <img :src="big_hero_image" :alt="title" class="absolute inset-0 w-full h-full object-cover z-0" />

                <!-- Dark overlay (optional for better text readability) -->
                <div class="absolute inset-0 bg-black/40 z-10"></div>

                <!-- Content Wrapper -->
                <div class="relative z-20 h-full flex items-center py-32" data-aos="fade-up">
                    <div class="container mx-auto px-4">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                            <!-- Left: Title and Description -->
                            <div class="space-y-6">
                                <h1
                                    class="text-white text-2xl md:text-6xl font-extrabold tracking-widest uppercase mt-[10rem] md:mt-0">
                                    Treasures of Broome | Pearlmasters Choice Tour
                                </h1>

                                <!-- Description with Glassmorphism and Price -->
                                <div v-if="renderedDescription || displayPrice"
                                    class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl p-6 shadow-2xl max-h-[500px] overflow-y-auto">

                                    <!-- Price at the top -->
                                    <div v-if="displayPrice" class="mb-4 pb-4 border-b border-white/30">
                                        <p class="text-bison text-2xl md:text-3xl font-bold">{{ displayPrice }}</p>
                                    </div>

                                    <!-- Description -->
                                    <article v-if="renderedDescription"
                                        class="text-white text-base md:text-lg leading-relaxed"
                                        v-html="renderedDescription">
                                    </article>
                                </div>
                            </div>

                            <!-- Right: Booking Card with Glassmorphism -->
                            <div class="flex justify-center lg:justify-end">
                                <div
                                    class="backdrop-blur-md bg-white/90 p-8 rounded-2xl shadow-2xl border border-white/30 text-center w-full max-w-md">
                                    <h3 class="text-xl font-bold text-heavy mb-6">Ready to Venture</h3>

                                    <a :href="rezdy_url" target="_blank" rel="noopener noreferrer"
                                        class="block w-full bg-bison hover:bg-heavy hover:text-bison text-heavy font-bold py-4 px-6 rounded-xl shadow-md transition duration-300 transform hover:-translate-y-1">
                                        Check Availability & Book
                                    </a>
                                    <p class="mt-3 text-xs text-gray">*Opens secure booking window</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </Layout>
</template>
