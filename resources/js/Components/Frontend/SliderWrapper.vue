<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide'

defineProps({
    items: Array,
    perPage: { type: Number, default: 3 },
    gap: { type: String, default: '1rem' },
    showArrows: { type: Boolean, default: false },
    showPagination: { type: Boolean, default: true },
})
</script>

<template>
    <Splide :options="{
        perPage,
        gap,
        arrows: showArrows,
        pagination: showPagination,
        breakpoints: {
            1024: { perPage: 2 },
            640: { perPage: 1 }
        }
    }" class="custom-splide">
        <SplideSlide v-for="item in items" :key="item.id">
            <slot :item="item" />
        </SplideSlide>
    </Splide>
</template>
