<script setup>
import { ref, reactive, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'
import EditorBlock from '@/Components/EditorBlock.vue'

// Props coming from controller
const props = defineProps({
    events_content: String,
    magazine_content: String,
})

const toast = useToast()
const editorContent = ref(
    typeof props.events_content === 'string'
        ? JSON.parse(props.events_content)
        : props.events_content || {}
);
const magazineContent = ref(
    typeof props.magazine_content === 'string'
        ? JSON.parse(props.magazine_content)
        : props.events_cmagazine_contentontent || {}
);

watch(editorContent, (val) => {
    form.events_content = val
})
watch(magazineContent, (val) => {
    form.magazine_content = val
})
// Inertia form
const form = useForm({
    preserveScroll: true,
    forceFormData: true,
    events_content: props.events_content || '',
    magazine_content: props.magazine_content || '',
})


// Save handler
function submit() {
    form.put(route('admin.popups.update'), {
        onSuccess: () => toast.success('Popup Data updated successfully!'),
        onError: () => toast.error('Failed to update popup data'),
    })
}
</script>

<template>
    <AdminLayout>

        <Head title="Site Popups" />
        <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow">
            <h1 class="text-2xl font-bold mb-6">Manage Popups</h1>

            <!-- Events Popup -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Events Popup Content</label>
                <EditorBlock v-model="editorContent" />
                <div v-if="form.errors.events_content" class="text-red-500 text-sm mt-1">
                    {{ form.errors.events_content }}
                </div>
            </div>

            <!-- Magazine Popup -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Magazine Popup Content</label>
                <EditorBlock v-model="magazineContent" />
                <div v-if="form.errors.magazine_content" class="text-red-500 text-sm mt-1">
                    {{ form.errors.magazine_content }}
                </div>
            </div>

            <!-- Save button -->
            <div class="flex justify-end">
                <button @click="submit" :disabled="form.processing"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                    Save
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
