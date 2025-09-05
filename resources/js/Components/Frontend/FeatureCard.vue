<template>
    <div class="p-6 bg-white rounded-2xl shadow hover:shadow-md transition flex justify-content-center items-center">
        <h3 class="text-lg font-semibold mb-2">{{ title }}</h3>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    description: String
})
</script>