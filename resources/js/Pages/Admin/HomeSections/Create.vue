<template>
    <AdminLayout>

        <Head title="Create Home Section" />

        <div class="flex justify-center mt-6">
            <div class="w-full max-w-2xl bg-white p-8 rounded-xl shadow-xl">
                <div class="mb-6 border-b pb-4">
                    <h2 class="text-2xl font-semibold text-blueGray-800">Add New Home Section</h2>
                    <p class="text-sm text-blueGray-500">Define a new content block to appear on the homepage.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Title</label>
                        <input v-model="form.title" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                    </div>

                    <!-- Order -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Display Order</label>
                        <input v-model="form.order" type="number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.order" class="text-red-500 text-sm mt-1">{{ form.errors.order }}</p>
                    </div>

                    <!-- Active Toggle -->
                    <div class="flex items-center">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                            class="form-checkbox h-5 w-5 text-emerald-500 rounded" />
                        <label for="is_active" class="ml-3 block text-sm text-blueGray-700">
                            Mark as active
                        </label>
                    </div>

                    <!-- Tiptap Editor -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Section Content</label>
                        <div
                            class="border border-gray-300 rounded-md shadow-sm focus-within:ring focus-within:ring-blue-200 bg-white p-2">
                            <EditorContent :editor="editor" class="min-h-[200px]" />
                        </div>
                        <p v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</p>
                    </div>

                    <!-- Actions -->
                    <!-- Actions -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="goBack"
                            class="bg-white border border-gray-300 text-blueGray-700 hover:bg-gray-100 font-semibold px-5 py-2 rounded-md shadow-sm">
                            Cancel
                        </button>

                        <button type="submit" :disabled="form.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2 rounded-md shadow">
                            Save Section
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'
import { watchEffect } from 'vue'
import { router } from '@inertiajs/vue3'


const toast = useToast()

const form = useForm({
    title: '',
    order: 0,
    is_active: true,
    content: '', // will store HTML from Tiptap
})

// ✅ Tiptap editor setup
const editor = useEditor({
    extensions: [StarterKit],
    content: '',
})

// ✅ Sync editor HTML into form.content
watchEffect(() => {
    if (editor.value) {
        form.content = editor.value?.getHTML() || ''
    }
})

function submit() {
    form.post(route('admin.home-sections.store'), {
        preserveScroll: true,
        onSuccess: () => toast.success('Home section created successfully!'),
        onError: () => toast.error('Failed to create section'),
    })
}

function goBack() {
    router.visit(route('admin.home-sections.index'))
}
</script>