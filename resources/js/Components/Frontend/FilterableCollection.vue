<template>
    <div class="bg-heavy text-bison">
        <section class="container mx-auto px-4 py-16">
            <h2 class="text-4xl font-bold text-left mb-12">{{ collection.name }}</h2>
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
                <!-- Tag Filter -->
                <Multiselect v-model="filters.tags" :options="tags" placeholder="Select Tags" label="name" track-by="id"
                    :multiple="true" class="w-full" />

                <!-- Search -->
                <input v-model="filters.search" type="text" class="w-full px-3 py-2 border rounded"
                    placeholder="Search..." />
            </div>

            <!-- Listings -->
            <TransitionGroup tag="div" name="fade-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                appear data-aos="fade-up">
                <Link v-for="item in items" :key="item.id" :href="`/collection/${item.slug}`" class="block group">
                <!-- Image Block -->
                <div
                    class="relative overflow-hidden rounded-xl shadow-lg transition duration-300 hover:shadow-xl group">
                    <!-- collection Image -->
                    <img :src="`/public/storage/${item.hero_image}`" :alt="item.name"
                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />

                    <TileShare :url="`https://ventureupnorth.com.au/collection/${item.slug}`" :title="item.name"
                        :text="truncateWords(item.summary, 20)" />

                    <!-- ✅ Tags block (with subtle blur and translucent background) -->
                    <div v-if="item.tags?.length" class="absolute top-4 right-4 flex flex-wrap gap-2 z-30">
                        <span v-for="tag in item.tags" :key="tag.id"
                            class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow">
                            {{ tag.name }}
                        </span>
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
                </div>
                </Link>
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
import TileShare from './TileShare.vue'

// Props
const props = defineProps({
    collection: Object,
    tags: Array,
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

    axios
        .get(props.apiRoute, {
            params: {
                page: page.value,
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
    pointer-collections: none;
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