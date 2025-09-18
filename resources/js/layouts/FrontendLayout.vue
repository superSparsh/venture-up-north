<template>
    <div class="font-lato text-blueGray-800 ">
        <Navbar :towns="towns" :experiences="experiences" :members="members" :magazine="magazine"
            :indulgeLinks="indulgeLinks" :magazinePopup="magazinePopup" />
        <main>
            <slot />
        </main>
        <Footer :towns="towns" :experiences="experiences" :contact="contact" :social="social" />
        <!-- Simple Popup -->
        <!-- <teleport to="body">
            <transition name="fade">
                <div v-if="showPopup" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50"
                    role="dialog" aria-modal="true" aria-labelledby="dev-modal-title">
                    <div class="mx-4 w-full max-w-md rounded-2xl bg-heavy p-6 shadow-2xl text-bison font-bold tracking-wide" @click.stop>
                        <div class="flex items-start justify-between">
                            <h3 id="dev-modal-title" class="text-lg font-semibold">
                                Heads up!
                            </h3>
                            <button @click="showPopup = false"
                                class="ml-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-blueGray-100 hover:bg-blueGray-200"
                                aria-label="Close">
                                ×
                            </button>
                        </div>

                        <p class="mt-3 text-blueGray-700">
                            This site is under development and will be fully functional by the
                            end of September.
                        </p>

                        <div class="mt-6 flex justify-end">
                            <button @click="showPopup = false"
                                class="rounded-lg bg-bison px-4 py-2 text-white">
                                Got it
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </teleport> -->
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
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

// Popup state
const showPopup = ref(true)

// Close on ESC
const onKeydown = (e) => {
    if (e.key === 'Escape') showPopup.value = false
}
onMounted(() => window.addEventListener('keydown', onKeydown))
onUnmounted(() => window.removeEventListener('keydown', onKeydown))
</script>

<style scoped>
body {
    font-family: 'Lato', sans-serif;
}

/* Simple fade transition for the modal */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>