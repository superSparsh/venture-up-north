<template>
    <div>
        <div class="flex justify-end mb-2">
            <button @click="isFullscreen = !isFullscreen"
                class="text-sm px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 shadow-sm" type="button">
                {{ isFullscreen ? 'Exit Fullscreen' : 'Fullscreen' }}
            </button>
        </div>
        <div :id="holderId" :class="[
            'border rounded p-4 bg-white shadow-sm transition-all',
            isFullscreen
                ? 'fixed inset-0 z-50 min-h-screen overflow-y-auto'
                : 'min-h-[300px] max-h-[500px] overflow-auto'
        ]" />
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import EditorJS from '@editorjs/editorjs'
import Header from '@editorjs/header'
import List from '@editorjs/list'
import Embed from '@editorjs/embed'
import ImageTool from '@editorjs/image'
import Paragraph from '@editorjs/paragraph'
import LinkTool from '@editorjs/link'
import Underline from '@editorjs/underline'
import Marker from '@editorjs/marker'
import InlineCode from '@editorjs/inline-code'


const props = defineProps({
    modelValue: Object,
})

const isFullscreen = ref(false)

const emit = defineEmits(['update:modelValue'])

const holderId = 'editorjs-' + Math.random().toString(36).substring(2, 8)

let editorInstance = null

onMounted(() => {
    editorInstance = new EditorJS({
        holder: holderId,
        placeholder: 'Start writing...',
        tools: {
            header: {
                class: Header,
                inlineToolbar: ['bold', 'italic', 'underline', 'marker', 'inlineCode'],
            },
            paragraph: {
                class: Paragraph,
                inlineToolbar: ['bold', 'italic', 'underline', 'marker', 'inlineCode'],
            },
            list: {
                class: List,
                inlineToolbar: true,
            },
            embed: Embed,
            image: {
                class: ImageTool,
                config: {
                    uploader: {
                        async uploadByFile(file) {
                            const formData = new FormData()
                            formData.append('image', file)

                            const response = await fetch('/api/uploads/editor-image', {
                                method: 'POST',
                                body: formData
                            })

                            const result = await response.json()

                            if (result.success) {
                                return result
                            } else {
                                throw new Error(result.message || 'Upload failed')
                            }
                        }
                    }
                }
            },
            linkTool: {
                class: LinkTool,
                config: {
                    endpoint: '/api/link-metadata' // You must create this endpoint
                }
            },
            underline: Underline,
            marker: Marker,
            inlineCode: InlineCode,
        },
        data: props.modelValue || {},
        async onChange(api) {
            const data = await api.saver.save()
            emit('update:modelValue', data)
        },
    })
})

onBeforeUnmount(() => {
    editorInstance?.destroy()
})

// helper to convert file to base64 data URL
async function toBase64Result(file) {
    const reader = new FileReader()
    return new Promise((resolve, reject) => {
        reader.onload = () => {
            resolve({
                success: 1,
                file: {
                    url: reader.result,
                }
            })
        }
        reader.onerror = reject
        reader.readAsDataURL(file)
    })
}

function handleKeydown(e) {
    if (e.key === 'Escape' && isFullscreen.value) {
        isFullscreen.value = false
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
})

onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleKeydown)
})

defineExpose({
    async getContent() {
        return await editorInstance?.save()
    }
})
</script>