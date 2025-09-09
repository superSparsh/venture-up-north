<script setup>
import { Menu, X, Search, Facebook, Instagram, Mail, Heart } from 'lucide-vue-next'
import { ref, onMounted, onUnmounted } from 'vue'
import MegaMenu from '../MegaMenu.vue'
import { Link } from '@inertiajs/vue3'
import SearchDropdown from '../SearchDropdown.vue'

const props = defineProps({
    towns: Array,
    experiences: Array,
    members: Array,
    magazine: Object,
    indulgeLinks: Array
})

const menuOpen = ref(false)
const showSearch = ref(false)
const scrolled = ref(false)
const searchRef = ref(null)

const handleScroll = () => {
    scrolled.value = window.scrollY > 100
}

const handleClickOutside = (event) => {
    if (searchRef.value && !searchRef.value.contains(event.target)) {
        showSearch.value = false
    }
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <header :class="[
        'fixed top-0 w-full z-50 transition-all duration-300',
        scrolled ? 'bg-white text-heavy' : 'bg-transparent text-white'
    ]">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <!-- Left -->
            <div class="flex items-center space-x-4 w-1/3 xl:-ml-[40px] md:-ml-[40px]">
                <div class="h-px flex-1 bg-gradient-to-r from-white/50 to-transparent"></div>
                <button @click="menuOpen = !menuOpen"
                    class="flex items-center cursor-pointer text-md tracking-wide z-50 relative">
                    <component :is="menuOpen ? X : Menu" class="w-5 h-5 transition" />
                    <span class="pl-1 leading-none uppercase tracking-wider text-xl">
                        {{ menuOpen ? 'Close' : 'Menu' }}
                    </span>
                </button>
            </div>

            <!-- 🏷️ Logo with Glow + Pulse -->

            <div v-if="!scrolled"
                class="w-full md:w-1/3 px-4 text-center flex flex-col items-center justify-center leading-tight">
                <Link :href="`/`">
                <img src="/public/images/Venture-Up-North.png" alt="Venture Up North" :class="[
                    'mx-auto object-contain transition duration-500 ease-in-out drop-shadow-[0_0_20px_rgba(255,255,255,0.6)] hover:drop-shadow-[0_0_30px_rgba(255,255,255,0.8)] hover:scale-105',
                    menuOpen ? 'h-40' : 'h-52 md:h-64'
                ]" />
                </Link>
            </div>

            <div v-else
                class="w-full md:w-1/3 px-4 text-center flex flex-col items-center justify-center leading-tight">
                <Link :href="`/`">
                <img src="/public/images/venture-comp.png" alt="Venture Up North" :class="[
                    'mx-auto object-contain transition duration-500 ease-in-out drop-shadow-[0_0_20px_rgba(255,255,255,0.6)] hover:drop-shadow-[0_0_30px_rgba(255,255,255,0.8)] hover:scale-105',
                    menuOpen ? 'h-28' : 'h-40 md:h-28'
                ]" />
                </Link>
            </div>


            <!-- Social Icons + Right Gradient -->
            <div class="flex items-center space-x-4 w-1/3 justify-end xl:-mr-[40px] md:-mr-[40px] md:mt-4">

                <!-- Search Wrapper -->
                <SearchDropdown :scrolled="scrolled" :menuOpen="menuOpen" />

                <!-- My Venture -->
                <a class="md:inline" href="/my-venture">
                    <Heart :class="[
                        'w-7 h-7 md:w-6 md:h-6 transition hover:text-teal-200',
                        scrolled ? 'text-heavy' : 'text-white'
                    ]" />
                </a>

                <!-- Facebook (hide on mobile) -->
                <a class="hidden md:inline" href="https://www.facebook.com/ventureupnorthwa" target="_blank"
                    rel="noopener noreferrer">
                    <Facebook :class="[
                        'w-7 h-7 md:w-6 md:h-6 transition hover:text-teal-200',
                        scrolled ? 'text-heavy' : 'text-white'
                    ]" />
                </a>

                <!-- Instagram (hide on mobile) -->
                <a class="hidden md:inline" href="https://www.instagram.com/ventureupnorth" target="_blank"
                    rel="noopener noreferrer">
                    <Instagram :class="[
                        'w-7 h-7 md:w-6 md:h-6 transition hover:text-teal-200',
                        scrolled ? 'text-heavy' : 'text-white'
                    ]" />
                </a>

                <!-- Mail -->
                <a href="mailto:chris@ventureupnorth.com.au" target="_blank" rel="noopener noreferrer">
                    <Mail :class="[
                        'w-7 h-7 md:w-6 md:h-6 transition hover:text-teal-200',
                        scrolled ? 'text-heavy' : 'text-white'
                    ]" />
                </a>


                <!-- Gradient Line -->
                <div class="h-px flex-1 bg-gradient-to-l from-white/50 to-transparent"></div>
            </div>


        </div>
    </header>
    <MegaMenu :open="menuOpen" @close="menuOpen = false" :scrolled="scrolled" :towns="towns" :experiences="experiences"
        :members="members" :magazine="magazine" :indulgeLinks="indulgeLinks" />
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.3s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
