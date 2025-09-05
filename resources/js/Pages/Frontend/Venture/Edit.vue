<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import VentureItemTile from '@/Components/Frontend/VentureItemTile.vue'
import ShareVentureModal from '@/Components/Frontend/ShareVentureModal.vue'


// Your existing composables
import { useMyVenture } from '@/Composables/useEditMyVenture'
import { useVenturePlan } from '@/Composables/useEditVenturePlan'

// Props from controller
const props = defineProps({
    venture: { type: Object, required: true },
    seo: { type: Object, required: true },
})

const page = usePage()
const errors = computed(() => page.props.errors || {})

// Store-like composables
const venture = useMyVenture()
const { items } = venture
const { days, assignment, addDay, deleteDay, renameDay, latestDayId, renumberDays, resetPlan } = useVenturePlan()

// Local UI state
const shareOpen = ref(false)
const saveLoading = ref(false)

// ---------- Bootstrap stores from server ----------
/**
 * We normalize incoming items into the same shape your store expects:
 * - key: type+id
 * - day assignment by day_index
 */
function toInt(v, def = null) {
    const n = Number(v)
    return Number.isFinite(n) ? n : def
}

function hydrateFromServer() {
    if (typeof resetPlan === 'function') resetPlan()

    // 1) Days (ensure numeric index)
    const incomingDays = Array.isArray(props.venture.days) ? props.venture.days : []
    const normalizedDays = incomingDays
        .slice()
        .sort((a, b) => toInt(a.index, 0) - toInt(b.index, 0))
        .map((d, i) => ({ id: i + 1, title: d.title || `Day ${i + 1}` }))

    days.value = normalizedDays

    // 2) Items
    const incomingItems = Array.isArray(props.venture.items) ? props.venture.items : []
    items.value = incomingItems.map(it => ({
        id: it.item_id,
        type: it.item_type,
        title: it.title,
        image: it.image_url,
        source_url: it.source_url,
        cat_source_url: it.cat_source_url,
        lat: it.lat,
        lng: it.lng,
        payload: it.payload,
        // keep raw indices around just in case
        _day_index: it.day_index,
        _venture_day_id: it.venture_day_id,
    }))

    // 3) Build a string-keyed map: "1" -> local day id
    const dayIndexToLocalId = new Map(
        normalizedDays.map((d, idx) => [String(idx + 1), d.id])
    )

    // 4) Assignment: prefer day_index; fall back to venture_day_id
    const lastLocalDayId = normalizedDays[normalizedDays.length - 1]?.id
    incomingItems.forEach(it => {
        const k = `${it.item_type}:${it.item_id}`
        const di = toInt(it.day_index, null)
        const vdi = toInt(it.venture_day_id, null)

        const localDayId =
            (di != null ? dayIndexToLocalId.get(String(di)) : undefined) ??
            (vdi != null ? dayIndexToLocalId.get(String(vdi)) : undefined) ??
            lastLocalDayId

        // Always set something so we don’t fall back later for the whole list
        if (localDayId != null) assignment[k] = localDayId
    })
}
onMounted(() => {
    hydrateFromServer()
})

// Helpers
const keyFor = (i) => `${i.type}:${i.id}`

const itemsByDay = computed(() => {
    const map = new Map(days.value.map(d => [d.id, []]))
    const firstId = days.value[0]?.id ?? null
    items.value.forEach(i => {
        const k = `${i.type}:${i.id}`
        const dId = assignment[k] ?? firstId
            ; (map.get(dId) || (firstId != null ? map.get(firstId) : [])).push(i)
    })
    return map
})

function removeItem(it) {
    const k = keyFor(it)
    if (assignment[k] != null) delete assignment[k]
    venture.remove?.(it) ?? (venture.removeItem?.(it) ?? (() => {
        items.value = items.value.filter(x => keyFor(x) !== k)
    }))()
}

const dragging = ref({ fromDayId: null, index: null, itemKey: null })
function onDragStart(dayId, index, it) {
    dragging.value = { fromDayId: dayId, index, itemKey: keyFor(it) }
}
function onDropOnDay(targetDayId) {
    const drag = dragging.value
    if (!drag.itemKey) return
    assignment[drag.itemKey] = targetDayId
    dragging.value = { fromDayId: null, index: null, itemKey: null }
}
function onDropWithinDay(targetDayId, targetIndex) {
    const drag = dragging.value
    if (!drag.itemKey) return
    assignment[drag.itemKey] = targetDayId
    const arr = [...(itemsByDay.value.get(targetDayId) || [])]
    const currentIdx = arr.findIndex(x => keyFor(x) === drag.itemKey)
    const [moved] = arr.splice(currentIdx, 1)
    const idx = Math.max(0, Math.min(targetIndex, arr.length))
    arr.splice(idx, 0, moved)
    const dayKeys = new Set(arr.map(keyFor))
    const rest = items.value.filter(x => !dayKeys.has(keyFor(x)))
    items.value = [...rest, ...arr]
    dragging.value = { fromDayId: null, index: null, itemKey: null }
}

function handleAddDayAfterCurrent() {
    addDay()
    renumberDays(true)
}
function handleRenameDay(day) {
    const v = prompt('Rename day', day.title)
    if (v) { renameDay(day.id, v); renumberDays(true) }
}
function handleDeleteDay(dayId) {
    const dayItems = itemsByDay.value.get(dayId) || []
    dayItems.forEach(it => removeItem(it))
    deleteDay(dayId)
    renumberDays(true)
}

// ---------- Submit (PUT) ----------
/**
 * Build payload:
 * - title/summary (editable here, basic example uses prompt or inputs; wire up as needed)
 * - days: [{title, index}]
 * - items: [{item_type, item_id, title?, position, day_index, image_url?, ...}]
 */
const formTitle = ref(props.venture.title || '')
const formName = ref(props.venture.owner_guest_name || '')
const formSummary = ref(props.venture.summary || '')
const heroFile = ref(null)
const socialFile = ref(null)

function buildPayload() {
    // map local days to 1..N indexes
    const sortedDays = days.value.map((d, i) => ({ title: d.title, index: i + 1 }))

    // reverse map title->index for quick lookup
    const dayIdToIndex = new Map(days.value.map((d, i) => [d.id, i + 1]))

    // items order per day becomes position 1..N
    const itemsPayload = []
    days.value.forEach((d) => {
        const di = dayIdToIndex.get(d.id)
        const arr = (itemsByDay.value.get(d.id) || [])
        arr.forEach((it, idx) => {
            itemsPayload.push({
                item_type: it.type,
                item_id: it.id,
                title: it.title ?? null,
                position: idx + 1,
                day_index: di,
                source_url: it.source_url ?? null,
                cat_source_url: it.cat_source_url ?? null,
                image_url: it.image ?? null,
                tags: it.tags ?? null,
                lat: it.lat ?? null,
                lng: it.lng ?? null,
                payload: it.payload ?? null,
            })
        })
    })

    const fd = new FormData()
    fd.append('title', formTitle.value)
    fd.append('owner_guest[name]', formName.value || '')
    fd.append('summary', formSummary.value ?? '')
    fd.append('days', JSON.stringify(sortedDays))
    fd.append('items', JSON.stringify(itemsPayload))
    if (heroFile.value) fd.append('heroImage', heroFile.value)
    if (socialFile.value) fd.append('socialImage', socialFile.value)

    // Laravel’s validator expects arrays; if you prefer JSON, decode server-side:
    // $validated['days'] = json_decode($request->input('days'), true);
    // $validated['items'] = json_decode($request->input('items'), true);
    return fd
}

function onHeroChange(e) { heroFile.value = e.target.files?.[0] ?? null }
function onSocialChange(e) { socialFile.value = e.target.files?.[0] ?? null }

async function save() {
    saveLoading.value = true
    const fd = buildPayload()
    router.visit(route('ventures.update', props.venture.slug), {
        method: 'post',
        data: fd,
        forceFormData: true,
        onFinish: () => { saveLoading.value = false },
        preserveScroll: true,
    })
}

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

</script>

<template>
    <Layout>

        <Head :title="seo.title" />

        <!-- HERO -->
        <section class="relative w-full h-screen overflow-hidden text-white">
            <img :src="coverImg" :alt="venture.title" class="absolute inset-0 w-full h-full object-cover z-0" />
            <div class="absolute inset-0 bg-black/40 z-10"></div>
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                        Edit Venture
                    </h1>
                    <p class="mt-3 max-w-2xl text-white/80">Reorder your days and places, update images, then save.</p>
                </div>
            </div>
        </section>

        <!-- BODY -->
        <section class="mx-auto px-4 text-white py-10 bg-heavy flex justify-center">
            <div class="container space-y-8">
                <div class="col-md-12 flex justify-end gap-3 mb-6">
                    <a :href="route('ventures.show', { slug: props.venture.slug })"
                        class="px-4 py-2 rounded-full bg-white/10 border border-white/20 hover:bg-white/20">
                        Preview Venture
                    </a>
                </div>


                <!-- Basic fields -->
                <div class="border border-bison rounded-2xl p-5 text-heavy grid md:grid-cols-2 gap-4">
                    <!-- Top error banner -->
                    <div v-if="Object.keys(errors).length"
                        class="mb-4 rounded-lg border border-rose-400/40 bg-rose-500/10 px-3 py-2 text-rose-200 text-sm">
                        <strong>Fix the following:</strong>
                        <ul class="list-disc list-inside mt-1">
                            <li v-for="(msg, key) in errors" :key="key">{{ msg }}</li>
                        </ul>
                    </div>
                    <div>
                        <label class="block text-md text-bison mb-1">Title</label>
                        <input v-model="formTitle"
                            class="w-full rounded-xl bg-white/70 px-3 py-2 text-md text-heavy border border-bison" />
                    </div>
                    <div>
                        <label class="block text-md text-bison mb-1">Name</label>
                        <input v-model="formName"
                            class="w-full rounded-xl bg-white/70 px-3 py-2 text-md text-heavy border border-bison" />
                    </div>

                    <div>
                        <label class="block text-md text-bison mb-1">
                            Hero Image (738×500)
                            <span v-if="props.venture.cover_image_url" class="block text-sm font-normal text-white mb-3">
                                You can keep this image or upload a new one
                            </span>
                        </label>
                        <input type="file" accept="image/*" @change="onHeroChange"
                            class="w-full text-md text-bison border-bison" />

                        <div v-if="props.venture.cover_image_url" class="mt-2">
                            <img :src="`/public/storage/${props.venture.cover_image_url}`"
                                class="h-24 rounded shadow" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-md text-bison mb-1">
                            Social Image (1200×630)
                            <span v-if="props.venture.og_image_url" class="block text-sm font-normal text-white mb-3">
                                You can keep this image or upload a new one
                            </span>
                        </label>
                        <input type="file" accept="image/*" @change="onSocialChange"
                            class="w-full text-md text-bison border-bison" />

                        <div v-if="props.venture.og_image_url" class="mt-2">
                            <img :src="`/public/storage/${props.venture.og_image_url}`"
                                class="h-24 rounded shadow" />
                        </div>
                    </div>


                    <div class="col-span-2">
                        <label class="block text-md text-bison mb-1">Summary</label>
                        <textarea v-model="formSummary"
                            class="w-full rounded-xl bg-white/70 px-3 py-2 text-md text-heavy border-bison"></textarea>
                    </div>
                </div>

                <!-- Days -->
                <div v-for="day in days" :key="day.id"
                    class="rounded-2xl bg-grey/30 border border-bison shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="sticky top-0 z-10 px-4 md:px-6 py-4 backdrop-blur-md border-b border-bison">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <button @click="handleRenameDay(day)"
                                    class="text-xl md:text-2xl font-extrabold tracking-widest uppercase hover:text-bison text-bison transition tracking-wider"
                                    title="Rename day">
                                    {{ day.title }}
                                </button>
                                <span class="inline-flex items-center justify-center text-xs font-semibold px-2 py-1 rounded-full
                             bg-bison ring-1 ring-inset ring-white/15 text-heavy">
                                    {{ (itemsByDay.get(day.id) || []).length }} items
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="() => handleDeleteDay(day.id)"
                                    class="px-3 py-1 rounded-full border border-bison hover:border-rose-400 hover:text-rose-200 text-md text-bison tracking-wider font-bold">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="p-4 md:p-6" @dragover.prevent @drop="onDropOnDay(day.id)">
                        <ul class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                            <li v-for="(it, idx) in (itemsByDay.get(day.id) || [])" :key="`${it.type}:${it.id}`"
                                class="cursor-grab active:cursor-grabbing" draggable="true"
                                @dragstart="onDragStart(day.id, idx, it)" @dragover.prevent
                                @drop="onDropWithinDay(day.id, idx)" aria-grabbed="false">
                                <VentureItemTile :item="it" @remove="removeItem(it)" />
                            </li>
                        </ul>

                        <div v-if="(itemsByDay.get(day.id) || []).length === 0" class="mt-4 p-8 rounded-2xl text-center text-white/70 border border-dashed border-white/20
                        bg-gradient-to-br from-white/5 to-transparent">
                            Drag items here to add them to <span class="font-semibold">{{ day.title }}</span>.
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 mb-6">
                    <button @click="handleAddDayAfterCurrent"
                        class="px-5 py-3 rounded-2xl text-heavy bg-bison hover:bg-envy hover:text-white"
                        title="Add a new day">
                        + Add Day
                    </button>
                    <button :disabled="saveLoading" @click="save"
                        class="px-5 py-3 rounded-2xl border border-white/30 bg-white/10 hover:bg-white/20">
                        <span v-if="!saveLoading">Save Changes</span>
                        <span v-else>Saving…</span>
                    </button>
                </div>
            </div>

            <ShareVentureModal v-model="shareOpen" />
        </section>
    </Layout>
</template>
