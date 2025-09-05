<script setup>
import SliderWrapper from '@/Components/Frontend/SliderWrapper.vue'
import { Link } from '@inertiajs/vue3'
import { truncateWords } from '@/utils/text'
import TileShare from '../TileShare.vue';

defineProps({
    tourTiles: {
        type: Array,
        magazine: Object,
    },
})

function trackClick(item) {
    if (typeof window.gtag === 'function') {
        window.gtag('event', 'rezdy_click', {
            event_category: 'Booking',
            event_label: item.title || item.rezdy_url,
            magazine: magazine?.slug || 'unknown',
            contributor: magazine?.contributor?.name || 'unknown',
        })
    } else {
        console.warn('❌ gtag is NOT available')
    }
}
const slugify = (s) =>
    (s || '')
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
</script>

<template>
    <div class="bg-white text-heavy">
        <section class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-left mb-12">Explore Tours</h2>

            <SliderWrapper :items="tourTiles" :per-page="3" gap="1.5rem">
                <template #default="{ item }">
                    <div class="relative group overflow-hidden rounded-xl shadow-lg mb-10 bg-black/30 hover:bg-black/50 transition duration-500"
                        data-aos="fade-up">
                        <!-- Image -->
                        <img :src="item.image" :alt="item.title"
                            class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />
                        <TileShare :url="`https://venturedownsouth.com.au/tours/book/${item.slug}`" :title="item.title"
                            :text="truncateWords(item.seo_description, 20)" />
                        <!-- ✅ Tags block (with subtle blur and translucent background) -->
                        <div v-if="item.tags?.length" class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
                            <Link v-for="tag in item.tags" :key="tag.id"
                                :href="`/tours#${tag.slug || slugify(tag.name)}`"
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
                                <a :href="`/tours/book/${item.slug}`" @click="trackClick(tile)"
                                    class="bg-white text-heavy font-semibold py-2 rounded-full text-sm text-center shadow hover:bg-envy hover:text-white transition">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </SliderWrapper>
        </section>
    </div>
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