<template>
    <Layout>

        <Head title="Upcoming Features - Venture Up North" />

        <section class="relative w-full h-screen overflow-hidden text-white">
            <!-- Background Image -->
            <img src="/public/images/footer-bg.jpg" alt="Sitemap"
                class="absolute inset-0 w-full h-full object-cover z-0" />

            <!-- Dark overlay (optional for better text readability) -->
            <div class="absolute inset-0 bg-black/40 z-10"></div>

            <!-- Content Wrapper -->
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                        What's Coming to Venture Up North
                    </h1>
                    <p class="mt-4 text-white text-lg md:text-xl font-light">We're launching powerful new features to
                        make
                        exploring the South West even
                        better.</p>
                </div>
            </div>
        </section>
        <section class="container mx-auto px-4 py-16">
            <div class="px-4 md:px-12 py-12 max-w-7xl mx-auto">
                <!-- Stage 3 -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-6 border-l-4 border-yellow-500 pl-4">🟢 Stage 3 — Launching Soon
                    </h2>
                    <div class="grid gap-6 md:grid-cols-2" data-aos="fade-up">
                        <FeatureCard title="A Trip and Itinerary Planner" />

                        <FeatureCard title="Ability for providers to submit their own events" />

                        <FeatureCard title="Guest submissions to our Magazine"
                             />
                    </div>
                </div>
            </div>
        </section>
    </Layout>
</template>

<script setup>
import FeatureCard from '@/Components/Frontend/FeatureCard.vue'
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
</script>