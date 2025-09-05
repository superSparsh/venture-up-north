<script setup>
import { useMyVenture } from '@/composables/useMyVenture';

const props = defineProps({
    item: { type: Object, required: true },
    index: { type: Number, required: true },
    draggingIndex: { type: [Number, null], default: null }
});
const emit = defineEmits(['drag-start', 'drop-on']);
const { remove } = useMyVenture();
</script>

<template>
    <li class="group rounded-xl overflow-hidden border border-white/10 bg-black/30" draggable="true"
        @dragstart="emit('drag-start', index)" @dragover.prevent @drop="emit('drop-on', index)">
        <a :href="item.url" class="flex gap-4 p-4 items-center">
            <img v-if="item.image" :src="item.image" :alt="item.title" class="w-24 h-24 object-cover rounded-lg">
            <div>
                <h3 class="text-xl font-bold group-hover:text-bison transition">{{ item.title }}</h3>
                <p class="text-xs opacity-70">{{ item.type }}</p>
            </div>
        </a>
        <div class="flex justify-end p-3 pt-0">
            <button @click="remove(item)"
                class="text-sm px-3 py-1 rounded-full border border-white/20 hover:border-bison hover:text-bison">
                Remove
            </button>
        </div>
    </li>
</template>
