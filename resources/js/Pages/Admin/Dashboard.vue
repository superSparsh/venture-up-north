<template>

    <Head title="Admin Dashboard" />

    <AdminLayout>
        <h1 class="text-2xl font-bold mb-4">Welcome to Admin Dashboard</h1>
        <p class="text-gray-600">Manage homepage, content, and contributors.</p>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from './Layout.vue'
import { Head } from '@inertiajs/vue3';
</script>