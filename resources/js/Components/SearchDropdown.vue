<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import { Link } from '@inertiajs/vue3'
import { Search, X } from 'lucide-vue-next'

const showSearch = ref(false)
const query = ref('')
const results = ref(null)
const loading = ref(false)
let debounceTimeout = null

defineProps({
    scrolled: Boolean,
    menuOpen: Boolean,
})

watch(query, (val) => {
    if (!val || val.length < 2) {
        results.value = null
        return
    }

    if (debounceTimeout) clearTimeout(debounceTimeout)

    debounceTimeout = setTimeout(async () => {
        loading.value = true
        try {
            const res = await axios.get('/api/search?q=' + encodeURIComponent(val))
            results.value = res.data
        } catch (err) {
            console.error('Search failed:', err)
        } finally {
            loading.value = false
        }
    }, 300)
})
</script>

<template>
    <div class="relative" ref="searchRef">
        <button @click.stop="showSearch = !showSearch" :class="[
            menuOpen ? 'w-5 h-5' : 'w-7 h-7 md:w-6 md:h-6',
            'transition hover:text-teal-200 flex items-center justify-center',
            scrolled ? 'text-heavy' : 'text-white'
        ]">
            <component :is="showSearch ? X : Search" :class="[
                menuOpen ? 'w-5 h-5' : 'w-7 h-7 md:w-6 md:h-6',
                'transition',
                scrolled ? 'text-heavy' : 'text-white'
            ]" />
        </button>

        <!-- Desktop -->
        <transition name="fade-slide">
            <div v-show="showSearch" class="absolute z-50 hidden md:block md:left-[-200px] md:w-[300px] mt-2">
                <input v-model="query" type="text" placeholder="Search..."
                    class="w-full px-4 py-2 text-sm rounded bg-white/90 backdrop-blur border border-gray-300 text-heavy placeholder:text-gray-400 shadow-md" />

                <div v-if="results" class="mt-2 bg-white border text-heavy rounded shadow p-2 space-y-3 text-sm">
                    <template v-if="loading">
                        <div>Searching...</div>
                    </template>

                    <template v-else>
                        <div v-if="results.towns.length">
                            <div class="font-bold mb-1 text-heavy">Towns</div>
                            <ul>
                                <li v-for="item in results.towns" :key="item.id">
                                    <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                        class="w-6 h-6 object-cover rounded" /> -->
                                    <Link :href="`/town/${item.slug}`" class="block py-1 hover:underline text-bison">{{
                                        item.name
                                    }}</Link>
                                </li>
                            </ul>
                        </div>

                        <div v-if="results.experiences.length">
                            <div class="font-bold mb-1 text-heavy">Experiences</div>
                            <ul>
                                <li v-for="item in results.experiences" :key="item.id">
                                    <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                        class="w-6 h-6 object-cover rounded" /> -->
                                    <Link :href="`/experience/${item.slug}`"
                                        class="block py-1 hover:underline text-bison">{{
                                            item.name }}</Link>
                                </li>
                            </ul>
                        </div>

                        <div v-if="results.tours.length">
                            <div class="font-bold mb-1 text-heavy">Tours</div>
                            <ul>
                                <li v-for="item in results.tours" :key="item.id">
                                    <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                        class="w-6 h-6 object-cover rounded" /> -->
                                    <Link :href="`/book/${item.slug}`" class="block py-1 hover:underline text-bison">{{
                                        item.name
                                    }}</Link>
                                </li>
                            </ul>
                        </div>
                        <div v-if="results.posts.length">
                            <div class="font-bold mb-1 text-heavy">Magazine</div>
                            <ul>
                                <li v-for="item in results.posts" :key="item.id">
                                    <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                        class="w-6 h-6 object-cover rounded" /> -->
                                    <Link :href="`/magazine/${item.slug}`"
                                        class="block py-1 hover:underline text-bison">{{
                                            item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                        <div v-if="results.events.length">
                            <div class="font-bold mb-1 text-heavy">Events</div>
                            <ul>
                                <li v-for="item in results.events" :key="item.id">
                                    <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                        class="w-6 h-6 object-cover rounded" /> -->
                                    <Link :href="`/event/${item.slug}`" class="block py-1 hover:underline text-bison">{{
                                        item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                        <div v-if="results.contents.length">
                            <div class="font-bold mb-1 text-heavy">Contents</div>
                            <ul>
                                <li v-for="item in results.contents" :key="item.id">
                                    <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                        class="w-6 h-6 object-cover rounded" /> -->
                                    <Link :href="`/explore/${item.slug}`" class="block py-1 hover:underline text-bison">{{
                                        item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                        <div v-if="results.content_listings.length">
                            <div class="font-bold mb-1 text-heavy">Content Listings</div>
                            <ul>
                                <li v-for="item in results.content_listings" :key="item.id">
                                    <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                        class="w-6 h-6 object-cover rounded" /> -->
                                    <Link :href="`/explore/${item.category.slug}/${item.slug}`" class="block py-1 hover:underline text-bison">{{
                                        item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                        <div v-if="results.collections.length">
                            <div class="font-bold mb-1 text-heavy">Collections</div>
                            <ul>
                                <li v-for="item in results.collections" :key="item.id">
                                    <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                        class="w-6 h-6 object-cover rounded" /> -->
                                    <Link :href="`/collection/${item.slug}`" class="block py-1 hover:underline text-bison">{{
                                        item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                    </template>
                </div>
            </div>
        </transition>

        <!-- Mobile -->
        <transition name="fade-slide">
            <div v-show="showSearch" class="fixed inset-0 z-50 bg-white/95 p-4 flex flex-col md:hidden">
                <div class="flex items-center justify-between mb-4">
                    <input v-model="query" type="text" placeholder="Search..."
                        class="flex-1 px-4 py-3 text-base rounded-md border border-gray-300 text-heavy placeholder:text-gray-400" />
                    <button @click="showSearch = false" class="ml-3 text-heavy hover:text-red-500 transition">
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <div v-if="results" class="overflow-y-auto space-y-4">
                    <div v-if="results.towns.length">
                        <h3 class="font-bold text-lg mb-1 text-heavy">Towns</h3>
                        <ul>
                            <li v-for="item in results.towns" :key="item.id">
                                <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                    class="w-6 h-6 object-cover rounded" /> -->
                                <Link :href="`/town/${item.slug}`" class="block py-1 hover:underline text-bison"
                                    @click="showSearch = false">
                                {{ item.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div v-if="results.experiences.length">
                        <h3 class="font-bold text-lg mb-1 text-heavy">Experiences</h3>
                        <ul>
                            <li v-for="item in results.experiences" :key="item.id">
                                <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                    class="w-6 h-6 object-cover rounded" /> -->
                                <Link :href="`/experience/${item.slug}`" class="block py-1 hover:underline text-bison"
                                    @click="showSearch = false">
                                {{ item.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div v-if="results.tours.length">
                        <h3 class="font-bold text-lg mb-1 text-heavy">Tours</h3>
                        <ul>
                            <li v-for="item in results.tours" :key="item.id">
                                <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                    class="w-6 h-6 object-cover rounded" /> -->
                                <Link :href="`/book/${item.slug}`" class="block py-1 hover:underline text-bison"
                                    @click="showSearch = false">
                                {{ item.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div v-if="results.posts.length">
                        <h3 class="font-bold text-lg mb-1 text-heavy">Magazine</h3>
                        <ul>
                            <li v-for="item in results.posts" :key="item.id">
                                <!-- <img v-if="item.hero_image" :src="item.hero_image"
                                    class="w-6 h-6 object-cover rounded" /> -->
                                <Link :href="`/magazine/${item.slug}`" class="block py-1 hover:underline text-bison"
                                    @click="showSearch = false">
                                {{ item.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.3s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
