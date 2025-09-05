<template>
  <div class="absolute top-3 right-3 z-40">
    <div class="relative pointer-events-auto" @mouseenter="hover = true" @mouseleave="hover = false">
      <!-- Trigger: Share icon -->
      <button @click.stop="toggleOpen" class="flex items-center justify-center w-10 h-10 rounded-full bg-heavy backdrop-blur-sm text-white shadow
               hover:bg-black/70 transition" aria-label="Share" title="Share">
        <ShareIcon class="w-5 h-5" />
      </button>

      <!-- Slide-up panel (appears ABOVE the button) -->
      <Transition name="slide-up">
        <div v-if="hover || open" class="absolute right-0 top-12 flex items-center gap-2 bg-bison px-3 py-2 rounded-full shadow-lg
                 text-white" @mouseenter="hover = true" @mouseleave="hover = false" @click.stop>
          <a :href="facebookUrl" target="_blank" rel="noopener" class="share-chip" title="Share on Facebook"
            aria-label="Share on Facebook">
            <!-- FB -->
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M22 12a10 10 0 1 0-11.5 9.87v-7h-2v-2.87h2V9.5c0-2 1.2-3.12 3-3.12.86 0 1.76.15 1.76.15v2h-1c-1 0-1.31.62-1.31 1.26v1.34h2.23L15 14.87h-2.1v7A10 10 0 0 0 22 12Z" />
            </svg>
          </a>

          <a :href="twitterUrl" target="_blank" rel="noopener" class="share-chip" title="Share on X"
            aria-label="Share on X">
            <!-- X -->
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.39l-5.22-6.828-5.976 6.828H1.886l7.73-8.834L1.5 2.25h6.913l4.713 6.216 5.118-6.216z" />
            </svg>
          </a>

          <a :href="linkedinUrl" target="_blank" rel="noopener" class="share-chip" title="Share on LinkedIn"
            aria-label="Share on LinkedIn">
            <!-- IN -->
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M19 0h-14c-2.76 0-5 2.24-5 5v14c0 2.762 2.24 5 5 5h14c2.762 0 5-2.238 5-5v-14c0-2.76-2.238-5-5-5zM8.338 18.338h-2.676v-8.676h2.676v8.676zM7 8.162c-.858 0-1.55-.692-1.55-1.55s.692-1.55 1.55-1.55 1.55.692 1.55 1.55-.692 1.55-1.55 1.55zM18.338 18.338h-2.676v-4.282c0-1.021-.021-2.336-1.424-2.336-1.426 0-1.644 1.113-1.644 2.263v4.355h-2.676v-8.676h2.571v1.188h.036c.358-.677 1.232-1.39 2.537-1.39 2.711 0 3.215 1.785 3.215 4.104v4.774z" />
            </svg>
          </a>

          <a :href="whatsappUrl" target="_blank" rel="noopener" class="share-chip" title="Share on WhatsApp"
            aria-label="Share on WhatsApp">
            <!-- WA -->
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.62-6.003C.122 5.29 5.485 0 12.057 0c3.18 0 6.167 1.24 8.413 3.488a11.8 11.8 0 0 1 3.484 8.414c-.003 6.572-5.293 11.935-11.867 11.935a11.9 11.9 0 0 1-6.005-1.616L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.593 5.448 0 9.886-4.434 9.889-9.877.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.435-9.889 9.884a9.85 9.85 0 0 0 1.713 5.574l-.999 3.648 3.775-.93zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.03-.967-.272-.099-.47-.149-.669.149-.198.297-.767.967-.94 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.297-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.611-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.007-1.413.248-.694.248-1.289.173-1.413z" />
            </svg>
          </a>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { ShareIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  url: { type: String, required: true },
  title: { type: String, default: '' },
  text: { type: String, default: '' }
})

const enc = s => encodeURIComponent(s || '')
const abs = (u) => new URL(u, window.location.origin).toString()
const shareUrl = computed(() => abs(props.url))

const facebookUrl = computed(() =>
  `https://www.facebook.com/sharer/sharer.php?u=${enc(shareUrl.value)}`
)
const twitterUrl = computed(() =>
  `https://twitter.com/intent/tweet?url=${enc(shareUrl.value)}&text=${enc(props.title)}`
)
const linkedinUrl = computed(() =>
  `https://www.linkedin.com/sharing/share-offsite/?url=${enc(shareUrl.value)}`
)
const whatsappUrl = computed(() =>
  `https://api.whatsapp.com/send?text=${encodeURIComponent(shareUrl.value)}`
)

const hover = ref(false)
const open = ref(false)
const toggleOpen = () => { open.value = !open.value }
</script>

<style scoped>
/* Smooth slide-up animation */
.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(8px) scale(0.98);
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: opacity .18s ease, transform .18s ease;
}

/* Icon chip styling */
.share-chip {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 9999px;
  transition: background-color .15s ease, opacity .15s ease;
}

.share-chip:hover {
  background-color: rgba(255, 255, 255, .12);
}
</style>
