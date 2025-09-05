<template>
    <div class="bg-heavy text-bison">
        <section class="container mx-auto px-4 py-16">
            <h2 class="text-4xl font-bold text-left mb-12">{{ tour.name }}</h2>
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                <!-- Tag Filter -->
                <Multiselect v-model="filters.tags" :options="tags" placeholder="Select Tags" label="name" track-by="id"
                    :multiple="true" class="w-full" />

                <!-- Town Filter -->
                <Multiselect v-model="filters.town" :options="towns" label="name" track-by="id" :multiple="true"
                    :close-on-select="false" placeholder="Select Towns" class="w-full" />


                <!-- Search -->
                <input v-model="filters.search" type="text" class="w-full px-3 py-2 border rounded"
                    placeholder="Search..." />
            </div>

            <!-- Listings -->
            <TransitionGroup tag="div" name="fade-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                appear data-aos="fade-up">
                <a v-for="item in items" :key="item.id" :href="`/tours/book/${item.slug}`" class="block group">
                    <!-- Image Block -->
                    <div
                        class="relative overflow-hidden rounded-xl shadow-lg transition duration-300 hover:shadow-xl group">
                        <!-- Event Image -->
                        <img :src="`/public/storage/${item.image}`" :alt="item.name"
                            class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />

                        <TileShare :url="`https://venturedownsouth.com.au/tours/book/${item.slug}`" :title="item.title"
                            :text="truncateWords(item.summary, 20)" />

                        <!-- ❤️ AddToMyVentureButton floating -->
                        <div class="absolute top-3 right-16 z-40">
                            <AddToMyVentureButton title="Add to My Venture" :item="{
                                id: item.id,
                                type: 'tour',
                                title: item.title,
                                url: item.slug,
                                image: item.image,
                                tags: item.tags
                            }" iconOnly />
                        </div>

                        <!-- ✅ Tags block (centered, not touching edges) -->
                        <div v-if="item.tags?.length" class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
                            <Link v-for="tag in item.tags" :key="tag.id"
                                :href="`/tours#${tag.slug || slugify(tag.name)}`"
                                class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
                            {{ tag.name }}
                            </Link>
                        </div>


                        <!-- Summary on Hover -->
                        <div
                            class="absolute inset-0 bg-black/70 text-white z-[10] opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col items-center justify-center text-center space-y-4">
                            <!-- Summary Text -->
                            <p class="text-md leading-relaxed">
                                {{ truncateWords(item.summary, 30) }}
                            </p>
                        </div>

                    </div>

                    <!-- Details Outside Image -->
                    <div class="mt-3 px-1 cursor-pointer">
                        <!-- Title -->
                        <h3 class="text-xl tracking-wide font-bold text-bison">
                            {{ item.title }}
                        </h3>

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
                </a>
            </TransitionGroup>

            <!-- Loader or End Message -->
            <div v-if="loading" class="text-center py-6">Loading...</div>
            <div v-if="finished && !loading" class="text-center py-6 text-gray-400">No more results.</div>

            <!-- Observer Target -->
            <div ref="scrollTarget" class="h-1" />
        </section>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import axios from 'axios'
import { Link } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import TileShare from './TileShare.vue';
import AddToMyVentureButton from '@/Components/Frontend/AddToMyVentureButton.vue';

// Props
const props = defineProps({
    tour: Object,
    tags: Array,
    towns: Array,
    apiRoute: {
        type: String,
        required: true
    },
    initialItems: {
        type: Array, // ✅ should be Array, not Object
        default: () => []
    }
})

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

// --- helpers ---
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


// 1) Your existing fetch watcher (unchanged)
watch(
    filters,
    () => {
        finished.value = false;
        page.value = 1;
        loadItems(true);
    },
    { deep: true }
);

// 2) A separate watcher ONLY for the URL hash sync
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
    observer.observe(scrollTarget.value)
}

// On Mount
onMounted(() => {

    const urlParams = new URLSearchParams(window.location.search)
    const selectedTownSlug = urlParams.get('town')

    if (selectedTownSlug) {
        const matchedTown = props.towns.find(town => town.slug === selectedTownSlug)
        if (matchedTown) {
            filters.town = [matchedTown]
            finished.value = false
            page.value = 1
            loadItems(true)
        }
    }

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

    observeScroll()
})

function truncateWords(text, count) {
    if (!text) return ''
    return text.split(' ').slice(0, count).join(' ') + '...'
}
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