<script setup>
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    venture: { type: Object, required: true },
    to: { type: String, default: null },
})
const title = props.venture.title

// Default: show page for ventures (your pages can override via `to`)
const url = computed(() => {
    if (props.to) return props.to

    // default fallback for plain ventures
    return `/ventures/${props.venture.slug}`
})

const img = computed(() => {
    const raw = props.venture?.cover_image_url ?? props.venture?.og_image_url;
    if (!raw) return '/public/images/venture.jpeg';
    if (/^https?:\/\//i.test(raw)) return raw;

    // strip a leading "storage/" OR "public/storage/" if present
    const clean = String(raw).replace(/^\/?(?:public\/)?storage\//, '');
    return `/public/storage/${clean}`;
});

const summary = computed(() => {
    const text = props.venture.summary || ''
    const words = text.split(/\s+/)
    return words.length > 20 ? words.slice(0, 20).join(' ') + '...' : text
})

const creator = computed(() =>
    props.venture.type === 'venture' ? 'Admin' : props.venture.owner_guest_name || 'Unknown'
)
</script>

<template>
    <a :href="url"
        class="group block rounded-2xl overflow-hidden bg-black/30 border border-white/10 hover:border-white/20 shadow-lg">
        <div class="relative">
            <img :src="img" :alt="title"
                class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-105" />

            <!-- Overlay -->
            <div class="absolute inset-0 flex flex-col justify-between px-4 py-3 text-white">
                <!-- Title at top -->
                <h3 class="text-2xl font-bold text-granny group-hover:text-white transition">
                    {{ title }}
                </h3>

                <!-- Bottom content -->
                <div>
                    <p class="text-md text-white">
                        By {{ creator }}
                    </p>
                    <div class="mt-3 text-md text-heavy flex items-center gap-3">
                        <span v-if="venture.items_count"
                            class="px-2 py-0.5 rounded-full bg-white/10 border border-white/10">
                            {{ venture.items_count }} items
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>

</template>
