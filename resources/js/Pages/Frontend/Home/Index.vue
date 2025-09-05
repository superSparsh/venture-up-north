<template>
    <Layout>
        <SeoMeta :title="`Welcome - Venture Up North`" :description="`Venture Up North - Explore, Indulge, Breathe`"
            :image="`/public/images/Venture-Up-North.png`" :canonical="`https://venturedownsouth.com`" :index="true"
            :follow="true" />

        <Head :title="seo?.title || 'Welcome'" />
        <HeroSection />
        <TownSection :towns="towns" />
        <ExperienceSection :experiences="experiences" />
        <TileSection :tiles="tiles" />
        <TeamSection :members="members" />
        <MagazineSection :magazines="magazines" :magazinePopup="magazinePopup"  />
    </Layout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import HeroSection from '@/Components/Frontend/HeroSection.vue'
import TeamSection from '@/Components/Frontend/TeamSection.vue'
import TownSection from '@/Components/Frontend/TownTile.vue'
import ExperienceSection from '@/Components/Frontend/ExperienceTile.vue'
import TileSection from '@/Components/Frontend/ImageTile.vue'
import MagazineSection from '@/Components/Frontend/VentureMagazineTile.vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'

defineProps({
    members: Array,
    towns: Array,
    experiences: Array,
    magazines: Array,
    tiles: Array,
    seo: Object,
    magazinePopup: { type: String, default: '' },
})
</script>