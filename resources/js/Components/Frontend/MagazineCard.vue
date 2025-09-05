<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    magazine: { type: Object, required: true }
})
const title = props.magazine.title
const url = `/magazine/${props.magazine.slug}`
const img = `/public/storage/${props.magazine.hero_image}` || '/public/storage/images/Venture-Up-North.png'
const summary = props.magazine.summary || ''
</script>

<template>
    <Link :href="url"
        class="group block rounded-2xl overflow-hidden bg-black/30 border border-white/10 hover:border-white/20 shadow-lg">
    <div class="relative">
        <img :src="img" :alt="title"
            class="h-64 object-cover w-full object-cover transition-transform duration-500 group-hover:scale-105" />
        <div class="absolute inset-0 flex flex-col justify-end items-start text-white px-4 group bottom-5">
            <h3 class="text-2xl font-bold text-white group-hover:text-heavy transition">
                {{ title }}
            </h3>
            <p v-if="summary" class="text-sm text-white/70 mt-1 line-clamp-2">{{ summary }}</p>
        </div>
    </div>

    </Link>
</template>
