<script setup>
import { ref, watch, computed } from 'vue'
import { useVentureShare } from '@/Composables/useVentureShare'

const props = defineProps({
    modelValue: { type: Boolean, default: false }
})
const emit = defineEmits(['update:modelValue'])

const { isEmpty, shareUrl, socialLinks, nativeShare } = useVentureShare()

const open = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v)
})

const copied = ref(false)
async function copyLink() {
    try {
        await navigator.clipboard.writeText(shareUrl.value)
        copied.value = true
        setTimeout(() => (copied.value = false), 1500)
    } catch (e) {
        // fallback
        const t = document.createElement('textarea')
        t.value = shareUrl.value
        document.body.appendChild(t)
        t.select()
        document.execCommand('copy')
        document.body.removeChild(t)
        copied.value = true
        setTimeout(() => (copied.value = false), 1500)
    }
}

async function shareNow() {
    const ok = await nativeShare()
    if (!ok) {
        // if native share not available, just copy link as a convenience
        copyLink()
    }
}

function safeOpen(url, target = '_blank') {
    if (!url) return
    if (typeof window !== 'undefined' && typeof window.open === 'function') {
        window.open(url, target, 'noopener')
    } else if (typeof location !== 'undefined') {
        location.href = url
    }
}

function goEmail() {
    if (isEmpty.value) return
    const link = socialLinks().email
    if (typeof window !== 'undefined') {
        window.location.href = link
    } else if (typeof location !== 'undefined') {
        location.href = link
    }
}

function goWhatsapp() { if (!isEmpty.value) safeOpen(socialLinks().whatsapp) }
function goFacebook() { if (!isEmpty.value) safeOpen(socialLinks().facebook) }
function goX() { if (!isEmpty.value) safeOpen(socialLinks().x) }
</script>

<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-[999] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/60" @click="open = false"></div>

            <div
                class="relative w-full max-w-5xl mx-auto rounded-2xl bg-heavy text-white border border-white/10 p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Share your Venture</h3>
                    <button @click="open = false" class="px-2 py-1 rounded hover:bg-white/10">✕</button>
                </div>

                <p class="text-white/80 text-sm mb-4">
                    Share a link to your current Venture itinerary with friends.
                </p>

                <div
                    class="bg-black/30 border border-white/10 rounded-xl p-3 mb-4 text-sm max-h-72 overflow-y-auto break-words">
                    {{ shareUrl }}
                </div>


                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <!-- Share -->
                    <button
                        class="rounded-xl px-3 py-2 bg-white text-heavy font-semibold hover:bg-envy hover:text-white transition disabled:opacity-50"
                        @click="shareNow" :disabled="isEmpty" title="Device share">
                        Share
                    </button>

                    <!-- Copy -->
                    <button class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition"
                        @click="copyLink" :disabled="isEmpty" title="Copy link">
                        {{ copied ? 'Copied!' : 'Copy Link' }}
                    </button>

                    <!-- Email -->
                    <a class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition text-center"
                        :href="isEmpty ? undefined : socialLinks().email"
                        :class="{ 'pointer-events-none opacity-50': isEmpty }" @click.prevent="goEmail" title="Email">
                        Email
                    </a>

                    <!-- WhatsApp -->
                    <a class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition text-center"
                        :href="isEmpty ? undefined : socialLinks().whatsapp"
                        :class="{ 'pointer-events-none opacity-50': isEmpty }" @click.prevent="goWhatsapp"
                        title="WhatsApp">
                        WhatsApp
                    </a>

                    <!-- Facebook -->
                    <a class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition text-center"
                        :href="isEmpty ? undefined : socialLinks().facebook"
                        :class="{ 'pointer-events-none opacity-50': isEmpty }" @click.prevent="goFacebook"
                        title="Facebook">
                        Facebook
                    </a>

                    <!-- X / Twitter -->
                    <a class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition text-center"
                        :href="isEmpty ? undefined : socialLinks().x"
                        :class="{ 'pointer-events-none opacity-50': isEmpty }" @click.prevent="goX" title="X (Twitter)">
                        X / Twitter
                    </a>
                </div>



                <p v-if="isEmpty" class="text-xs text-white/60 mt-4">
                    Your Venture is empty — add a few places first to share.
                </p>
            </div>
        </div>
    </transition>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity .2s
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0
}
</style>
