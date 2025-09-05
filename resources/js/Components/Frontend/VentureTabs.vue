<script setup>
import { Link } from '@inertiajs/vue3'
const props = defineProps({ active: { type: String, required: true } })
const tabs = [
    { key: 'community', label: 'Community Ventures', href: '/venture/community' },
    { key: 'our', label: 'Our Ventures', href: '/venture/ours' },
    { key: 'your', label: 'Your Ventures', href: '/venture/your' },
]
</script>

<template>
    <div class="flex flex-wrap gap-2 mt-5">
        <Link v-for="t in tabs" :key="t.key" :href="t.href" class="px-4 py-2 rounded-full border transition" :class="t.key === active
            ? 'bg-bison text-heavy border-bison'
            : 'bg-heavy text-white border-white/20 hover:bg-bison hover:text-heavy'">
        {{ t.label }}
        </Link>
    </div>
</template>