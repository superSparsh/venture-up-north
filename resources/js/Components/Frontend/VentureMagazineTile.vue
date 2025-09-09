<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import SliderWrapper from '@/Components/Frontend/SliderWrapper.vue'
import { Link } from '@inertiajs/vue3'
import { truncateWords } from '@/utils/text'
import TileShare from './TileShare.vue';
import EditorJSHTML from 'editorjs-html'

const props = defineProps({
    magazines: {
        type: Array,
    },
    magazinePopup: { type: String, default: '' },
})

const slugify = (s) =>
    (s || '')
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

const showModal = ref(false)
function openModal() {
    showModal.value = true
    document.documentElement.classList.add('overflow-hidden') // lock scroll
}
function closeModal() {
    showModal.value = false
    document.documentElement.classList.remove('overflow-hidden')
}

function onKeydown(e) {
    if (e.key === 'Escape') closeModal()
}

onMounted(() => window.addEventListener('keydown', onKeydown))
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown))

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
    if (!props.magazinePopup) return ''
    try {
        const json = typeof props.magazinePopup === 'string'
            ? JSON.parse(props.magazinePopup)
            : props.magazinePopup

        const parsed = edjsParser.parse(json)

        return Array.isArray(parsed) ? parsed.join('') : parsed
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})
</script>

<template>
    <section class="container mx-auto px-4 py-16">
        <div class="flex flex-col items-center text-center gap-4
           md:flex-row md:items-center md:justify-between md:text-left md:gap-6 mb-10">
            <!-- Heading -->
            <h2 class="text-3xl sm:text-4xl font-bold">
                Venture Magazine
            </h2>

            <!-- Button -->
            <button type="button" @click="openModal" class="inline-flex items-center justify-center rounded-full
             px-4 sm:px-6 py-2.5
             text-base sm:text-lg md:text-xl
             bg-bison text-heavy font-semibold shadow
             hover:bg-heavy  hover:text-bison transition
             md:ml-4">
                Submit Magazine Article
            </button>
        </div>

        <SliderWrapper :items="magazines" :per-page="3" gap="1.5rem">
            <template #default="{ item }">
                <div class="relative group overflow-hidden rounded-xl shadow-lg mb-10 bg-black/30 hover:bg-black/50 transition duration-500"
                    data-aos="fade-up">
                    <!-- Image -->
                    <img :src="item.hero_image" :alt="item.title"
                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />

                    <TileShare :url="`https://ventureupnorth.com.au/magazine/${item.slug}`" :title="item.title"
                        :text="truncateWords(item.seo_description, 20)" />

                    <!-- ✅ Tags block (with subtle blur and translucent background) -->
                    <div v-if="item.tags?.length" class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
                        <Link v-for="tag in item.tags" :key="tag.id" :href="`/tours#${tag.slug || slugify(tag.name)}`"
                            class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
                        {{ tag.name }}
                        </Link>
                    </div>

                    <!-- Dark overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 to-black/10 transition-opacity opacity-0 group-hover:opacity-100 duration-300">
                    </div>

                    <!-- Content on hover -->
                    <div class="absolute inset-0 flex flex-col justify-center items-center text-white px-4 group">
                        <!-- ✨ Name (centered by default, moves up on hover) -->
                        <h2
                            class="text-2xl md:text-3xl font-extrabold tracking-widest transition-all duration-300 transform group-hover:-translate-y-10">
                            {{ item.title }}
                        </h2>

                        <!-- ✨ Hover Buttons -->
                        <div
                            class="absolute bottom-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col gap-3 w-full px-6">
                            <Link :href="`/magazine/${item.slug}`"
                                class="bg-envy text-heavy font-semibold py-2 rounded-full text-sm text-center shadow hover:bg-bison hover:text-heavy transition">
                            Read More<span class="sr-only"> About {{ item.title }}</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </template>
        </SliderWrapper>
        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" aria-modal="true"
            role="dialog">
            <!-- Dark overlay -->
            <div class="absolute inset-0 bg-black/60" @click="closeModal"></div>

            <!-- Dialog -->
            <div class="relative z-10 w-full sm:w-[92%] max-w-xl rounded-lg shadow-2xl bg-rose-50
           max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Header bar -->
                <div class="bg-indigo-800 text-white text-xl sm:text-2xl tracking-wide font-bold text-center py-3 px-4">
                    Magazine Articles
                </div>

                <!-- Body (scrolls if content is tall) -->
                <div class="p-4 sm:p-6 text-heavy overflow-y-auto" style="scrollbar-gutter: stable;">
                    <!-- Make rich text responsive & wrap long content -->
                    <div class="prose max-w-none prose-li:marker:text-heavy tracking-wide font-bold
               prose-img:max-w-full prose-img:h-auto break-words" v-html="renderedDescription"></div>

                    <!-- CTA -->
                    <div class="text-center mt-6">
                        <Link :href="route?.('login') ?? '/register'" class="inline-flex items-center justify-center px-6 py-2 rounded-full
                 bg-heavy text-white font-semibold hover:bg-indigo-700 transition text-lg tracking-wide">
                        Register or Login
                        </Link>
                    </div>
                </div>

                <!-- Close button -->
                <button type="button" @click="closeModal"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" aria-label="Close" title="Close">
                    ✕
                </button>
            </div>
        </div>

    </section>
</template>
<style scoped>
.custom-splide>>>.splide__arrow {
    background: #C3BBA4;
    border-radius: 9999px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
    width: 2.5rem;
    height: 2.5rem;
}

.custom-splide>>>.splide__pagination__page {
    width: 14px !important;
    height: 14px !important;
    background-color: #C3BBA4 !important;
}

.custom-splide>>>.splide__pagination__page.is-active {
    background-color: #ccc !important;
}
</style>