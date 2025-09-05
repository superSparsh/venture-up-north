<template>
    <transition name="fade">
        <div v-if="open"
            :class="['fixed top-0 left-0 w-full md:h-auto text-heavy z-40 font-sans', scrolled ? 'bg-white' : 'bg-bison']">

            <!-- Menu Content -->
            <div
                class="container mx-auto px-6 md:px-16 grid grid-cols-1 md:grid-cols-5 gap-8 overflow-y-auto relative mega-menu">
                <!-- Mobile social icons inside MegaMenu -->


                <!-- Things To Do -->
                <div class="hidden md:block">
                    <h3 class="text-lg font-semibold mb-4">Things To Do</h3>
                    <ul class="space-y-2 text-sm">
                        <li v-for="experience in experiences" :key="experience.id">
                            <a :href="`/experience/book/${experience.slug}`">{{ experience.name }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Towns -->
                <div class="hidden md:block">
                    <h3 class="text-lg font-semibold mb-4">Explore the Region</h3>
                    <ul class="space-y-2 text-sm">
                        <li v-for="town in towns" :key="town.id">
                            <Link :href="`/town/${town.slug}`">{{ town.name }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Indulge -->
                <div class="hidden md:block">
                    <h3 class="text-lg font-semibold mb-4">Let's Indulge</h3>
                    <ul class="space-y-2 text-sm">
                        <li v-for="link in indulgeLinks" :key="link.id">
                            <a :href="link.link" target="_blank" rel="noopener sponsored">{{ link.title }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Meet the Team -->
                <div class="hidden md:block">
                    <h3 class="text-lg font-semibold mb-4">Meet the Team</h3>
                    <ul class="space-y-2 text-sm">
                        <li v-for="member in members" :key="member.id">
                            <a :href="`javascript:void(0)`" @click="openProfileModal(member)">{{ member.name }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Featured Post -->
                <Link :href="`/magazine/${magazine.slug}`">
                <div class="hidden md:block">
                    <img :src="`/public/storage/${magazine.hero_image}`" alt="Featured"
                        class="w-full h-48 object-cover rounded-md shadow mb-3" />
                    <h4 class="text-sm font-semibold">{{ magazine.title }}</h4>
                    <p class="text-xs text-slate-600">
                        {{ formattedDate }}
                    </p>
                </div>
                </Link>

                <!-- 📱 MOBILE ACCORDION MENU -->
                <!-- Things To Do -->
                <div class="md:hidden bg-white rounded-xl shadow-lg p-4 space-y-6">
                    <button @click="toggleAccordion('thingsToDo')"
                        class="w-full flex justify-between items-center text-left font-semibold text-lg py-2">
                        Things To Do
                        <span class="text-gray-500 text-xl">{{ accordionStates.thingsToDo ? '−' : '+' }}</span>
                    </button>
                    <transition name="fade">
                        <ul v-show="accordionStates.thingsToDo" class="pl-2 mt-2 space-y-2 text-md text-heavy">
                            <li v-for="experience in experiences" :key="experience.id">
                                <a :href="`/experience/book/${experience.slug}`" class="block hover:underline">{{
                                    experience.name }}</a>
                            </li>
                        </ul>
                    </transition>
                </div>

                <!-- Explore the Region -->
                <div class="md:hidden bg-white rounded-xl shadow-lg p-4 space-y-6">
                    <button @click="toggleAccordion('exploreRegion')"
                        class="w-full flex justify-between items-center text-left font-semibold text-lg py-2">
                        Explore the Region
                        <span class="text-gray-500 text-xl">{{ accordionStates.exploreRegion ? '−' : '+' }}</span>
                    </button>
                    <transition name="fade">
                        <ul v-show="accordionStates.exploreRegion" class="pl-2 mt-2 space-y-2 text-md text-heavy">
                            <li v-for="town in towns" :key="town.id">
                                <Link :href="`/town/${town.slug}`" class="block hover:underline">{{ town.name }}
                                </Link>
                            </li>
                        </ul>
                    </transition>
                </div>

                <!-- Let's Indulge -->
                <div class="md:hidden bg-white rounded-xl shadow-lg p-4 space-y-6">
                    <button @click="toggleAccordion('indulge')"
                        class="w-full flex justify-between items-center text-left font-semibold text-lg py-2">
                        Let's Indulge
                        <span class="text-gray-500 text-xl">{{ accordionStates.indulge ? '−' : '+' }}</span>
                    </button>
                    <transition name="fade">
                        <ul v-show="accordionStates.indulge" class="pl-2 mt-2 space-y-2 text-md text-heavy">
                            <li v-for="link in indulgeLinks" :key="link.id">
                                <a :href="link.link" class="block hover:underline" target="_blank"
                                    rel="noopener sponsored">{{ link.title }}</a>
                            </li>
                        </ul>
                    </transition>
                </div>

                <!-- Meet the Team -->
                <div class="md:hidden bg-white rounded-xl shadow-lg p-4 space-y-6">
                    <button @click="toggleAccordion('meetTeam')"
                        class="w-full flex justify-between items-center text-left font-semibold text-lg py-2">
                        Meet the Team
                        <span class="text-gray-500 text-xl">{{ accordionStates.meetTeam ? '−' : '+' }}</span>
                    </button>
                    <transition name="fade">
                        <ul v-show="accordionStates.meetTeam" class="pl-2 mt-2 space-y-2 text-md text-heavy">
                            <li v-for="member in members" :key="member.id">
                                <a href="javascript:void(0)" @click="openProfileModal(member)"
                                    class="block hover:underline">{{ member.name }}</a>
                            </li>
                        </ul>
                    </transition>
                </div>

                <!-- Featured Post -->
                <div class="md:hidden">
                    <Link :href="`/magazine/${magazine.slug}`" class="block">
                    <img :src="`/public/storage/${magazine.hero_image}`" alt="Featured"
                        class="w-full h-40 object-cover rounded-md shadow mb-2" />
                    <h4 class="text-sm font-semibold text-gray-800">{{ magazine.title }}</h4>
                    <p class="text-xs text-gray-500">{{ formattedDate }}</p>
                    </Link>
                </div>

                <!-- Only visible on mobile -->
                <div class="flex md:hidden justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="https://www.facebook.com/venturedownsouthwa" target="_blank" rel="noopener noreferrer">
                        <Facebook class="w-6 h-6 text-white hover:text-teal-600 transition" />
                    </a>
                    <a href="https://www.instagram.com/venturedownsouth" target="_blank" rel="noopener noreferrer">
                        <Instagram class="w-6 h-6 text-white hover:text-teal-600 transition" />
                    </a>
                    <a href="/my-venture" target="_blank" rel="noopener noreferrer">
                        <Heart class="w-6 h-6 text-white hover:text-teal-600 transition" />
                    </a>
                </div>


                <!-- Decorative Circle Blob -->
                <div
                    class="absolute -left-1/3 -bottom-1/4 w-[500px] h-[500px] bg-white opacity-30 rounded-full blur-3xl pointer-events-none">
                </div>
            </div>
        </div>
    </transition>
    <!-- Modal -->
    <TeamMemberModal :visible="showModal" :member="selectedMember" @close="closeProfileModal" />
</template>

<script setup>
import { ref, computed } from 'vue'
import { Facebook, Instagram, Heart } from 'lucide-vue-next'
import TeamMemberModal from '@/Components/Frontend/ProfileModal.vue'


const menuOpen = ref(false)
import { Link } from '@inertiajs/vue3'
const props = defineProps({
    open: Boolean,
    scrolled: Boolean,
    towns: Array,
    experiences: Array,
    members: Array,
    magazine: Object,
    indulgeLinks: Array
})

const accordionStates = ref({
    thingsToDo: false,
    exploreRegion: false,
    indulge: false,
    meetTeam: false,
});

function toggleAccordion(section) {
    accordionStates.value[section] = !accordionStates.value[section];
}

const formattedDate = computed(() => {
    const date = new Date(props.magazine.published_at);

    const day = date.getDate();
    const month = date.toLocaleString('en-GB', { month: 'long' });
    const year = date.getFullYear();
    const weekday = date.toLocaleString('en-GB', { weekday: 'long' });

    const getOrdinal = (n) => {
        const s = ["th", "st", "nd", "rd"];
        const v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    };

    return `${weekday}, ${getOrdinal(day)} ${month} ${year}`;
});


const showModal = ref(false)
const selectedMember = ref(null)

function openProfileModal(member) {
    selectedMember.value = member
    showModal.value = true
}

function closeProfileModal() {
    showModal.value = false
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.mega-menu {
    padding-top: 7rem;
    padding-bottom: 5rem;
}

@media only screen and (min-width: 768px) {
    .mega-menu {
        padding-top: 10rem;
        padding-bottom: 5rem;
    }
}
</style>