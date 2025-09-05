<script setup>
import SliderWrapper from '@/Components/Frontend/SliderWrapper.vue'
import { Link } from '@inertiajs/vue3'
import { truncateWords } from '@/utils/text'
import TileShare from './TileShare.vue';
import AddToMyVentureButton from '@/Components/Frontend/AddToMyVentureButton.vue';

defineProps({
    towns: {
        type: Array,
    },
    tags: {
        type: Array,
    },
})

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
    <section class="container mx-auto px-4 py-16">
        <h2 class="text-4xl font-bold text-left mb-12 text-heavy">Let’s Venture</h2>
        <SliderWrapper :items="towns" :per-page="3" gap="1.5rem">
            <template #default="{ item }">
                <div class="relative group overflow-hidden rounded-xl shadow-lg mb-10 bg-black/30 hover:bg-black/50 transition duration-500"
                    data-aos="fade-up">
                    <!-- Image -->
                    <img :src="item.hero_image" :alt="item.name"
                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />

                    <TileShare :url="`https://venturedownsouth.com.au/town/${item.slug}`" :title="item.name"
                        :text="truncateWords(item.seo_description, 20)" />

                    <!-- ❤️ AddToMyVentureButton floating -->
                    <div class="absolute top-3 right-16 z-40">
                        <AddToMyVentureButton title="Add to My Venture" :item="{
                            id: item.id,
                            type: 'town',
                            title: item.name,
                            url: item.slug,
                            image: item.hero_image,
                            tags: item.tags
                        }" iconOnly />
                    </div>

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
                        <!-- ✨ Name (always visible, floats above hover buttons) -->
                        <h2
                            class="text-2xl md:text-3xl font-extrabold tracking-widest transition-all duration-300 transform group-hover:-translate-y-20 z-20">
                            {{ item.name }}
                        </h2>

                        <!-- ✨ Hover Buttons -->
                        <div
                            class="absolute bottom-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col gap-3 w-full px-6 z-10">
                            <Link :href="`/town/${item.slug}`"
                                class="bg-envy text-heavy font-semibold py-2 rounded-full text-sm text-center shadow hover:bg-bison hover:text-heavy transition">
                            About {{ item.name }}
                            </Link>
                            <Link :href="`/tours?town=${item.slug}`"
                                class="bg-white text-heavy font-semibold py-2 rounded-full text-sm text-center shadow hover:bg-envy hover:text-white transition">
                            Tours
                            </Link>
                            <Link :href="`/explore/accommodation-down-south?town=${item.slug}`"
                                class="bg-envy text-heavy font-semibold py-2 rounded-full text-sm text-center shadow hover:bg-bison hover:text-heavy transition">
                            Accommodation
                            </Link>
                        </div>
                    </div>



                </div>
            </template>
        </SliderWrapper>
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
    background-color: #323B2F !important;

}

.custom-splide>>>.splide__pagination__page.is-active {
    background-color: #ccc !important;
}
</style>