<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import EditorJSHTML from 'editorjs-html'
import { ref, computed } from 'vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'
import dayjs from 'dayjs'
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


const props = defineProps({
    tour: {
        type: Object,
        required: true,
        default: () => ({})
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

const renderedOpening = computed(() => {
    if (!props.tour.opening_times) return ''
    try {
        const json = typeof props.tour.opening_times === 'string'
            ? JSON.parse(props.tour.opening_times)
            : props.tour.opening_times

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
    if (props.tour.video) availableTabs.push('video')
    if (props.tour.location) availableTabs.push('map')
    if (props.tour.custom_fields) availableTabs.push('information')
    return availableTabs
})

// Set first available tab as default
const activeTab = ref(tabs.value[0])

const cleanIframe = computed(() => {
    if (!props.tour.location) return null

    return props.tour.location
        .replace(/width="\d+"/, 'width="100%"')
        .replace(/height="\d+"/, 'height="500px"')
})

const cleanVideoIframe = computed(() => {
    if (!props.tour.video) return null

    return props.tour.video
        .replace(/width="\d+"/, 'width="100%"')
        .replace(/height="\d+"/, 'height="500px"')
})
const googleMapsUrl = computed(() => {
    if (!props.tour.address) return null
    const baseUrl = 'https://www.google.com/maps/search/?api=1&query='
    return baseUrl + encodeURIComponent(props.tour.address)
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

</script>

<template>
    <Layout>

        <Head :title="tour.name" />
        <SeoMeta :title="`Explore ${tour.name} - Venture Up North`" :description="tour.summary"
            :image="tour.seo_image" :canonical="`https://venturedownsouth.com/experiences/${tour.slug}`" :index="true"
            :follow="true" />

        <!-- Hero Section -->
        <section class="relative w-full h-screen overflow-hidden text-white mb-10">
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
        <section class="container mx-auto mb-10" data-aos="fade-up">
            <div
                class="component component-text lg:w-5/6 xl:5/6 lg:mx-auto px-5 lg:px-0 mt-8 lg:mt-10 grid grid-cols-1 md:grid-cols-12 gap-4 ">
                <!-- Left: Towns + Tags -->
                <div class="space-y-6 md:col-span-3">
                    <!-- Tags -->
                    <div v-if="tour.tags?.length">
                        <h3 class="text-xl font-semibold text-bison mb-2 pb-3 w-5/6 border-b border-envy">Tags</h3>
                        <Link v-for="tag in tour.tags" :key="tag.id" :href="`#`"
                            class="px-3 py-1 tracking-wide uppercase rounded-full bg-gray-100 text-heavy text-lg block font-semibold hover:text-center hover:bg-heavy hover:text-white transition">
                        {{ tag.name }}
                        </Link>
                    </div>
                    <!-- Towns -->
                    <div v-if="tour.towns?.length">
                        <h3 class="text-xl font-semibold text-bison mb-2 pb-3 w-5/6 border-b border-envy">Towns</h3>
                        <Link v-for="town in tour.towns" :key="town.id" :href="`/town/${town.slug}`"
                            class="px-3 py-1 tracking-wide uppercase rounded-full bg-gray-100 text-heavy text-lg block font-semibold hover:text-center hover:bg-heavy hover:text-white transition">
                        {{ town.name }}
                        </Link>
                    </div>
                </div>
                <div class="space-y-6 md:col-span-6">
                    <div class="text-content text-xl text-gray-700 font-medium">
                        <p class="text-lg text-bison leading-relaxed font-bold tracking-wide">
                            {{ tour.summary }}
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
                            <div v-if="tour.custom_buttons?.length" class="flex flex-wrap gap-3 justify-center">
                                <a v-for="(field, i) in tour.custom_buttons" :key="i" :href="field.value"
                                    class="text-sm font-semibold text-white border border-white/30 px-4 py-2 rounded-full hover:border-bison hover:text-bison transition-all"
                                    target="_blank" rel="noopener noreferrer">
                                    {{ field.label }}
                                </a>
                            </div>


                            <!-- Address Line -->
                            <div class="flex items-start space-x-2" v-if="googleMapsUrl && tour.address">
                                <MapPinIcon class="w-7 h-7 text-bison" />
                                <a :href="googleMapsUrl" target="_blank" rel="noopener noreferrer"
                                    class="text-bison hover:text-white font-semibold  whitespace-normal">
                                    {{ tour.address }}
                                </a>
                            </div>

                            <!-- Opening Time -->
                            <div class="flex items-start space-x-2 cursor-pointer" v-if="tour.opening_times">
                                <ClockIcon class="w-5 h-5 text-bison " />
                                <p class="text-bison hover:text-white font-semibold  whitespace-normal" v-html="renderedOpening">
                                </p>
                            </div>

                            <!-- Phone Numbers -->
                            <div v-if="tour.phone_number" class="flex items-center space-x-2">
                                <PhoneIcon class="w-5 h-5 text-bison" />
                                <div class="flex flex-wrap gap-3">
                                    <a v-for="(phone, index) in tour.phone_number.split(',')" :key="index"
                                        :href="`tel:${phone.trim()}`" class="text-bison hover:text-white font-semibold  whitespace-normal">
                                        {{ phone.trim() }}
                                    </a>
                                </div>
                            </div>


                            <!-- Mail -->
                            <div v-if="tour.email" class="flex items-center space-x-2">
                                <Mail class="w-5 h-5 text-bison" />
                                <div class="flex flex-wrap gap-3">
                                    <a v-for="(email, index) in tour.email.split(',')" :key="index"
                                        :href="`mailto:${email.trim()}`"
                                        class="text-bison hover:text-white font-semibold  whitespace-normal">
                                        {{ email.trim() }}
                                    </a>
                                </div>
                            </div>
                            <div v-if="tour.social_links?.length" class="flex flex-wrap gap-3 justify-center">
                                <a v-for="(field, i) in tour.social_links" :key="i" :href="normalizeUrl(field.value)"
                                    class="text-sm font-semibold text-white border border-white/30 px-4 py-2 rounded-full hover:border-bison hover:text-bison transition-all"
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
                    <div v-if="tour.custom_fields?.length"
                        class="bg-heavy/80 text-white p-6 rounded-xl shadow-xl border border-white/10 backdrop-blur-md">
                        <div v-for="(field, i) in tour.custom_fields" :key="i"
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
