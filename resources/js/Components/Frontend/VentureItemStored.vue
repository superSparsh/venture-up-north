<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AddToMyVentureButton from '@/Components/Frontend/AddToMyVentureButton.vue'
import { MapPinIcon, TicketIcon, CalendarIcon, BuildingStorefrontIcon, SparklesIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline'
import TileShare from './TileShare.vue';
import { truncateWords } from '@/utils/text'
const slugify = (s) =>
    (s || '')
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

const props = defineProps({
    item: { type: Object, required: true },
})
// console.log(props.item)
const emit = defineEmits(['remove'])

/** Type meta: label + colors + icon (easy to extend later) */
const TYPE_META = {
    event: { label: 'Event', pill: 'bg-fuchsia-500/20 text-fuchsia-200 ring-1 ring-inset ring-fuchsia-400/30', icon: CalendarIcon },
    experience: { label: 'Experience', pill: 'bg-sky-500/20 text-sky-200 ring-1 ring-inset ring-sky-400/30', icon: SparklesIcon },
    tour: { label: 'Tour', pill: 'bg-emerald-500/20 text-emerald-200 ring-1 ring-inset ring-emerald-400/30', icon: TicketIcon },
    listing: { label: 'Listing', pill: 'bg-amber-500/20 text-amber-200 ring-1 ring-inset ring-amber-400/30', icon: BuildingStorefrontIcon },
    town: { label: 'Town', pill: 'bg-rose-500/20 text-rose-200 ring-1 ring-inset ring-rose-400/30', icon: MapPinIcon },
}
const kind = computed(() => TYPE_META[props.item.type] || { label: props.item.type || 'Place', pill: 'bg-white/10 text-white/80 ring-1 ring-inset ring-white/20', icon: MapPinIcon })

const imgSrc = computed(() => {
    const raw = props.item.image_url || props.item.og_image_url || '/images/placeholder.webp'

    // if it already starts with /public/storage/, just return as is
    if (raw.startsWith('/public/storage/')) {
        return raw
    }

    // if it’s an absolute URL (http/https), don’t prepend
    if (/^https?:\/\//i.test(raw)) {
        return raw
    }

    // otherwise prepend /public/storage/
    return `/public/storage/${raw.replace(/^\/+/, '')}`
})
const title = computed(() => props.item.title || props.item.name || 'Untitled')
const url = computed(() => props.item.url || '#')
const cat_url = computed(() => props.item.cat_source_url || '#')
const hasGeo = computed(() => props.item.lat != null && props.item.lng != null)

/** Smart CTAs by type (extend anytime) */
const ctas = computed(() => {
    const t = props.item.item_type
    const slug = props.item.source_url
    const cat_slug = props.item.cat_source_url
    const arr = []
    if (t == 'town' && slug) {
        arr.push({ href: `/town/${slug}`, label: `About ${title.value}` })
        arr.push({ href: `/tours?town=${slug}`, label: 'Tours' })
        arr.push({ href: `/explore/accommodation-up-north?town=${slug}`, label: 'Accommodation' })
    }
    if (t == 'experience' && slug) {
        arr.push({ href: `/experience/${slug}`, label: `About ${title.value}` })
        arr.push({ href: `/experience/book/${slug}`, label: 'Book Now', external: true })
    }
    if (t == 'tour' && slug) {
        arr.push({ href: `/tours/book/${slug}`, label: 'Book Now', external: true })
    }
    if (t == 'event' && slug) {
        arr.push({ href: `/event/${slug}`, label: `Explore Event` })
    }
    if (t == 'listing' && slug) {
        arr.push({ href: `/explore/${cat_slug}/${slug}`, label: `Explore` })
    }
    return arr
})

function gmapsHref() {
    if (!hasGeo.value) return '#'
    const q = `${props.item.lat},${props.item.lng}`
    return `https://www.google.com/maps?q=${encodeURIComponent(q)}`
}
// console.log(props.item)
</script>

<template>
    <div class="relative group overflow-hidden rounded-2xl shadow-2xl bg-black/40 border border-white/10
           backdrop-blur-md isolate ring-1 ring-white/5 transition duration-500
           hover:shadow-[0_10px_40px_-10px_rgba(0,0,0,0.6)] hover:border-white/20">
        <!-- gradient border aura -->
        <div class="pointer-events-none absolute -inset-px rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            style="background: radial-gradient(120% 120% at 100% 0%, rgba(220, 38, 38, .35) 0%, rgba(16,185,129,.35) 35%, rgba(56,189,248,.25) 75%, transparent 100%);">
        </div>

        <!-- Image -->
        <img :src="imgSrc" :alt="title"
            class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />

        <!-- Type pill -->
        <!-- <div class="absolute top-3 left-3 z-30">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                :class="kind.pill">
                <component :is="kind.icon" class="w-4 h-4" />
                {{ kind.label }}
            </span>
        </div> -->

        <!-- Floating heart -->

        <!-- <div class="absolute top-3 right-3 z-40">
            <AddToMyVentureButton :item="{
                id: item.id,
                type: item.type,
                title: title,
                url: url,
                cat_url: cat_url,
                image: imgSrc
            }" iconOnly />
        </div> -->

        <!-- ✅ Tags block (centered, not touching edges) -->
        <div v-if="item.tags?.length && item.item_type === 'town'"
            class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
            <Link v-for="tag in item.tags" :key="tag.id" :href="`/tours#${tag.slug || slugify(tag.name)}`"
                class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
            {{ tag.name }}
            </Link>
        </div>

        <!-- ✅ Tags block (centered, not touching edges) -->
        <div v-if="item.tags?.length && item.item_type === 'experience'"
            class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
            <Link v-for="tag in item.tags" :key="tag.id" :href="`/tours#${tag.slug || slugify(tag.name)}`"
                class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
            {{ tag.name }}
            </Link>
        </div>

        <!-- ✅ Tags block (centered, not touching edges) -->
        <div v-if="item.tags?.length && item.item_type === 'tour'"
            class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
            <Link v-for="tag in item.tags" :key="tag.id" :href="`/tours#${tag.slug || slugify(tag.name)}`"
                class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
            {{ tag.name }}
            </Link>
        </div>

        <!-- ✅ Tags block (centered, not touching edges) -->
        <div v-if="item.tags?.length && item.item_type === 'event'"
            class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
            <Link v-for="tag in item.tags" :key="tag.id" :href="`/events#${tag.slug || slugify(tag.name)}`"
                class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
            {{ tag.name }}
            </Link>
        </div>

        <!-- ✅ Tags block (centered, not touching edges) -->
        <div v-if="item.tags?.length && item.item_type === 'listing'"
            class="absolute top-4 left-4 right-16 flex flex-wrap gap-2 z-30">
            <Link v-for="tag in item.tags" :key="tag.id" :href="`/explore/${cat_url}#${tag.slug || slugify(tag.name)}`"
                class="text-white text-sm font-semibold px-2 py-1 rounded-full tracking-small backdrop-blur-sm bg-black/40 shadow hover:bg-bison hover:text-white transition">
            {{ tag.name }}
            </Link>
        </div>

        <!-- Subtle gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none">
        </div>

        <!-- Content / Hover actions -->
        <div class="absolute inset-0 p-4 md:p-6 flex flex-col justify-end z-20">
            <div class="space-y-2">
                <h3 class="text-white text-xl md:text-2xl font-extrabold tracking-wide drop-shadow-sm">
                    {{ title }}
                </h3>

                <div class="flex flex-wrap gap-2">
                    <!-- Primary CTAs -->
                    <template v-for="(cta, i) in ctas" :key="i">
                        <Link v-if="!cta.external" :href="cta.href" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-heavy
                         hover:bg-envy hover:text-white transition shadow">
                        {{ cta.label }}
                        </Link>
                        <a v-else :href="cta.href" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-heavy
                      hover:bg-envy hover:text-white transition shadow inline-flex items-center gap-1">
                            {{ cta.label }}
                            <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                        </a>
                    </template>

                    <!-- Directions if we have lat/lng -->
                    <a v-if="hasGeo" :href="gmapsHref()" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-full text-xs font-semibold bg-black/40 text-white/90 border border-white/20
                    hover:bg-black/60 transition inline-flex items-center gap-1.5">
                        <MapPinIcon class="w-4 h-4" /> Directions
                    </a>

                    <!-- Remove -->
                    <!-- <button class="ml-auto px-3 py-1.5 rounded-full text-xs font-semibold bg-black/40 text-white/90 border border-white/20
                   hover:border-rose-400 hover:text-rose-200 transition" @click.stop="$emit('remove')"
                        title="Remove from My Venture">
                        Remove
                    </button> -->
                </div>
            </div>
        </div>
    </div>
</template>
