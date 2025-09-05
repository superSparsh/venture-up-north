<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import EditorJSHTML from 'editorjs-html'

const props = defineProps({ active: { type: String, required: true }, magazinePopup: { type: String, default: '' } })
const tabs = [
    { key: 'community', label: 'Community Articles', href: '/magazines/community' },
    { key: 'our', label: 'Our Articles', href: '/magazines/ours' },
]

const showModal = ref(false)
function openModal() {
    showModal.value = true
    document.documentElement.classList.add('overflow-hidden') // lock scroll
}
function closeModal() {
    showModal.value = false
    document.documentElement.classList.remove('overflow-hidden')
}

const edjsParser = EditorJSHTML({
    linkTool: (block) => {
        const { link, meta } = block.data
        const title = meta?.title || link
        const description = meta?.description || ''
        const imageUrl = meta?.image?.url

        return `
       <a href="${link}" target="_blank" rel="noopener noreferrer" class="block p-4 rounded hover:shadow transition bg-white no-underline">
        <div class="text-lg font-semibold text-blue-600">${title}</div>
        ${imageUrl ? `<img src="${imageUrl}" class="mt-2 object-contain rounded" />` : ''}
      </a>
    `
    }
})

const renderedDescription = computed(() => {
    if (!props.magazinePopup) return ''
    try {
        const json = typeof props.magazinePopup === 'string'
            ? JSON.parse(props.magazinePopup)
            : props.magazinePopup

        const parsed = edjsParser.parse(json)

        return Array.isArray(parsed) ? parsed.join('') : parsed
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})
</script>

<template>
    <div class="flex flex-wrap gap-2 mt-5 items-center">
        <!-- Tabs -->
        <Link v-for="t in tabs" :key="t.key" :href="t.href" class="px-4 py-2 rounded-full border transition" :class="t.key === active
            ? 'bg-bison text-heavy border-bison'
            : 'bg-heavy text-white border-white/20 hover:bg-bison hover:text-heavy'">
        {{ t.label }}
        </Link>

        <!-- Always at end -->
        <button type="button" @click="openModal" class="px-4 py-2 rounded-full border transition inline-flex items-center justify-end
               border-bison text-heavy hover:border-heavy hover:text-bison">
            Write an Article
        </button>
    </div>



    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" aria-modal="true"
        role="dialog">
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black/60" @click="closeModal"></div>

        <!-- Dialog -->
        <div class="relative z-10 w-full sm:w-[92%] max-w-xl rounded-lg shadow-2xl bg-rose-50
           max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Header bar -->
            <div class="bg-indigo-800 text-white text-xl sm:text-2xl tracking-wide font-bold text-center py-3 px-4">
                Magazine Articles
            </div>

            <!-- Body (scrolls if content is tall) -->
            <div class="p-4 sm:p-6 text-heavy overflow-y-auto" style="scrollbar-gutter: stable;">
                <!-- Make rich text responsive & wrap long content -->
                <div class="prose max-w-none prose-li:marker:text-heavy tracking-wide font-bold
               prose-img:max-w-full prose-img:h-auto break-words" v-html="renderedDescription"></div>

                <!-- CTA -->
                <div class="text-center mt-6">
                    <Link :href="route?.('login') ?? '/register'" class="inline-flex items-center justify-center px-6 py-2 rounded-full
                 bg-heavy text-white font-semibold hover:bg-indigo-700 transition text-lg tracking-wide">
                    Register or Login
                    </Link>
                </div>
            </div>

            <!-- Close button -->
            <button type="button" @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
                aria-label="Close" title="Close">
                ✕
            </button>
        </div>
    </div>
</template>