<script setup>
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Access Denied" />
    <section class="min-h-screen flex flex-col items-center justify-center text-center bg-white px-4">
        <img src="/public/images/403" alt="403" class="max-w-md mb-8" />
        <Link href="/" class="bg-bison text-heavy px-6 py-2 rounded font-semibold hover:bg-envy transition">
        Return to Home
        </Link>
    </section>
</template>
