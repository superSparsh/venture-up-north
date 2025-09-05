<script setup>
import { computed } from 'vue'
import { useMyVenture } from '@/Composables/useMyVenture'
import { HeartIcon as HeartOutline } from '@heroicons/vue/24/outline'
import { HeartIcon as HeartSolid } from '@heroicons/vue/24/solid'

const props = defineProps({
    item: { type: Object, required: true },
    iconOnly: { type: Boolean, default: false }
})

// get whatever the store provides (names may vary across your files)
const store = useMyVenture()

// robust key
const keyFor = (i) => `${i.type}:${i.id}`

// selected → prefer store.has if present; else compute from items
const selected = computed(() => {
    if (typeof store.has === 'function') return store.has(props.item)
    const arr = store.items?.value ?? []
    return Array.isArray(arr) && arr.some(i => keyFor(i) === keyFor(props.item))
})

// unified click handler that tries toggle, then add/remove, then addItem/removeItem
function onClick() {
    if (typeof store.toggle === 'function') {
        store.toggle(props.item)
        return
    }

    const isSelected = selected.value

    if (isSelected) {
        if (typeof store.remove === 'function') { store.remove(props.item); return }
        if (typeof store.removeItem === 'function') { store.removeItem(props.item); return }
    } else {
        if (typeof store.add === 'function') { store.add(props.item); return }
        if (typeof store.addItem === 'function') { store.addItem(props.item); return }
    }

    // last resort: mutate items array directly (only if store has items ref)
    if (store.items?.value) {
        if (isSelected) {
            store.items.value = store.items.value.filter(i => keyFor(i) !== keyFor(props.item))
        } else {
            store.items.value.push(props.item)
        }
    } else {
        console.error('[AddToMyVentureButton] No toggle/add/remove methods found on useMyVenture()')
    }
}
</script>

<template>
    <button type="button" @click.stop.prevent="onClick"
        class="flex items-center justify-center rounded-full p-2 transition shadow-lg font-bold" :class="[
            iconOnly ? 'w-10 h-10' : 'gap-2 px-3 py-2',
            selected ? 'bg-bison text-heavy' : 'bg-heavy text-white hover:bg-bison hover:text-heavy'
        ]" :aria-pressed="selected">
        <component :is="selected ? HeartSolid : HeartOutline" class="w-5 h-5" />
        <span v-if="!iconOnly" class="text-sm font-semibold">
            {{ selected ? 'Added' : 'Add to My Venture' }}
        </span>
    </button>
</template>
