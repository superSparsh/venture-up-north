<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { truncateWords } from '@/utils/text'
import TileShare from './TileShare.vue';

const props = defineProps({
    allTowns: {
        type: Array,
    },
    currentSlug: {
        type: String,
        required: true,
    },
    tags: {
        type: Object,
    },
})
console.log(props.allTowns)

const filteredTowns = computed(() => {
    return props.allTowns?.filter(town => town.slug !== props.currentSlug) || []
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
        <h2 class="text-3xl font-bold text-heavy">Explore Other Towns of the Region</h2>
        <div class="flex flex-wrap gap-6">
            <div v-for="town in filteredTowns" :key="town.id"
                class="md:w-[263px] sm:w-full mt-4 pt-4 group transition-transform duration-300" data-aos="fade-up">
                <Link :href="`/town/${town.slug}`">
                <img :src="town.hero_image" :alt="town.name"
                    class="w-full h-96 object-cover rounded-lg shadow transition-transform duration-500 group-hover:scale-105 group-hover:shadow-xl" />
                <TileShare :url="`https://venturedownsouth.com.au/town/${town.slug}`" :title="town.name"
                    :text="truncateWords(town.seo_description, 20)" />
                <!-- ✅ Tags block (with subtle blur and translucent background) -->
                <div v-if="town.tags?.length" class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
                    <Link v-for="tag in item.tags" :key="tag.id" :href="`/tours#${tag.slug || slugify(tag.name)}`"
                        class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
                    {{ tag.name }}
                    </Link>
                </div>
                <div class="mt-4">
                    <strong
                        class="block font-medium tracking-small text-2xl text-heavy transition-colors duration-300 group-hover:text-bison">
                        {{ town.name }}
                    </strong>
                </div>
                </Link>
            </div>
        </div>
    </section>
</template>
