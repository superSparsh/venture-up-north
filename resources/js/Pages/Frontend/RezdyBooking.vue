<script setup>
import { onMounted, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'

const props = defineProps({
    title: String,
    rezdy_url: String,
    big_hero_image: String,
    seo_title: String,
    seo_description: String,
    seo_image: String,
    slug: String,
    base_path: String,
})

const iframeRef = ref(null)

onMounted(() => {
    // Load pluginJs manually if not already loaded
    if (!document.querySelector('script[src*="pluginJs"]')) {
        const script = document.createElement('script')
        script.src = 'https://venturedownsouth.rezdy.com/pluginJs'
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
            :image="props.seo_image" :canonical="`https://venturedownsouth.com/${props.base_path}/${props.slug}`"
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
                    </div>
                </div>
            </section>
            <!-- Rezdy Widget -->
            <section class="container mx-auto px-4 mb-10" data-aos="fade-up">
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
        <div v-if="loading" class="flex justify-center items-center h-[400px] bg-[#C3BBA4]">
            <span class="text-heavy font-semibold text-lg animate-pulse">Loading booking options...</span>
        </div>
        <iframe ref="iframeRef" v-show="!loading" class="rezdy w-full border-none"
            style="height: 1000px; background-color: #C3BBA4 !important;" frameborder="0"
            :src="rezdy_url + '?iframe=true'" scrolling="no"></iframe>
    </Layout>
</template>
