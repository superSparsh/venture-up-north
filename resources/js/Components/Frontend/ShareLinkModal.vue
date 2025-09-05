<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    shareUrl: { type: String, default: '' },
    title: { type: String, default: 'Venture Up North' },
    text: { type: String, default: 'Check out this Venture!' }
})
const emit = defineEmits(['update:modelValue'])

const open = computed({
    get: () => props.modelValue,
    set: v => emit('update:modelValue', v)
})

const copied = ref(false)
async function copyLink() {
    try {
        await navigator.clipboard.writeText(props.shareUrl)
    } catch {
        const t = document.createElement('textarea')
        t.value = props.shareUrl
        document.body.appendChild(t); t.select(); document.execCommand('copy'); document.body.removeChild(t)
    }
    copied.value = true
    setTimeout(() => (copied.value = false), 1500)
}

const links = computed(() => {
    const u = encodeURIComponent(props.shareUrl)
    const t = encodeURIComponent(props.title)
    const txt = encodeURIComponent(props.text)
    return {
        email: `mailto:?subject=${t}&body=${txt}%0A%0A${u}`,
        whatsapp: `https://api.whatsapp.com/send?text=${u}%0A%0A${txt}`,
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${u}`,
        x: `https://twitter.com/intent/tweet?text=${txt}&url=${u}`
    }
})

async function nativeShare() {
    if (!navigator.share) return false
    try {
        await navigator.share({ title: props.title, text: props.text, url: props.shareUrl })
        return true
    } catch { return false }
}

function safeOpen(url, target = '_blank') { if (url) window.open(url, target, 'noopener') }
function goEmail() { window.location.href = links.value.email }
</script>

<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-[999] grid place-items-center">
            <div class="absolute inset-0 bg-black/60" @click="open = false"></div>

            <div
                class="relative w-full max-w-xl mx-auto rounded-2xl bg-heavy text-white border border-white/10 p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Share this Venture</h3>
                    <button @click="open = false" class="px-2 py-1 rounded hover:bg-white/10"
                        aria-label="Close">✕</button>
                </div>

                <p class="text-white/80 text-sm mb-4">Send a link to this venture page.</p>

                <div
                    class="bg-black/30 border border-white/10 rounded-xl p-3 mb-4 text-sm max-h-16 overflow-y-auto break-words">
                    {{ shareUrl }}
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <button
                        class="rounded-xl px-3 py-2 bg-white text-heavy font-semibold hover:bg-envy hover:text-white transition"
                        @click="nativeShare">
                        Share
                    </button>

                    <button class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition"
                        @click="copyLink">
                        {{ copied ? 'Copied!' : 'Copy Link' }}
                    </button>

                    <a class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition text-center"
                        :href="links.email" @click.prevent="goEmail">Email</a>

                    <a class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition text-center"
                        :href="links.whatsapp" target="_blank" rel="noopener"
                        @click.prevent="safeOpen(links.whatsapp)">WhatsApp</a>

                    <a class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition text-center"
                        :href="links.facebook" target="_blank" rel="noopener"
                        @click.prevent="safeOpen(links.facebook)">Facebook</a>

                    <a class="rounded-xl px-3 py-2 bg-black/30 border border-white/20 hover:bg-black/40 transition text-center"
                        :href="links.x" target="_blank" rel="noopener" @click.prevent="safeOpen(links.x)">X /
                        Twitter</a>
                </div>
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
