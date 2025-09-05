<template>
    <div class="font-lato text-blueGray-800 ">
        <Navbar :towns="towns" :experiences="experiences" :members="members" :magazine="magazine"
            :indulgeLinks="indulgeLinks" :magazinePopup="magazinePopup" />
        <main>
            <slot />
        </main>
        <Footer :towns="towns" :experiences="experiences" :contact="contact" :social="social" />
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import Navbar from '@/Components/Frontend/Navbar.vue'
import Footer from '@/Components/Frontend/Footer.vue'
const layoutData = usePage().props.layoutData
const towns = layoutData?.towns ?? []
const experiences = layoutData?.experiences ?? []
const contact = layoutData?.contact ?? {}
const social = layoutData?.social ?? []
const members = layoutData?.members ?? []
const magazine = layoutData?.magazine ?? []
const indulgeLinks = layoutData?.indulgeLinks ?? []
const magazinePopup = layoutData?.magazinePopup ?? []
</script>

<style scoped>
body {
    font-family: 'Lato', sans-serif;
}
</style>