import { computed } from 'vue'
import { useMyVenture } from '@/Composables/useMyVenture'

export function useVentureShare() {
    const venture = useMyVenture()
    const origin = typeof window !== 'undefined' ? window.location.origin : ''
    const basePath = '/my-ventures' // adjust if your route differs, e.g. '/my-ventures/share'

    const isEmpty = computed(() => !venture.items?.value?.length)

    const shareUrl = computed(() => {
        // Encode the whole venture plan into the URL (client-side share)
        const payload = venture.exportPayload()
        return `${origin}${basePath}?v=${encodeURIComponent(payload)}`
    })

    const title = computed(() => 'My Venture - Venture Up North')
    const text = computed(() => 'Check out my Venture plan for Down South!')

    function socialLinks() {
        const u = encodeURIComponent(shareUrl.value)
        const t = encodeURIComponent(title.value)
        const txt = encodeURIComponent(text.value)

        return {
            whatsapp: `https://api.whatsapp.com/send?text=${txt}%20${u}`,
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${u}`,
            x: `https://twitter.com/intent/tweet?text=${txt}&url=${u}`,
            copy: shareUrl.value,
            email: `mailto:?subject=${t}&body=${txt}%0A%0A${u}`
        }
    }

    async function nativeShare() {
        if (!navigator.share) return false
        try {
            await navigator.share({
                title: title.value,
                text: text.value,
                url: shareUrl.value
            })
            return true
        } catch (e) {
            // user canceled or unsupported
            return false
        }
    }

    return { isEmpty, shareUrl, title, text, socialLinks, nativeShare }
}
