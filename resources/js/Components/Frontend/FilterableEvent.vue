<template>
    <div class="bg-heavy text-bison">
        <section class="container mx-auto px-4 py-16">
            <div class="flex flex-col items-center text-center gap-4
           md:flex-row md:items-center md:justify-between md:text-left md:gap-6 mb-10">
                <!-- Heading -->
                <h2 class="text-4xl font-bold text-left">{{ event.name }}</h2>


                <!-- Button -->
                <button type="button" @click="openModal" class="inline-flex items-center justify-center rounded-full
             px-4 sm:px-6 py-2.5
             text-base sm:text-lg md:text-xl
             bg-bison text-heavy font-semibold shadow
             hover:bg-envy  hover:text-white transition
             md:ml-4">
                    Submit Event
                </button>
            </div>
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                <!-- Tag Filter -->
                <Multiselect v-model="filters.tags" :options="tags" placeholder="Select Tags" label="name" track-by="id"
                    :multiple="true" class="w-full" />

                <!-- Town Filter -->
                <Multiselect v-model="filters.town" :options="towns" label="name" track-by="id" :multiple="true"
                    :close-on-select="false" placeholder="Select Towns" class="w-full" />

                <!-- Month Filter -->
                <!-- <Multiselect v-model="filters.month" :options="months" :multiple="true" :close-on-select="false"
                    placeholder="Select Months" class="w-full" /> -->


                <!-- Search -->
                <input v-model="filters.search" type="text" class="w-full px-3 py-2 border rounded"
                    placeholder="Search..." />
            </div>

            <!-- Listings -->
            <TransitionGroup tag="div" name="fade-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                appear data-aos="fade-up">
                <Link v-for="item in items" :key="item.id" :href="`/event/${item.slug}`" class="block group">
                <!-- Image Block -->
                <div
                    class="relative overflow-hidden rounded-xl shadow-lg transition duration-300 hover:shadow-xl group">
                    <!-- Event Image -->
                    <img :src="`/public/storage/${item.hero_image}`" :alt="item.name"
                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />


                    <TileShare :url="`https://ventureupnorth.com.au/event/${item.slug}`" :title="item.name"
                        :text="truncateWords(item.summary, 20)" />

                    <!-- ❤️ AddToMyVentureButton floating -->
                    <div class="absolute top-3 right-16 z-40">
                        <AddToMyVentureButton title="Add to My Venture" :item="{
                            id: item.id,
                            type: 'event',
                            title: item.name,
                            url: item.slug,
                            image: item.hero_image,
                            tags: item.tags
                        }" iconOnly />
                    </div>
                    <!-- ✅ Tags block (with subtle blur and translucent background) -->
                    <div v-if="item.tags?.length" class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
                        <Link v-for="tag in item.tags" :key="tag.id" :href="`/events#${tag.slug || slugify(tag.name)}`"
                            class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
                        {{ tag.name }}
                        </Link>
                    </div>

                    <!-- Summary on Hover -->
                    <div
                        class="absolute inset-0 bg-black/70 text-white z-[10] opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-4 flex items-center justify-center text-md text-center">
                        <p>{{ truncateWords(item.summary, 30) }}</p>
                    </div>
                </div>

                <!-- Details Outside Image -->
                <div class="mt-3 px-1 cursor-pointer">
                    <!-- Title -->
                    <h3 class="text-xl tracking-wide font-bold text-bison">
                        {{ item.name }}
                    </h3>


                    <!-- Custom Fields as Pills -->
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span
                            class="px-3 py-1 text-sm rounded border-2 border-bison text-white backdrop-blur-md bg-white/10 relative overflow-hidden animated-border">
                            {{ getEventDateDisplay(item) }}
                        </span>
                    </div>


                    <!-- Linked Town Names -->
                    <div class="text-sm text-gray-500 tracking-wider mt-3 flex flex-wrap items-center mt-10">
                        <template v-for="(town, index) in item.towns" :key="town.id">
                            <Link :href="`/town/${town.slug}`"
                                class="hover:underline hover:text-bison hover:font-bold mr-2 text-gray-600 transition"
                                @click.stop target="_blank" rel="noopener">
                            {{ town.name.toUpperCase() }}
                            </Link>
                            <span v-if="index < item.towns.length - 1" class="text-gray-400 mr-2">|</span>
                        </template>
                    </div>
                </div>
                </Link>
            </TransitionGroup>

            <!-- Loader or End Message -->
            <div v-if="loading" class="text-center py-6">Loading...</div>
            <div v-if="finished && !loading" class="text-center py-6 text-gray-400">No more results.</div>

            <!-- Observer Target -->
            <div ref="scrollTarget" class="h-1" />
        </section>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" aria-modal="true"
            role="dialog">
            <!-- Dark overlay -->
            <div class="absolute inset-0 bg-black/60" @click="closeModal" />

            <!-- Dialog -->
            <div class="relative z-10 w-full sm:w-[92%] max-w-xl rounded-lg shadow-2xl bg-rose-50
           max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Header bar -->
                <div
                    class="bg-indigo-800 text-white text-2xl tracking-wide sm:text-xl font-bold text-center py-3 rounded-t-md">
                    Event Submission
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
                <button type="button" @click="closeModal" class="absolute top-2 right-2 text-gray-200 hover:text-white"
                    aria-label="Close" title="Close">
                    ✕
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onBeforeUnmount, computed } from 'vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import axios from 'axios'
import { Link } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import TileShare from './TileShare.vue';
import EditorJSHTML from 'editorjs-html'
import AddToMyVentureButton from '@/Components/Frontend/AddToMyVentureButton.vue';

// Props
const props = defineProps({
    event: Object,
    tags: Array,
    towns: Array,
    months: {
        type: Array,
        default: () => [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ]
    },
    apiRoute: {
        type: String,
        required: true
    },
    initialItems: {
        type: Array, // ✅ should be Array, not Object
        default: () => []
    },
    eventPopup: { type: String, default: '' },
})
console.log(props.eventPopup)
// Filters
const filters = reactive({
    tags: [],
    town: [],
    search: ''
})

// Items
const items = ref([...props.initialItems])
const page = ref(2)
const loading = ref(false)
const finished = ref(false)
const scrollTarget = ref(null)

const format = (date) => dayjs(date).format('MMM D, YYYY')

function getEventDateDisplay(item) {
    const start = item.start_date
    const end = item.end_date
    const label = item.event_date_label

    if (start && end) {
        return `${format(new Date(start), 'd MMM yyyy')} – ${format(new Date(end), 'd MMM yyyy')}`
    }

    if (start && label) {
        return `${format(new Date(start), 'd MMM yyyy')} · ${label}`
    }

    if (label) {
        return label
    }

    return ''
}
const slugify = (s) =>
    (s || '')
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

function findTagByHash(hash) {
    const raw = (hash || '').replace(/^#/, '');
    if (!raw) return null;

    // prefer slug if your tags have it
    let tag = props.tags.find(t => t.slug === raw);
    if (tag) return tag;

    // fallback: slugified name
    return props.tags.find(t => slugify(t.name) === raw) || null;
}

function setHashForSingleTag(tagsArr) {
    // only set hash when EXACTLY one tag is selected; otherwise leave as-is
    if (!tagsArr || tagsArr.length === 0) {
        history.replaceState(null, '', `${location.pathname}${location.search}`);
        return;
    }
    if (tagsArr.length === 1) {
        const t = tagsArr[0];
        const h = `#${t.slug || slugify(t.name)}`;
        history.replaceState(null, '', `${location.pathname}${location.search}${h}`);
    }
    // if >1 tags selected, do nothing (don’t change the hash)
}
// Load Items (lazy)
function loadItems(reset = false) {
    if (loading.value || finished.value) return

    loading.value = true
    const tagIds = filters.tags.map(t => t.id)
    const townIds = filters.town.map(t => t.id)

    axios
        .get(props.apiRoute, {
            params: {
                page: page.value,
                town_ids: townIds,
                tags: tagIds,
                search: filters.search?.trim() || ''
            }
        })
        .then(res => {
            const newItems = res.data.data || []

            if (reset) {
                items.value = [...newItems]
                page.value = 2
                finished.value = newItems.length === 0
            } else {
                items.value.push(...newItems)
                if (newItems.length === 0) finished.value = true
                else page.value++
            }
        })
        .finally(() => {
            loading.value = false
        })
}

// Watch Filters
watch(filters, () => {
    finished.value = false
    page.value = 1
    loadItems(true)
}, { deep: true })

watch(
    () => (Array.isArray(filters.tags) ? filters.tags.map(t => t.id) : []),
    () => {
        // only reflect a single-tag selection in the URL (shareable)
        setHashForSingleTag(Array.isArray(filters.tags) ? filters.tags : []);
    },
    { deep: false }
);

// Lazy Load on Scroll
function observeScroll() {
    const observer = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting && !loading.value && !finished.value) {
            loadItems()
        }
    })

    // preselect tag from hash (e.g., /tours#winetasting)
    const initialTag = findTagByHash(window.location.hash);
    if (initialTag) {
        const already = filters.tags?.some(t => t.id === initialTag.id);
        if (!already) {
            // keep multiselect behavior: just push it into the array
            filters.tags.push(initialTag);
            // no need to call loadItems here; your existing deep watch(filters, …) will do it
        }
    }
    observer.observe(scrollTarget.value)
}

// On Mount
onMounted(() => {
    observeScroll()
})

function truncateWords(text, count) {
    if (!text) return ''
    return text.split(' ').slice(0, count).join(' ') + '...'
}


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
    if (!props.eventPopup) return ''
    try {
        const json = typeof props.eventPopup === 'string'
            ? JSON.parse(props.eventPopup)
            : props.eventPopup

        const parsed = edjsParser.parse(json)

        return Array.isArray(parsed) ? parsed.join('') : parsed
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})
</script>

<style scoped>
.fade-list-enter-active,
.fade-list-leave-active {
    transition: all 0.3s ease;
}

.fade-list-enter-from,
.fade-list-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

.animated-border::before {
    content: '';
    position: absolute;
    inset: 0;
    border: 2px solid #323B2F;
    /* Bison */
    opacity: 0.3;
    animation: pulse-border 2s infinite ease-in-out;
    border-radius: inherit;
    pointer-events: none;
}

@keyframes pulse-border {
    0% {
        transform: scale(1);
        opacity: 0.4;
    }

    50% {
        transform: scale(1.03);
        opacity: 0.8;
    }

    100% {
        transform: scale(1);
        opacity: 0.4;
    }
}
</style>