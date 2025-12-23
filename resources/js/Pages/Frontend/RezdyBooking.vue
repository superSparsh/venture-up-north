<script setup>
import { onMounted, ref, computed } from 'vue'
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
    location: String,
    address: String,
    opening_times: [String, Array, Object],
    email: String,
    phone_number: String,
    video: String,
    custom_fields: Array,
    social_links: Array
})

const iframeRef = ref(null)

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

onMounted(() => {
    // Load pluginJs manually if not already loaded
    if (!document.querySelector('script[src*="pluginJs"]')) {
        const script = document.createElement('script')
        script.src = 'https://ventureupnorth.rezdy.com/pluginJs'
        script.defer = true
        document.body.appendChild(script)

        // Wait for script to load and then trigger resizing
        script.onload = () => {
            if (window.Rezdy && typeof window.Rezdy.resizeIframes === 'function') {
                window.Rezdy.resizeIframes()
            }
        }
    } else {
        // If already loaded, resize directly
        setTimeout(() => {
            if (window.Rezdy && typeof window.Rezdy.resizeIframes === 'function') {
                window.Rezdy.resizeIframes()
            }
        }, 500)
    }

    // Watch for iframe load
    iframeRef.value?.addEventListener('load', () => {
        loading.value = false
    })
})

const loading = ref(true)
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
                <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                    <div class="container mx-auto px-4">
                        <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                            {{ title }}
                        </h1>

                        <!-- Action Card -->
                        <div v-if="affiliates === 'fareharbour'"
                            class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100 text-center mt-10 lg:w-1/3 mx-auto">
                            <h3 class="text-xl font-bold text-heavy mb-4">Ready to Venture</h3>

                            <a :href="rezdy_url"
                                class="block w-full bg-bison hover:bg-heavy hover:text-bison text-heavy font-bold py-4 px-6 rounded-xl shadow-md transition duration-300 transform hover:-translate-y-1">
                                Check Details & Availability
                            </a>
                            <p class="mt-3 text-xs text-gray">*Opens secure booking window</p>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Rezdy Widget -->
            <section v-if="affiliates !== 'fareharbour'" class="container mx-auto px-4 mb-10" data-aos="fade-up">
                <div
                    class="component component-text component-text-introduction lg:w-3/4 xl:w-3/4 lg:mx-auto px-5 lg:px-0">
                    <div class="text-content mt-8 lg:mt-10 text-xl text-gray-700 font-medium">
                        <h2 class="text-2xl md:text-3xl font-bold text-left text-heavy">
                            Secure Your Spot – Book Now
                        </h2>
                    </div>
                </div>
            </section>
        </div>
        <div v-if="loading && affiliates !== 'fareharbour'"
            class="flex justify-center items-center h-[400px] bg-[#C3BBA4]">
            <span class="text-heavy font-semibold text-lg animate-pulse">Loading booking options...</span>
        </div>
        <iframe v-if="affiliates !== 'fareharbour'" ref="iframeRef" v-show="!loading" class="rezdy w-full border-none"
            style="height: 1000px; background-color: #C3BBA4 !important;" frameborder="0"
            :src="rezdy_url + '?iframe=true'" scrolling="no"></iframe>
    </Layout>
</template>
