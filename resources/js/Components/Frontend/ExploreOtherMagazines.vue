<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { truncateWords } from '@/utils/text'
import TileShare from './TileShare.vue';

const props = defineProps({
    allMagazines: {
        type: Array,
    },
    currentSlug: {
        type: String,
        required: true,
    },
})
console.log(props.allMagazines)
const filteredMagazines = computed(() => {
    return props.allMagazines?.filter(magazine => magazine.slug !== props.currentSlug) || []
})

</script>


<template>
    <section class="container mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold text-heavy">More Stories to Inspire Your Next Journey</h2>
        <div class="flex flex-wrap gap-6">
            <div v-for="magazine in filteredMagazines" :key="magazine.id"
                class="md:w-[263px] sm:w-full mt-4 pt-4 group transition-transform duration-300" data-aos="fade-up">
                <div class="group relative">
                    <!-- Image (clicks go to article) -->
                    <Link :href="`/magazine/${magazine.slug}`" class="block">
                    <img :src="magazine.hero_image" :alt="magazine.name"
                        class="w-full h-96 object-cover rounded-lg shadow transition-transform duration-500 group-hover:scale-105 group-hover:shadow-xl" />
                    </Link>

                    <!-- Share (not wrapped by Link) -->
                    <TileShare class="absolute top-3 right-3 z-40"
                        :url="`https://ventureupnorth.com.au/magazine/${magazine.slug}`" :title="magazine.name"
                        :text="truncateWords(magazine.seo_description, 20)" />

                    <!-- Tags (not inside Link either) -->
                    <div v-if="magazine.tags?.length" class="absolute left-4 right-16 flex flex-wrap gap-2 z-30"
                        style="top:25px">
                        <Link v-for="tag in magazine.tags" :key="tag.id"
                            :href="`/tours#${(tag.slug || tag.name)?.toLowerCase().replace(/[^\w\s-]/g, '').replace(/\s+/g, '-')}`"
                            class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
                        {{ tag.name }}
                        </Link>
                    </div>

                    <!-- Title (clicks go to article) -->
                    <div class="mt-4">
                        <Link :href="`/magazine/${magazine.slug}`">
                        <strong
                            class="block font-medium tracking-small text-2xl text-heavy transition-colors duration-300 group-hover:text-bison">
                            {{ magazine.name }}
                        </strong>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>
