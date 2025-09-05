<script setup>
import { onMounted } from 'vue';
import { useMyVenture } from '@/Composables/useMyVenture';
import { router } from '@inertiajs/vue3';

const { importPayload } = useMyVenture();

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const b64 = params.get('i') || '';
    if (b64) importPayload(b64);
    router.visit('/my-venture', { replace: true });
});
</script>

<template>
    <div class="container mx-auto px-4 py-10 text-white">
        <p>Loading shared venture…</p>
    </div>
</template>
