<template>
    <div>
        <HeroSection v-if="sections.hero" :content="sections.hero.content" />
        <TeamSection v-if="sections.team" />
        <MagazineSection v-if="sections.magazine" />
        <TownSection v-if="sections['lets-venture']" />
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

// import HeroSection from '../Components/home/HeroSection.vue'
// import TeamSection from '../Components/home/TeamSection.vue'
// import MagazineSection from '../Components/home/MagazineSection.vue'
// import TownSection from '../Components/home/TownSection.vue'

const sections = ref({})

onMounted(async () => {
    const { data } = await axios.get('/api/home')
    sections.value = data
})
</script>