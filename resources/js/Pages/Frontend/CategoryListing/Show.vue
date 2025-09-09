<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import EditorJSHTML from 'editorjs-html'
import { ref, computed } from 'vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'
import {
    MapPinIcon,
    PhoneIcon,
    ClockIcon,
    Mail,
    Facebook,
    Instagram,
    Youtube,
    Twitter,
    Globe
} from 'lucide-vue-next'
import { ShareIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    categoryListing: {
        type: Object,
        required: true,
    },
    category: {
        type: Object,
        required: true,
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
    if (!props.categoryListing.description) return ''
    try {
        const json = typeof props.categoryListing.description === 'string'
            ? JSON.parse(props.categoryListing.description)
            : props.categoryListing.description

        const parsed = edjsParser.parse(json)

        return Array.isArray(parsed) ? parsed.join('') : parsed
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})

const renderedOpening = computed(() => {
    if (!props.categoryListing.opening_times) return ''
    try {
        const json = typeof props.categoryListing.opening_times === 'string'
            ? JSON.parse(props.categoryListing.opening_times)
            : props.categoryListing.opening_times

        const parsed = edjsParser.parse(json)

        return Array.isArray(parsed) ? parsed.join('') : parsed
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})


function formatTabLabel(tab) {
    const labels = {
        video: 'Video',
        map: 'Map',
        information: 'Other Information',
    }
    return labels[tab] || tab
}

// Dynamic tab items based on availability
const tabs = computed(() => {
    const availableTabs = []
    if (props.categoryListing.video) availableTabs.push('video')
    if (props.categoryListing.location) availableTabs.push('map')
    if (props.categoryListing.custom_fields) availableTabs.push('information')
    return availableTabs
})

// Set first available tab as default
const activeTab = ref(tabs.value[0])

const cleanIframe = computed(() => {
    if (!props.categoryListing.location) return null

    return props.categoryListing.location
        .replace(/width="\d+"/, 'width="100%"')
        .replace(/height="\d+"/, 'height="500px"')
})

const cleanVideoIframe = computed(() => {
    if (!props.categoryListing.video) return null

    return props.categoryListing.video
        .replace(/width="\d+"/, 'width="100%"')
        .replace(/height="\d+"/, 'height="500px"')
})
const googleMapsUrl = computed(() => {
    if (!props.categoryListing.address) return null
    const baseUrl = 'https://www.google.com/maps/search/?api=1&query='
    return baseUrl + encodeURIComponent(props.categoryListing.address)
})

// Mapping label to icon component
const iconMap = {
    facebook: Facebook,
    instagram: Instagram,
    youtube: Youtube,
    twitter: Twitter,
    website: Globe,
}

const getSocialIcon = (label) => {
    const key = label.toLowerCase()
    return iconMap[key] ?? Globe // fallback to Globe if unknown
}

const normalizeUrl = (url) => {
    if (!url) return '#';
    return url.startsWith('http://') || url.startsWith('https://')
        ? url
        : `https://${url}`;
};

//Share URL
const iconClass = 'w-5 h-5 mt-1 text-bison'
const shareAbs = (u) => new URL(u, window.location.origin).toString()
const shareUrl = computed(() =>
  shareAbs(`/explore/${props.category.slug}/${props.categoryListing.slug}`)
)

const enc = s => encodeURIComponent(s || '')
const facebookUrl = computed(() =>
  `https://www.facebook.com/sharer/sharer.php?u=${enc(shareUrl.value)}`
)
const twitterUrl = computed(() =>
  `https://twitter.com/intent/tweet?url=${enc(shareUrl.value)}&text=${enc(props.categoryListing.name)}`
)
const linkedinUrl = computed(() =>
  `https://www.linkedin.com/sharing/share-offsite/?url=${enc(shareUrl.value)}`
)
const whatsappUrl = computed(() =>
  `https://api.whatsapp.com/send?text=${encodeURIComponent(shareUrl.value)}`
)

const canNativeShare = typeof navigator !== 'undefined' && typeof navigator.share === 'function'
const nativeShare = async () => {
  try {
    await navigator.share({
      title: props.categoryListing.name,
      text: (props.categoryListing.summary || '').slice(0, 140),
      url: shareUrl.value
    })
  } catch {}
}
</script>
<style scoped>
.share-pill{
  @apply inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-bison text-white;
}
</style>

<template>
    <Layout>

        <Head :title="categoryListing.name" />
        <SeoMeta :title="`Explore ${categoryListing.name} - Venture Up North`" :description="categoryListing.summary"
            :image="categoryListing.seo_image"
            :canonical="`https://ventureupnorth.com/experiences/${categoryListing.slug}`" :index="true"
            :follow="true" />

        <!-- Hero Section -->
        <section class="relative w-full h-screen overflow-hidden text-white mb-10">
            <!-- Background Image -->
            <img :src="categoryListing.big_hero_image" :alt="categoryListing.name"
                class="absolute inset-0 w-full h-full object-cover z-0" />

            <!-- Dark overlay (optional for better text readability) -->
            <div class="absolute inset-0 bg-black/40 z-10"></div>

            <!-- Content Wrapper -->
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                        {{ categoryListing.name }}
                    </h1>
                </div>
            </div>
        </section>
        <!-- Description Section -->
        <section class="container mx-auto mb-10" data-aos="fade-up">
            <div
                class="component component-text lg:w-5/6 xl:5/6 lg:mx-auto px-5 lg:px-0 mt-8 lg:mt-10 grid grid-cols-1 md:grid-cols-12 gap-4 ">
                <!-- Left: Towns + Tags -->
                <div class="space-y-6 md:col-span-3">
                    <!-- Tags -->
                    <div v-if="categoryListing.tags?.length">
                        <h3 class="text-xl font-semibold text-bison mb-2 pb-3 w-5/6 border-b border-envy">Tags</h3>
                        <Link v-for="tag in categoryListing.tags" :key="tag.id" :href="`#`"
                            class="px-3 py-1 tracking-wide uppercase rounded-full bg-gray-100 text-heavy text-lg block font-semibold hover:text-center hover:bg-heavy hover:text-white transition">
                        {{ tag.name }}
                        </Link>
                    </div>
                    <!-- Towns -->
                    <div v-if="categoryListing.towns?.length">
                        <h3 class="text-xl font-semibold text-bison mb-2 pb-3 w-5/6 border-b border-envy">Towns</h3>
                        <Link v-for="town in categoryListing.towns" :key="town.id" :href="`/town/${town.slug}`"
                            class="px-3 py-1 tracking-wide uppercase rounded-full bg-gray-100 text-heavy text-lg block font-semibold hover:text-center hover:bg-heavy hover:text-white transition">
                        {{ town.name }}
                        </Link>
                    </div>

                    <!-- Events -->
                    <div v-if="categoryListing.events?.length">
                        <h3 class="text-xl font-semibold text-bison mb-2 pb-3 w-5/6 border-b border-envy">Events</h3>
                        <Link v-for="event in categoryListing.events" :key="event.id" :href="`/event/${event.slug}`"
                            class="px-3 py-1 tracking-wide uppercase rounded-full bg-gray-100 text-heavy text-lg block font-semibold hover:text-center hover:bg-heavy hover:text-white transition">
                        {{ event.name }}
                        </Link>
                    </div>
                </div>
                <div class="space-y-6 md:col-span-6">
                    <div class="text-content text-xl text-gray-700 font-medium">
                        <p class="text-lg text-bison leading-relaxed font-bold tracking-wide">
                            {{ categoryListing.summary }}
                        </p>
                    </div>
                    <article class="prose prose-lg text-heavy font-bold text-lg tracking-small leading-6 antialiased"
                        v-html="renderedDescription">
                    </article>
                </div>
                <div class="space-y-6 md:col-span-3">
                    <div class="lg:col-span-2">
                        <div
                            class="bg-heavy/80 text-white p-6 rounded-xl shadow-xl border border-white/10 backdrop-blur-md space-y-4">
                            <!-- Buttons -->
                            <div v-if="categoryListing.custom_buttons?.length" class="w-full flex justify-center">
                                <a v-for="(field, i) in categoryListing.custom_buttons" :key="i" :href="field.value"
                                    class="block tracking-wide w-full max-w-md text-center text-lg font-bold text-white border border-white px-6 py-3 rounded hover:border-bison hover:text-bison transition-all"
                                    target="_blank" rel="noopener noreferrer">
                                    {{ field.label }}
                                </a>
                            </div>
                            <!-- Share row -->
                            <div class="flex items-center justify-between gap-3 border-b border-white/10 pb-3 mb-4">
                                <span class="text-sm uppercase tracking-widest text-white/70">Share</span>

                                <div class="flex items-center gap-2">
                                    <!-- Native (mobile) -->
                                    <button v-if="canNativeShare" @click.stop="nativeShare"
                                        class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/20 text-white text-xs font-semibold"
                                        aria-label="Share">
                                        <ShareIcon class="w-4 h-4" />
                                        Share
                                    </button>

                                    <!-- Social icons -->
                                    <a :href="facebookUrl" target="_blank" rel="noopener" class="share-pill"
                                        aria-label="Share on Facebook">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M22 12a10 10 0 1 0-11.5 9.87v-7h-2v-2.87h2V9.5c0-2 1.2-3.12 3-3.12.86 0 1.76.15 1.76.15v2h-1c-1 0-1.31.62-1.31 1.26v1.34h2.23L15 14.87h-2.1v7A10 10 0 0 0 22 12Z" />
                                        </svg>
                                    </a>
                                    <a :href="twitterUrl" target="_blank" rel="noopener" class="share-pill"
                                        aria-label="Share on X">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.39l-5.22-6.828-5.976 6.828H1.886l7.73-8.834L1.5 2.25h6.913l4.713 6.216 5.118-6.216z" />
                                        </svg>
                                    </a>
                                    <a :href="linkedinUrl" target="_blank" rel="noopener" class="share-pill"
                                        aria-label="Share on LinkedIn">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M19 0h-14c-2.76 0-5 2.24-5 5v14c0 2.762 2.24 5 5 5h14c2.762 0 5-2.238 5-5v-14c0-2.76-2.238-5-5-5zM8.338 18.338h-2.676v-8.676h2.676v8.676zM7 8.162c-.858 0-1.55-.692-1.55-1.55s.692-1.55 1.55-1.55 1.55.692 1.55 1.55-.692 1.55-1.55 1.55zM18.338 18.338h-2.676v-4.282c0-1.021-.021-2.336-1.424-2.336-1.426 0-1.644 1.113-1.644 2.263v4.355h-2.676v-8.676h2.571v1.188h.036c.358-.677 1.232-1.39 2.537-1.39 2.711 0 3.215 1.785 3.215 4.104v4.774z" />
                                        </svg>
                                    </a>
                                    <a :href="whatsappUrl" target="_blank" rel="noopener" class="share-pill"
                                        aria-label="Share on WhatsApp">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.62-6.003C.122 5.29 5.485 0 12.057 0c3.18 0 6.167 1.24 8.413 3.488a11.8 11.8 0 0 1 3.484 8.414c-.003 6.572-5.293 11.935-11.867 11.935a11.9 11.9 0 0 1-6.005-1.616L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.593 5.448 0 9.886-4.434 9.889-9.877.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.435-9.889 9.884a9.85 9.85 0 0 0 1.713 5.574l-.999 3.648 3.775-.93zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.03-.967-.272-.099-.47-.149-.669.149-.198.297-.767.967-.94 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.611-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.007-1.413.248-.694.248-1.289.173-1.413z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Address Line -->
                            <div class="flex items-start space-x-2" v-if="googleMapsUrl && categoryListing.address">
                                <div class="flex-shrink-0">
                                    <MapPinIcon :class="iconClass" />
                                </div>
                                <a :href="googleMapsUrl" target="_blank" rel="noopener noreferrer"
                                    class="text-bison hover:text-white font-semibold  whitespace-normal">
                                    {{ categoryListing.address }}
                                </a>
                            </div>

                            <!-- Opening Time -->
                            <div class="flex items-start space-x-2 cursor-pointer" v-if="categoryListing.opening_times">
                                <div class="flex-shrink-0">
                                    <ClockIcon :class="iconClass" />
                                </div>
                                <p class="text-bison hover:text-white font-semibold  whitespace-normal"
                                    v-html="renderedOpening">
                                </p>
                            </div>

                            <!-- Phone Numbers -->
                            <div v-if="categoryListing.phone_number" class="flex items-center space-x-2">
                                <PhoneIcon :class="iconClass" />
                                <div class="flex flex-wrap gap-3">
                                    <a v-for="(phone, index) in categoryListing.phone_number.split(',')" :key="index"
                                        :href="`tel:${phone.trim()}`"
                                        class="text-bison hover:text-white font-semibold  whitespace-normal">
                                        {{ phone.trim() }}
                                    </a>
                                </div>
                            </div>


                            <!-- Mail -->
                            <div v-if="categoryListing.email" class="flex items-center space-x-2">
                                <div class="flex-shrink-0">
                                    <Mail :class="iconClass" />
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <a v-for="(email, index) in categoryListing.email.split(',')" :key="index"
                                        :href="`mailto:${email.trim()}`"
                                        class="text-bison hover:text-white font-semibold  whitespace-normal">
                                        {{ email.trim() }}
                                    </a>
                                </div>
                            </div>

                            <div v-if="categoryListing.social_links?.length"
                                class="flex flex-wrap gap-3 justify-center">
                                <a v-for="(field, i) in categoryListing.social_links" :key="i"
                                    :href="normalizeUrl(field.value)"
                                    class="text-sm font-semibold text-white border border-white/30 px-4 py-2 rounded-full hover:border-bison hover:text-bison transition-all  whitespace-normal"
                                    target="_blank" rel="noopener noreferrer">
                                    <component :is="getSocialIcon(field.label)" class="w-6 h-6" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Dynamic Tabs Navigation -->
            <div v-if="tabs.length" class="mt-10 mb-6">
                <div class="flex flex-wrap gap-2 border-b border-bison">
                    <button v-for="tab in tabs" :key="tab" @click="activeTab = tab"
                        class="px-4 py-2 text-sm font-bold uppercase tracking-wide transition duration-300" :class="activeTab === tab
                            ? 'border-b-2 border-bison text-bison'
                            : 'text-gray-400 hover:text-bison'">
                        {{ formatTabLabel(tab) }}
                    </button>
                </div>
            </div>


            <!-- Tabs Content -->
            <div v-if="activeTab === 'video'" class="mt-6">
                <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow-lg">
                    <div class="embed-wrapper w-full h-full" v-html="cleanVideoIframe"></div>
                </div>
            </div>

            <div v-if="activeTab === 'map'" class="mt-6">
                <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow-lg">
                    <div class="embed-wrapper w-full h-full" v-html="cleanIframe"></div>
                </div>
            </div>

            <div v-if="activeTab === 'information'" class="mt-6">
                <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow-lg">
                    <div v-if="categoryListing.custom_fields?.length"
                        class="bg-heavy/80 text-white p-6 rounded-xl shadow-xl border border-white/10 backdrop-blur-md">
                        <div v-for="(field, i) in categoryListing.custom_fields" :key="i"
                            class="flex flex-col sm:flex-row sm:items-start sm:space-x-4 py-3 px-2 mb-3 rounded-md justify-content-center items-center"
                            :class="i % 2 === 0 ? 'bg-white/5' : 'bg-white/10'">
                            <span
                                class="uppercase tracking-wide text-md font-semibold text-bison/70 sm:text-right w-[150px]">
                                {{ field.label }}:
                            </span>

                            <span class="text-white text-md font-medium truncate sm:whitespace-normal"
                                :title="field.value">
                                {{ field.value }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </Layout>
</template>
