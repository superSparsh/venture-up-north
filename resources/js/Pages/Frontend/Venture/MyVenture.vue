<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useMyVenture } from '@/Composables/useMyVenture'
import { useVenturePlan } from '@/Composables/useVenturePlan'
import Layout from '@/layouts/FrontendLayout.vue'
import VentureItemTile from '@/Components/Frontend/VentureItemTile.vue'
import ShareVentureModal from '@/Components/Frontend/ShareVentureModal.vue'
import SaveVentureModal from '@/Components/Frontend/SaveVentureModal.vue'

/** ✅ Use the venture store as an object (not destructured) so we can call its methods */
const venture = useMyVenture()
const { items } = venture

const { days, assignment, addDay, deleteDay, renameDay, latestDayId, renumberDays } = useVenturePlan()

const keyFor = (i) => `${i.type}:${i.id}`
const STORAGE_KEY = 'vds.myventure.v1'
const shareOpen = ref(false)

/** Ensure unassigned items go to the latest day */
onMounted(() => {
    const dest = latestDayId()
    items.value.forEach(it => {
        const k = keyFor(it)
        if (assignment[k] == null) assignment[k] = dest
    })
})

watch(items, (nv) => {
    const dest = latestDayId()
    nv.forEach(it => {
        const k = keyFor(it)
        if (assignment[k] == null) assignment[k] = dest
    })
}, { deep: false })

/** Items grouped by day */
const itemsByDay = computed(() => {
    const map = new Map(days.value.map(d => [d.id, []]))
    const fallbackId = days.value[days.value.length - 1]?.id ?? days.value[0]?.id
    items.value.forEach(i => {
        const k = keyFor(i)
        const dId = assignment[k] ?? fallbackId
            ; (map.get(dId) || map.get(fallbackId)).push(i)
    })
    return map
})

/** ✅ Robust remover that persists to localStorage */
const doRemove =
    (typeof venture.remove === 'function' && venture.remove.bind(venture)) ||
    (typeof venture.removeItem === 'function' && venture.removeItem.bind(venture)) ||
    ((it) => {
        const arr = Array.isArray(venture.items?.value) ? venture.items.value : []
        venture.items.value = arr.filter(x => keyFor(x) !== keyFor(it))
        // persist immediately
        // localStorage.setItem(STORAGE_KEY, JSON.stringify(venture.items.value))
    })

function removeItem(it) {
    const k = keyFor(it)
    if (assignment[k] != null) delete assignment[k]
    venture.remove(it)      // store persists
}

/** 🧲 Drag & drop */
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
    // persist items order too
    // localStorage.setItem(STORAGE_KEY, JSON.stringify(items.value))
    dragging.value = { fromDayId: null, index: null, itemKey: null }
}

const isEmpty = computed(() => items.value.length === 0)
const saveOpen = ref(false)

/** Day helpers */
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
    dayItems.forEach(it => venture.remove(it))
    deleteDay(dayId)
    renumberDays(true)
    // If you truly want to wipe everything:
    // venture.clear()
}

</script>

<template>
    <Layout>

        <Head title="My Ventures - Venture Up North" />

        <!-- HERO -->
        <section class="relative w-full h-screen overflow-hidden text-white">
            <img src="/public/images/Venture-Up-North.png" alt="My Ventures"
                class="absolute inset-0 w-full h-full object-cover z-0" />
            <div class="absolute inset-0 bg-black/40 z-10"></div>
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">My Ventures
                    </h1>
                    <p class="mt-3 max-w-2xl text-white/80">Plan your days. Add places via the heart, then organise them
                        here.</p>
                </div>
            </div>
        </section>

        <!-- BODY -->
        <section class="mx-auto px-4 text-white py-10 bg-heavy flex justify-center">

            <div v-if="isEmpty" class="container bg-bison border border-bison rounded-xl p-8 text-center text-heavy">
                <p class="text-lg font-semibold">Your venture is currently empty.</p>
                <p class="opacity-70 mt-2">
                    Browse the site and click “Add to My Venture” (heart) to build your plan.
                </p>
                <div class="mt-6 flex flex-col sm:flex-row justify-center gap-3">
                    <Link href="/"
                        class="px-5 py-2 rounded-full border border-envy hover:border-heavy hover:text-heavy">
                    Find Activities
                    </Link>
                    <Link href="/venture/your"
                        class="px-5 py-2 rounded-full border border-envy hover:border-heavy hover:text-heavy">
                    View Saved Ventures
                    </Link>
                </div>
            </div>


            <!-- Days -->
            <div v-else class="container space-y-8">
                <div class="text-center">
                    <p class="text-2xl tracking-wide font-bold">Your next venture starts here.
                    </p>
                    <p class="mt-3 text-white text-md font-normal tracking-wide">Click ♡ Add to My Venture as you
                        explore, craft your perfect
                        getaway,
                        and
                        share the excitement instantly with friends and family on social or by email.</p>
                </div>
                <!-- <div class="col-md-12 text-end">
                    <button @click="shareOpen = true"
                        class="px-4 py-2 rounded-full border border-white/30 bg-white/10 hover:border-bison hover:text-bison">
                        Share Venture
                    </button>
                </div> -->
                <!-- Day panels -->
                <div v-for="day in days" :key="day.id"
                    class="rounded-2xl bg-grey/30 border border-bison shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="sticky top-0 z-10 px-4 md:px-6 py-4
           backdrop-blur-md
            border-b border-bison">
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

                        <!-- Empty-day hint -->
                        <div v-if="(itemsByDay.get(day.id) || []).length === 0" class="mt-4 p-8 rounded-2xl text-center text-white/70
            border border-dashed border-white/20
            bg-gradient-to-br from-white/5 to-transparent">
                            Drag items here to add them to <span class="font-semibold">{{ day.title }}</span>.
                        </div>
                    </div>

                </div>
                <!-- Add Day -->
                <div class="col-md-12 flex justify-end gap-3 mb-6">
                    <button @click="handleAddDayAfterCurrent"
                        class="px-5 py-3 rounded-2xl text-heavy bg-bison hover:bg-envy hover:text-white"
                        title="Add a new day">
                        + Add Day
                    </button>

                    <button v-if="!isEmpty" @click="saveOpen = true"
                        class="px-4 py-2 rounded-full bg-white/10 border border-white/20 hover:bg-white/20">
                        Save Venture
                    </button>
                </div>
            </div>
        </section>
        <ShareVentureModal v-model="shareOpen" />
        <SaveVentureModal v-model="saveOpen" />
    </Layout>
</template>
