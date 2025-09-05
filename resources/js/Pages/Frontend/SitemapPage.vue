<script setup>
import { Head,Link } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    towns: Array,
    experiences: Array,
    posts: Array,
    tours: Array,
    events: Array,
    contents: Array,
    content_listings: Array,
    collections: Array,
})
</script>

<template>
    <Layout>

        <Head title="Sitemap - Venture Up North" />


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
                        Sitemap
                    </h1>
                    <p class="mt-4 text-white text-lg md:text-xl font-light">Explore everything Venture Up North has
                        to offer</p>
                </div>
            </div>
        </section>

        <!-- Sitemap Links -->
        <section class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-gray-800 pb-10" data-aos="fade-up">
                <!-- Towns -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-teal-700 border-b pb-2">Towns</h2>
                    <ul class="space-y-2 text-sm">
                        <li v-for="town in towns" :key="town.id">
                            <Link :href="`/town/${town.slug}`"
                                class="hover:text-teal-600 hover:underline text-heavy font-semibold">{{ town.name
                                }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Experiences -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-teal-700 border-b pb-2 text-bison">Experiences</h2>
                    <ul class="space-y-2 text-sm">
                        <li v-for="experience in experiences" :key="experience.id">
                            <Link :href="`/experience/${experience.slug}`"
                                class="hover:text-teal-600 hover:underline text-heavy font-semibold">{{
                                    experience.name }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Magazine Posts -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-teal-700 border-b pb-2 text-bison">Magazine</h2>
                    <ul class="space-y-2 text-sm">
                        <li v-for="post in posts" :key="post.id">
                            <Link :href="`/magazine/${post.slug}`"
                                class="hover:text-teal-600 hover:underline text-heavy font-semibold">{{
                                    post.title }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Tour Tiles -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-teal-700 border-b pb-2 text-bison">Tours</h2>
                    <ul class="space-y-2 text-sm">
                        <li v-for="tour in tours" :key="tour.id">
                            <Link :href="`/tours/book/${tour.slug}`"
                                class="hover:text-teal-600 hover:underline text-heavy font-semibold">{{ tour.title
                                }}</Link>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-gray-800" data-aos="fade-up">
                <!-- Events -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-teal-700 border-b pb-2">Events</h2>
                    <ul class="space-y-2 text-sm">
                        <li v-for="event in events" :key="event.id">
                            <Link :href="`/event/${event.slug}`"
                                class="hover:text-teal-600 hover:underline text-heavy font-semibold">{{ event.name
                                }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Contents -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-teal-700 border-b pb-2 text-bison">Contents</h2>
                    <ul class="space-y-2 text-sm">
                        <li v-for="content in contents" :key="content.id">
                            <Link :href="`/explore/${content.slug}`"
                                class="hover:text-teal-600 hover:underline text-heavy font-semibold">{{
                                    content.name }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Content Listings -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-teal-700 border-b pb-2 text-bison">Content Listings</h2>
                    <ul class="space-y-2 text-sm">
                        <li v-for="content_listing in content_listings" :key="content_listing.id">
                            <Link :href="`/explore/${content_listing.category.slug}/${content_listing.slug}`"
                                class="hover:text-teal-600 hover:underline text-heavy font-semibold">{{
                                    content_listing.title }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Collections -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-teal-700 border-b pb-2 text-bison">Collections</h2>
                    <ul class="space-y-2 text-sm">
                        <li v-for="collection in collections" :key="collection.id">
                            <Link :href="`/collection/${collection.slug}`"
                                class="hover:text-teal-600 hover:underline text-heavy font-semibold">{{ collection.name
                                }}</Link>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </Layout>
</template>
