<script setup>
import { useMyVenture } from '@/Composables/useMyVenture';
import { useVenturePlan } from '@/Composables/useVenturePlan'
const props = defineProps({ findUrl: { type: String, default: '/explore' } });
const { clear, exportPayload } = useMyVenture()
const { addDay, renumberDays } = useVenturePlan()
function copyShareLink() {
    const url = `${window.location.origin}/venture/share?i=${exportPayload()}`;
    navigator.clipboard.writeText(url);
    alert('Share link copied!');
}
function onAddDay () {
  addDay()
  renumberDays(true)
}
</script>

<template>
    <div class="flex flex-wrap gap-3 mb-6">
        <!-- <a :href="findUrl" class="px-4 py-2 rounded-full border border-heavy hover:border-bison hover:text-bison text-heavy">
            Find Activities
        </a> -->
        <button @click="onAddDay"
            class="px-4 py-2 rounded-full border border-heavy bg-white/10 hover:border-bison hover:text-bison text-heavy">
            + Add Day
        </button>
        <button @click="copyShareLink"
            class="px-4 py-2 rounded-full border border-heavy hover:border-bison hover:text-bison text-heavy">
            Share
        </button>
        <button @click="clear"
            class="px-4 py-2 rounded-full border border-heavy hover:border-bison hover:text-bison text-heavy">
            Clear
        </button>
    </div>
</template>
