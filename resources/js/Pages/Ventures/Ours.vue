<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import VentureCard from '@/Components/Frontend/VentureCard.vue'
import Layout from '@/layouts/FrontendLayout.vue'
import VentureTabs from '@/Components/Frontend/VentureTabs.vue'

const props = defineProps({
    ventures: Object,
    filters: Object
})

function submit(e) {
    e.preventDefault()

    const fd = new FormData(e.target)
    const filters = Object.fromEntries(fd)

    router.get(route('ventures.ours'), filters, {
        preserveScroll: true,
        preserveState: true,
        replace: true
    })
}

import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
</script>

<template>
    <Layout>

        <Head title="Our Ventures - Venture Up North" />

        <!-- HERO -->
        <section class="relative w-full h-screen overflow-hidden text-white">
            <img src="/public/images/venture.jpeg" alt="Our Ventures"
                class="absolute inset-0 w-full h-full object-cover z-0" />
            <div class="absolute inset-0 bg-black/40 z-10"></div>
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">Our Ventures
                    </h1>
                    <p class="mt-3 max-w-2xl text-white/80">Browse ventures created by us.</p>
                </div>
            </div>
        </section>

        <section class="container mx-auto px-4 py-8 text-heavy space-y-6 mb-5">
            <VentureTabs active="our" />
            <!-- Filters -->
            <form class="bg-heavy border border-bison rounded-2xl p-4 grid gap-3 md:grid-cols-2" @submit="submit">
                <input name="q" :value="filters.q" placeholder="Search title"
                    class="rounded-xl bg-bison border border-bison px-3 py-2" />
                <!-- <input name="min_days" :value="filters.min_days" type="number" min="1" placeholder="Min days"
                    class="rounded-xl bg-bison border border-bison px-3 py-2" />
                <input name="max_days" :value="filters.max_days" type="number" min="1" placeholder="Max days"
                    class="rounded-xl bg-bison border border-bison px-3 py-2" /> -->
                <!-- <Multiselect v-model="filters.sort" :options="[
                    { id: 'recent', name: 'Most recent' },
                    { id: 'popular', name: 'Most items' }
                ]" placeholder="Sort by" label="name" track-by="id" :multiple="false"
                    class="w-full rounded-xl bg-bison border border-bison" /> -->

                <div class=" flex justify-end gap-2">
                    <button type="submit"
                        class="px-4 py-2 rounded-full bg-bison text-heavy hover:bg-white">Apply</button>
                    <Link :href="route('ventures.ours')"
                        class="text-bison px-4 py-2 rounded-full border border-white/20">
                    Reset</Link>
                </div>
            </form>

            <div v-if="!ventures.data.length" class="bg-white/5 border border-white/10 rounded-xl p-8 text-center">
                <p class="text-lg font-semibold">No ventures match your filters.</p>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <VentureCard v-for="v in ventures.data" :key="v.id" :venture="v" :to="v.type === 'venture'
                    ? `/collection/${v.slug}`  
                    : `/ventures/${v.slug}`" />
            </div>

            <!-- Pagination -->
            <div v-if="ventures.links?.length" class="mt-8 flex flex-wrap gap-2 justify-center">
                <Link v-for="l in ventures.links" :key="l.label" :href="l.url || '#'"
                    class="px-3 py-1.5 rounded border border-white/20"
                    :class="[{ 'bg-bison text-heavy': l.active }, !l.url && 'opacity-40 pointer-events-none']"
                    v-html="l.label" />
            </div>
        </section>
    </Layout>
</template>
