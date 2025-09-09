<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import TileShare from './TileShare.vue';
import { truncateWords } from '@/utils/text'

const props = defineProps({
    allExperiences: {
        type: Array,
    },
    currentSlug: {
        type: String,
        required: true,
    },
})

const filteredExperiences = computed(() => {
    return props.allExperiences?.filter(experience => experience.slug !== props.currentSlug) || []
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
    <section class="container mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold text-heavy">Discover More Hidden Gems</h2>
        <div class="flex flex-wrap gap-6">
            <div v-for="experience in filteredExperiences" :key="experience.id"
                class="md:w-[263px] sm:w-full mt-4 pt-4 group transition-transform duration-300" data-aos="fade-up">
                <Link :href="`/experience/${experience.slug}`">
                <img :src="experience.hero_image" :alt="experience.name"
                    class="w-full h-96 object-cover rounded-lg shadow transition-transform duration-500 group-hover:scale-105 group-hover:shadow-xl" />
                <TileShare :url="`https://ventureupnorth.com.au/experience/${experience.slug}`" :title="experience.name"
                    :text="truncateWords(experience.seo_description, 20)" />
                <div v-if="item.tags?.length" class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
                    <Link v-for="tag in item.tags" :key="tag.id" :href="`/tours#${tag.slug || slugify(tag.name)}`"
                        class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
                    {{ tag.name }}
                    </Link>
                </div>
                <div class="mt-4">
                    <strong
                        class="block font-medium tracking-small text-2xl text-heavy transition-colors duration-300 group-hover:text-bison">
                        {{ experience.name }}
                    </strong>
                </div>
                </Link>
            </div>
        </div>
    </section>
</template>
