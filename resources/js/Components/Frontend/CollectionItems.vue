<script setup>
import SliderWrapper from '@/Components/Frontend/SliderWrapper.vue'
import { Link } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const props = defineProps({
    events: {
        type: Array,
        default: () => []
    },
    listings: {
        type: Array,
        default: () => []
    },
    tours: {
        type: Array,
        default: () => []
    },
    tags: Array,
    collection: Object
})
// Filters
const selectedTags = ref([])
const searchText = ref('')

// Filter logic
const filterItems = (items) => {
    return items.filter(item => {
        // ✅ Match search
        const matchesSearch = !searchText.value ||
            item.title?.toLowerCase().includes(searchText.value.toLowerCase()) ||
            item.name?.toLowerCase().includes(searchText.value.toLowerCase())

        // ✅ Match tags
        const selectedIds = selectedTags.value.map(tag => tag.id)
        const matchesTags = selectedIds.length === 0 || item.tags?.some(tag => selectedIds.includes(tag.id))

        return matchesSearch && matchesTags
    })
}

// Computed filtered sets
const filteredListings = computed(() => filterItems(props.listings))
const filteredTours = computed(() => filterItems(props.tours))
const filteredEvents = computed(() => filterItems(props.events))

const allItems = computed(() => {
    return [
        ...filteredEvents.value.map(item => ({ ...item, type: 'event' })),
        ...filteredListings.value.map(item => ({ ...item, type: 'listing' })),
        ...filteredTours.value.map(item => ({ ...item, type: 'tour' }))
    ]
})

const itemsPerPage = 15
const currentPage = ref(1)

const paginatedItems = computed(() =>
    allItems.value.slice(0, currentPage.value * itemsPerPage)
)

const handleScroll = () => {
    const bottomReached =
        window.innerHeight + window.scrollY >= document.body.offsetHeight - 300
    if (bottomReached && paginatedItems.value.length < allItems.value.length) {
        currentPage.value += 1
    }
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<style scoped>
.custom-splide>>>.splide__arrow {
    background: #C3BBA4 !important;
    border-radius: 9999px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
    width: 2.5rem;
    height: 2.5rem;
}

.custom-splide>>>.splide__pagination__page {
    width: 14px !important;
    height: 14px !important;
    background-color: #8DA894 !important;

}

.custom-splide>>>.splide__pagination__page.is-active {
    background-color: #C3BBA4 !important;
}
</style>

<template>
    <div class="bg-heavy text-bison ">
        <section class="container mx-auto px-4 py-12">
            <h2 class="text-4xl font-bold text-left mb-12" v-if="collection.type === 'collection'">
                Let's Explore
            </h2>

            <!-- Show filters only for collection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-14" v-if="collection.type === 'collection'">
                <!-- Tag filter -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Filter by Tags</label>
                    <Multiselect v-model="selectedTags" :options="tags" :multiple="true" track-by="id" label="name"
                        placeholder="Select Tags"
                        class="w-full bg-white rounded border border-heavy focus:outline-none" />
                </div>

                <!-- Search input -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Search</label>
                    <input v-model="searchText" type="text"
                        class="w-full px-4 py-2 rounded border border-bison focus:outline-none"
                        placeholder="Search by title or name..." />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 flex flex-wrap gap-6"
                :class="{ 'border border-bison p-6 rounded-2xl': collection.type != 'collection' }">
                <div v-for="(item, index) in paginatedItems" :key="`${item.type}-${item.id}`"
                    class="group cursor-pointer transition-transform duration-300 w-full mb-10 cursor-pointer"
                    data-aos="fade-up" :style="{ transitionDelay: `${index * 100}ms` }">

                    <div
                        class="relative overflow-hidden rounded-xl shadow-lg bg-black/30 group-hover:bg-black/50 transition duration-500 h-96 ">
                        <img :src="item.image || item.hero_image" :alt="item.name || item.title"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />

                        <!-- Tags -->
                        <div v-if="item.tags?.length" class="absolute top-4 right-4 flex flex-wrap gap-2 z-30">
                            <span v-for="tag in item.tags" :key="tag.id"
                                class="text-white text-xs font-semibold px-2 py-1 rounded-full backdrop-blur-sm bg-black/40 shadow">
                                {{ tag.name }}
                            </span>
                        </div>

                        <!-- Hover overlay with button -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                            <div
                                class="absolute bottom-5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col gap-3 w-full px-6">
                                <Link v-if="item.type === 'event'" :href="`/event/${item.slug}`"
                                    class="bg-envy text-heavy font-semibold py-2 rounded-full text-sm text-center shadow hover:bg-bison hover:text-heavy transition">
                                About {{ item.name }}
                                </Link>
                                <Link v-else-if="item.type === 'listing'"
                                    :href="`/explore/${item.category?.slug ?? 'unknown'}/${item.slug}`"
                                    class="bg-envy text-heavy font-semibold py-2 rounded-full text-sm text-center shadow hover:bg-bison hover:text-heavy transition">
                                About {{ item.name }}
                                </Link>
                                <a v-else-if="item.type === 'tour'" :href="`/tours/book/${item.slug}`"
                                    class="bg-envy text-heavy font-semibold py-2 rounded-full text-sm text-center shadow hover:bg-bison hover:text-heavy transition">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 text-center">
                        <strong class="block font-semibold text-lg hover:text-white">
                            {{ item.name || item.title }}
                        </strong>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
