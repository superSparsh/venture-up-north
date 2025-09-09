<script setup>
import { Facebook, Instagram, Youtube } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import ComingSoon from './ComingSoon.vue'
import AccomodationService from './AccomodationService.vue'

const props = defineProps({
    towns: {
        type: Array,
        required: true
    },
    experiences: {
        type: Array,
        required: true
    },
    contact: {
        type: Object,
        default: () => ({
            email: '',
            phone: '',
            address: ''
        })
    },
    social: {
        type: Array,
        default: () => []
    }
})

function resolveIcon(name) {
    const icons = { Facebook, Instagram, Youtube }
    return icons[name] || 'span'
}

// Modal visibility toggles
const showEventModal = ref(false)
const showBookingModal = ref(false)

function openEventModal(member) {
    showEventModal.value = true
}

function openBookingModal(member) {
    showBookingModal.value = true
}

function closeEventModal() {
    showEventModal.value = false
}

function closeBookingModal() {
    showBookingModal.value = false
}
</script>
<template>
    <footer class="relative text-white w-full">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="/public/images/footer-bg.jpg" alt="Footer Background" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        </div>

        <!-- Content -->
        <div class="relative container mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-6 z-10">
            <!-- Column 1 -->
            <div data-aos="fade-up">
                <!-- Contact Information with bottom border -->
                <div class="pb-8 border-b border-solid border-gray-300 mb-8">
                    <h3 class="text-lg font-bold mb-4">Contact Information</h3>
                    <ul class="space-y-2 text-sm">
                        <li v-if="contact.email">
                            ✉️ Email:
                            <a :href="`mailto:${contact.email}`" class="hover:text-teal-300">
                                {{ contact.email }}
                            </a>
                        </li>
                        <li v-if="contact.phone">
                            ☏ Contact Number:
                            <a :href="`tel:${contact.phone}`" class="hover:text-teal-300">
                                {{ contact.phone }}
                            </a>
                        </li>
                        <li v-if="contact.address">
                            📍 Address: {{ contact.address }}
                        </li>
                    </ul>
                </div>

                <!-- Follow Us section below the border -->
                <div class="text-white space-y-4">
                    <h3 class="text-lg font-extrabold uppercase tracking-wide">Follow Us</h3>

                    <!-- Social Icons -->
                    <div class="flex items-center space-x-4 text-2xl">
                        <a v-for="(item, index) in social" :key="index" :href="item.url" target="_blank"
                            class="hover:text-bison" rel="noopener noreferrer">
                            <component :is="resolveIcon(item.icon)" />
                        </a>
                    </div>

                    <!-- Buttons -->
                    <div class="space-y-10">
                        <Link href="/contact">
                        <button
                            class="bg-bison text-heavy px-6 py-2 mb-3 rounded font-bold text-base w-full hover:bg-white transition">
                            Contact Us
                        </button>
                        </Link>

                        <Link href="/login">
                        <button
                            class="bg-bison text-heavy px-6 py-2 rounded font-bold text-base w-full hover:bg-white transition">
                            Login
                        </button>
                        </Link>
                    </div>
                </div>
            </div>



            <!-- Column 2 -->
            <!-- <div data-aos="fade-up">
                <button @click="showEventModal = true"
                    class="inline-block bg-bison text-heavy px-4 py-2 rounded hover:bg-heavy hover:text-bison transition">
                    Coming Soon
                </button>
            </div> -->

            <!-- Column 3 -->
            <div data-aos="fade-up">
                <a href="https://tidd.ly/3FzY8fq" target="_blank" rel="noopener sponsored"
                    class="mb-5">
                    <!-- Accommodation Booking Service -->
                    <img src="/public/images/Flights&Accommodation.png" alt="Flights & Accommodation Service"
                        class="object-cover mb-5 w-full" />
                </a>
                <h6 class="font-bold tracking-wide">All site images courtesy of Tourism Western Australia</h6>
            </div>

            <!-- Event Modal -->
            <ComingSoon :visible="showEventModal" @close="closeEventModal" title="Coming Soon">
                <p>This feature is coming soon. Stay tuned!</p>
            </ComingSoon>

            <!-- Booking Modal -->
            <AccomodationService :visible="showBookingModal" @close="closeBookingModal"
                title="Accommodation Booking Service">
                <p>We’re working on integrating booking services. Check back shortly!</p>
            </AccomodationService>

            <!-- Column 4: Association Info -->
            <div data-aos="fade-up">
                <!-- Left: Branding / Info -->
                <div class="w-full">
                    <img src="/public/images/Venture-Up-North.png" alt="Footer Logo"
                        class="w-[115px] h-auto object-contain mb-4" />
                    <p class="text-sm mb-2">Subscribe to stay updated with the latest from the Venture Up North.
                    </p>
                    <!-- Right: Mailchimp Form -->
                    <form
                        action="https://ventureupnorth.us16.list-manage.com/subscribe/post?u=278c223d88935d8eb67de9227&amp;id=bd1d9a7a1a&amp;f_id=00f6c2e1f0"
                        method="post" target="_blank"
                        class="flex flex-col sm:flex-row items-start sm:items-center gap-2" novalidate>
                        <input type="email" name="EMAIL" placeholder="Your email address" required
                            class="w-full px-4 py-2 text-sm rounded border border-gray-300 text-black" />

                        <button type="submit"
                            class="px-4 py-2 bg-bison text-heavy rounded hover:bg-heavy hover:text-bison transition text-sm">
                            Subscribe
                        </button>

                        <!-- Anti-bot honeypot -->
                        <div style="position: absolute; left: -5000px;" aria-hidden="true">
                            <input type="text" name="b_278c223d88935d8eb67de9227_bd1d9a7a1a" tabindex="-1" />
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div
            class="relative z-10 border-t border-white/30 py-4 px-6 text-xs flex flex-col md:flex-row justify-between items-center text-white bg-black bg-opacity-40">
            <div class="mb-2 md:mb-0">© 2025 Venture Up North</div>
            <div class="space-x-4">
                <Link :href="`/sitemap`" class="hover:underline">Sitemap</Link>
                <!-- <Link :href="`/upcoming-features`" class="hover:underline">Upcoming Features</Link> -->
                <!-- <a href="#" class="hover:underline">Privacy Policy</a>
                <a href="#" class="hover:underline">Disclaimer</a> -->
            </div>
        </div>
    </footer>
</template>
