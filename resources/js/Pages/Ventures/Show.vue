<script setup>
import { computed, ref, watchEffect } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import ShareLinkModal from '@/Components/Frontend/ShareLinkModal.vue'
import VentureItemTileStored from '@/Components/Frontend/VentureItemStored.vue'

// Controller should pass a flattened venture with days + items
// venture.days: [{id, title, index}...]
// venture.items: [{id, venture_day_id, position, item_type, item_id, source_url, title, image_url, tags, lat, lng }]
const props = defineProps({
    venture: { type: Object, required: true },
    isOwner: { type: Boolean, default: false },
})

const shareOpen = ref(false)

// Group items by day id (ordered by venture.days.index), read-only
// const daysOrdered = computed(() => {
//     const arr = (props.venture.days || []).slice().sort((a, b) => (a.index || 0) - (b.index || 0))
//     return arr
// })

// const itemsByDay = computed(() => {
//     const map = new Map()
//     daysOrdered.value.forEach(d => map.set(d.id, []))
//         ; (props.venture.items || [])
//             .slice()
//             .sort((a, b) => (a.venture_day_id - b.venture_day_id) || (a.position - b.position))
//             .forEach(it => {
//                 const bucket = map.get(it.venture_day_id)
//                 if (bucket) bucket.push(it)
//             })
//     return map
// })

const coverImg = computed(() => {
    const raw = props.venture?.cover_image_url
    if (!raw) {
        return '/public/images/Venture-Up-North.png'
    }

    // Already full URL
    if (/^https?:\/\//i.test(raw)) {
        return raw
    }

    // Already has /public/storage
    if (raw.startsWith('/public/storage/')) {
        return raw
    }

    // Normalize relative storage path
    return `/public/storage/${raw.replace(/^\/+/, '')}`
})

const norm = v => String(v ?? '')

const daysOrdered = computed(() => {
    return (props.venture.days || [])
        .slice()
        .sort((a, b) => (Number(a.index ?? 0) - Number(b.index ?? 0)))
})

const itemsByDay = computed(() => {
    const map = new Map()
    daysOrdered.value.forEach(d => map.set(norm(d.id), []))

        ; (props.venture.items || [])
            .slice()
            .sort((a, b) =>
                Number(a.venture_day_id) - Number(b.venture_day_id) ||
                Number(a.position) - Number(b.position)
            )
            .forEach(it => {
                const bucket = map.get(norm(it.venture_day_id))
                if (bucket) bucket.push(it)
            })

    return map
})

const shareUrl = computed(() => {
    if (typeof window === 'undefined') return ''
    return window.location.href
})


function goBack() {
    window.history.back();
}


const isOwner = computed(() => props.isOwner)


const editOpen = ref(false)
const saving = ref(false)

const editForm = ref({
    title: props.venture.title || '',
    summary: props.venture.summary || '',
    heroFile: null,
    socialFile: null,
})

// image previews (optional)
const heroPreview = ref(null)
const socialPreview = ref(null)

function onHeroChange(e) {
    const f = e.target.files?.[0] || null
    editForm.value.heroFile = f
    heroPreview.value = f ? URL.createObjectURL(f) : null
}
function onSocialChange(e) {
    const f = e.target.files?.[0] || null
    editForm.value.socialFile = f
    socialPreview.value = f ? URL.createObjectURL(f) : null
}

const page = usePage()
const errors = computed(() => {
    const p = page?.props
    const propsObj = p && typeof p.value !== 'undefined' ? p.value : p
    return propsObj?.errors ?? {}
})

</script>

<template>
    <Layout>

        <Head :title="`${venture.title} - Venture Up North`" />

        <section class="relative w-full h-screen overflow-hidden text-white">
            <img :src="coverImg" :alt="venture.title" class="absolute inset-0 w-full h-full object-cover z-0" />
            <div class="absolute inset-0 bg-black/40 z-10"></div>
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">{{
                        venture.title }}
                    </h1>
                    <div class="mt-3 flex flex-wrap gap-2 text-md">
                        <span class="px-2 py-1 rounded-full bg-white/10 border border-white/10">
                            {{ venture.days_count || venture.days?.length || 0 }} days
                        </span>
                        <span v-if="venture.items_count"
                            class="px-2 py-1 rounded-full bg-white/10 border border-white/10">
                            {{ venture.items_count }} items
                        </span>
                    </div>
                </div>
            </div>
        </section>
        <!-- Description Section -->
        <section class="container mx-auto px-4 mb-16" data-aos="fade-up">
            <div class="component component-text component-text-introduction lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0">
                <div class="text-content mt-8 lg:mt-10 text-xl text-gray-700 font-medium">
                    <p class="text-lg text-bison leading-relaxed max-w-3xl font-bold tracking-wide">
                        {{ venture.summary }}
                    </p>
                </div>
            </div>
        </section>

        <section class="mx-auto px-4 text-white py-10 bg-heavy flex justify-center">
            <!-- BODY: read-only days + items -->
            <section class="container space-y-8">
                <div class="flex justify-between items-center mb-6">
                    <!-- Creator -->
                    <div class="flex items-center gap-2">
                        <!-- If you ever add avatar -->
                        <!-- <img src="/path/to/avatar.jpg" alt="Creator" class="w-8 h-8 rounded-full" /> -->
                        <div>
                            <p class="text-sm text-white font-semibold">By</p>
                            <p class="text-md font-bold text-bison">
                                {{ venture.type === 'venture' ? 'Admin' : venture.owner_guest_name }}
                            </p>
                        </div>
                    </div>

                    <!-- Share + Back -->
                    <div class="flex gap-3">
                        <button v-if="isOwner" @click="editOpen = true" class="px-4 py-2 rounded-full border ...">
                            Edit
                        </button>
                        <button @click="goBack"
                            class="px-4 py-2 rounded-full border border-white/30 bg-white/10 hover:border-bison hover:text-bison"
                            title="Go Back">
                            ← Back
                        </button>

                        <button @click="shareOpen = true"
                            class="px-4 py-2 rounded-full border border-white/30 bg-white/10 hover:border-bison hover:text-bison"
                            title="Share this Venture">
                            Share
                        </button>
                    </div>
                </div>

                <div v-for="day in daysOrdered" :key="day.id"
                    class="rounded-2xl bg-grey/30 border border-bison shadow-lg overflow-hidden">
                    <!-- Day header (no controls) -->
                    <div class="sticky top-0 z-10 px-4 md:px-6 py-4
           backdrop-blur-md
            border-b border-bison">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="text-xl md:text-2xl font-extrabold tracking-widest uppercase hover:text-bison text-bison transition tracking-wider">
                                    {{ day.title || `Day ${day.index}` }}
                                </div>
                                <span class="inline-flex items-center justify-center text-xs font-semibold px-2 py-1 rounded-full
                   bg-bison ring-1 ring-inset ring-white/15 text-heavy">
                                    ({{ (itemsByDay.get(String(day.id)) || []).length }} items)
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Items (read-only, no heart/no remove) -->
                    <div class=" p-4 md:p-6">
                        <ul class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                            <li v-for="it in (itemsByDay.get(String(day.id)) || [])"
                                :key="`d${day.id}:${it.item_type}:${it.item_id}:${it.id}`">
                                <VentureItemTileStored :item="it" />
                            </li>

                        </ul>

                        <!-- Empty day -->
                        <div v-if="(itemsByDay.get(String(day.id)) || []).length === 0" class="...">
                            No items for <span class="font-semibold">{{ day.title || `Day ${day.index}` }}</span>.
                        </div>
                    </div>
                </div>
            </section>
        </section>

        <!-- Share modal with current page URL -->
        <ShareLinkModal v-model="shareOpen" :share-url="shareUrl" :title="venture.title" />

        <!-- Edit Venture Modal -->
        <transition name="fade">
            <div v-if="editOpen" class="fixed inset-0 z-[999] grid place-items-center p-4 sm:p-6">
                <div class="absolute inset-0 bg-black/60" @click="editOpen = false"></div>

                <div class="relative z-10 w-full max-w-lg rounded-2xl bg-heavy border border-white/10 p-6 text-white
                max-h-[90vh] overflow-y-auto">
                    <h3 class="text-xl font-bold mb-4">Edit Venture</h3>

                    <!-- Errors -->
                    <div v-if="Object.keys(errors).length"
                        class="mb-4 rounded-lg border border-rose-400/30 bg-rose-400/10 p-3 text-rose-200 text-sm">
                        <ul class="list-disc ml-5 space-y-1">
                            <li v-for="(msg, key) in errors" :key="key">{{ msg }}</li>
                        </ul>
                    </div>

                    <label class="block text-sm mb-2">Title</label>
                    <input v-model="editForm.title"
                        class="w-full mb-4 rounded-lg bg-black/30 border border-white/10 p-2"
                        placeholder="Trip title" />

                    <label class="block text-sm mb-2">Summary</label>
                    <textarea v-model="editForm.summary"
                        class="w-full mb-6 rounded-lg bg-black/30 border border-white/10 p-2"
                        placeholder="Short summary"></textarea>

                    <label class="block text-sm mb-2">Hero Image (min 738×500)</label>
                    <input type="file" accept="image/*" @change="onHeroChange"
                        class="w-full mb-2 rounded-lg bg-black/30 border border-white/10 p-2 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-bison file:text-heavy hover:file:bg-white" />
                    <img v-if="heroPreview" :src="heroPreview"
                        class="w-full h-40 object-cover rounded-lg mb-6 border border-white/10" alt="Hero preview" />

                    <label class="block text-sm mb-2">Social Image (min 1200×630)</label>
                    <input type="file" accept="image/*" @change="onSocialChange"
                        class="w-full mb-2 rounded-lg bg-black/30 border border-white/10 p-2 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-bison file:text-heavy hover:file:bg-white" />
                    <img v-if="socialPreview" :src="socialPreview"
                        class="w-full h-40 object-cover rounded-lg mb-6 border border-white/10" alt="Social preview" />
                </div>
            </div>
        </transition>

    </Layout>
</template>
