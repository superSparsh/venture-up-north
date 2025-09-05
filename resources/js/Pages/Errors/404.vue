<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
</script>

<template>
    <Head title="Page Not Found" />
    <section class="min-h-screen flex flex-col items-center justify-center text-center bg-white px-4">
        <img src="/public/images/404.png" alt="404" class="max-w-md mb-8" />
        <Link href="/" class="bg-bison text-heavy px-6 py-2 rounded font-semibold hover:bg-envy transition">
        Back to Home
        </Link>
    </section>
</template>
