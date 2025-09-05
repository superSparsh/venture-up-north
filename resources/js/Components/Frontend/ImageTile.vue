<template>
    <section class="container mx-auto px-4 py-16">
        <h2 class="text-4xl font-bold text-left mb-12 text-heavy">Let’s Indulge</h2>
        <div class="hidden md:grid grid-cols-3 grid-rows-2 gap-6">
            <template v-for="(tile, index) in tiles" :key="tile.id">
                <Link :href="tile.link" :class="tileClasses(index)"
                    class="relative group overflow-hidden rounded-xl shadow hover:shadow-lg transition-all duration-300">
                <img :src="tile.image" :alt="tile.title"
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                <!-- <TileShare :url="`https://venturedownsouth.com.au/${tile.link}`" :title="tile.title"
                    :text="truncateWords(tile.summary, 20)" /> -->
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center text-center px-4">
                    <div>
                        <h2 class="text-white text-md md:text-4xl font-bold leading-snug text-center">
                            <template v-for="(part, i) in tile.title.split(',')" :key="i">
                                <div>{{ part.trim() }}</div>
                            </template>
                        </h2>
                        <!-- <p v-if="tile.subtitle" class="text-white text-sm mt-1 whitespace-pre-line">
                                {{ tile.subtitle }}
                            </p> -->
                    </div>
                </div>
                </Link>
            </template>
        </div>

        <!-- Mobile Slider -->
        <div class="block md:hidden">
            <SliderWrapper :items="tiles" :per-page="1" gap="1.5rem">
                <template #default="{ item }">
                    <div class="relative group overflow-hidden rounded-xl shadow-lg mb-10 bg-black/30 hover:bg-black/50 transition duration-500"
                        data-aos="fade-up">
                        <!-- Image -->
                        <Link :href="item.link"
                            class="relative group overflow-hidden rounded-xl shadow hover:shadow-lg transition-all duration-300 h-[220px]">
                        <img :src="item.image" :alt="item.title"
                            class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />


                        <!-- Dark overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 to-black/10 transition-opacity opacity-0 group-hover:opacity-100 duration-300">
                        </div>

                        <!-- Content on hover -->
                        <div class="absolute inset-0 flex flex-col justify-center items-center text-white px-4 group">
                            <!-- ✨ Name (centered by default, moves up on hover) -->
                            <h2
                                class="text-2xl md:text-3xl font-extrabold tracking-widest transition-all duration-300 transform group-hover:-translate-y-10">
                                <template v-for="(part, i) in item.title.split(',')" :key="i">
                                    <div>{{ part.trim() }}</div>
                                </template>
                            </h2>
                        </div>
                        </Link>
                    </div>
                </template>
            </SliderWrapper>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import SliderWrapper from '@/Components/Frontend/SliderWrapper.vue'
import TileShare from './TileShare.vue';
import { truncateWords } from '@/utils/text'

defineProps({
    tiles: {
        type: Array,
        default: () => []
    }
})

function tileClasses(index) {
    switch (index) {
        case 0: return 'col-start-1 row-start-1 h-[180px] md:h-[250px]'
        case 1: return 'col-start-2 row-start-1 row-span-2 h-[376px] md:h-[525px]' // Center
        case 2: return 'col-start-1 row-start-2 h-[180px] md:h-[250px]'
        case 3: return 'col-start-3 row-start-1 h-[180px] md:h-[250px]'
        case 4: return 'col-start-3 row-start-2 h-[180px] md:h-[250px]'
        default: return ''
    }
}
</script>
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
    background-color: #323B2F !important;

}

.custom-splide>>>.splide__pagination__page.is-active {
    background-color: #ccc !important;
}
</style>