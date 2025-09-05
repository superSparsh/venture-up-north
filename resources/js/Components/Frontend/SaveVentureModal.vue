<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useMyVenture } from '@/Composables/useMyVenture'
import { useVenturePlan } from '@/Composables/useVenturePlan'

const props = defineProps({ modelValue: Boolean })
const emit = defineEmits(['update:modelValue'])

const page = usePage()
const errors = computed(() => page.props.errors || {})

const open = computed({
  get: () => props.modelValue,
  set: v => emit('update:modelValue', v)
})

const name = ref('')
const title = ref('My Venture')
const summary = ref('')
const heroImage = ref(null)
const socialImage = ref(null)

function onHeroImageChange(e) {
  heroImage.value = e.target.files?.[0] || null
}
function onSocialImageChange(e) {
  socialImage.value = e.target.files?.[0] || null
}
const { items } = useMyVenture()
const { days, assignment } = useVenturePlan()
const payload = computed(() => {
  // days → array with index
  const ds = days.value
    .slice() // copy
    .sort((a, b) => a.id - b.id)
    .map((d, idx) => ({ title: d.title || `Day ${idx + 1}`, index: idx + 1 }))

  // map from dayId to index
  const dayIdToIndex = new Map()
  days.value
    .slice()
    .sort((a, b) => a.id - b.id)
    .forEach((d, idx) => dayIdToIndex.set(d.id, idx + 1))

  // items with day_index + position
  const grouped = new Map()
  items.value.forEach((it) => {
    const key = `${it.type}:${it.id}`
    const dayId = assignment[key]
    const idx = dayIdToIndex.get(dayId) || 1
    if (!grouped.has(idx)) grouped.set(idx, [])
    grouped.get(idx).push(it)
  })
  const outItems = []
  for (const [idx, arr] of grouped.entries()) {
    arr.forEach((it, pos) => {
      outItems.push({
        day_index: idx,
        position: pos + 1,
        item_type: it.type,
        item_id: it.id,
        source_url: it.url,
        cat_source_url: it.cat_url,
        title: it.title,
        image_url: it.image || null,
        tags: it.tags || null,
        lat: it.lat ?? null,
        lng: it.lng ?? null,
        payload: { ...(it.payload || {}) }
      })
    })
  }

  return {
    title: title.value,
    owner_guest: { name: name.value || 'Guest' }, // since guest save
    visibility: 'private',
    summary: summary.value,
    heroImage: heroImage.value,
    socialImage: socialImage.value,
    days: ds,
    items: outItems
  }
})

function save() {
  router.post(route('ventures.store'), payload.value, {
    forceFormData: true,
    preserveScroll: true,
    preserveState: true,
    replace: true,
    onSuccess: () => {
      // ✅ Clear all venture-related localStorage
      localStorage.removeItem('vds.myventure.v1')
      localStorage.removeItem('vds.myventure.days.v1')
      localStorage.removeItem('vds.myventure.assign.v1')

      // Optionally reset your composables too
      venture.items.value = []
      days.value = []
      Object.keys(assignment).forEach(k => delete assignment[k])

      open.value = false
    },
    onError: () => {
      open.value = true
    },
  })
}

</script>

<template>
  <transition name="fade">
    <div v-if="open" class="fixed inset-0 z-[999] grid place-items-center">
      <div class="absolute inset-0 bg-black/60" @click="open = false"></div>
      <div class="relative w-full max-w-lg rounded-2xl bg-heavy border border-white/10 p-6 text-white">
        <h3 class="text-xl font-bold mb-4">Save your Venture</h3>

        <!-- Top error banner -->
        <div v-if="Object.keys(errors).length"
          class="mb-4 rounded-lg border border-rose-400/40 bg-rose-500/10 px-3 py-2 text-rose-200 text-sm">
          <strong>Fix the following:</strong>
          <ul class="list-disc list-inside mt-1">
            <li v-for="(msg, key) in errors" :key="key">{{ msg }}</li>
          </ul>
        </div>

        <!-- Name -->
        <label class="block text-sm mb-2">Your name</label>
        <input v-model="name" class="w-full mb-4 rounded-lg bg-black/30 border border-white/10 p-2"
          placeholder="Your name" />

        <!-- Title -->
        <label class="block text-sm mb-2">Venture title</label>
        <input v-model="title" class="w-full mb-6 rounded-lg bg-black/30 border border-white/10 p-2"
          placeholder="Eg. Augusta Weekend" />

        <!-- Summary -->
        <label class="block text-sm mb-2">Summary</label>
        <textarea v-model="summary" class="w-full mb-6 rounded-lg bg-black/30 border border-white/10 p-2"
          placeholder="Eg. This venture.."></textarea>

        <!-- Hero Image -->
        <label class="block text-sm mb-2">Hero Image Minimum image size:
          738×500px (width × height)</label>
        <input type="file" @change="onHeroImageChange" accept="image/*"
          class="w-full mb-6 rounded-lg bg-black/30 border border-white/10 p-2 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-bison file:text-heavy hover:file:bg-white" />

        <!-- Social Image -->
        <label class="block text-sm mb-2">Social Image (Minimum image size:
          1200×630px (width × height))</label>
        <input type="file" @change="onSocialImageChange" accept="image/*"
          class="w-full mb-6 rounded-lg bg-black/30 border border-white/10 p-2 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-bison file:text-heavy hover:file:bg-white" />

        <!-- Actions -->
        <div class="flex justify-end gap-3">
          <button class="px-4 py-2 rounded-full border border-white/20" @click="open = false">
            Cancel
          </button>
          <button class="px-4 py-2 rounded-full bg-bison text-heavy hover:bg-white" @click="save">
            Save Venture
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>
