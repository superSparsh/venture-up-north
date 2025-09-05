<template>

    <Head title="Site SEO Settings" />
    <div class="max-w-4xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold mb-6">Site Settings</h1>

        <!-- Contact Info -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label>SEO Title</label>
                    <input v-model="form.title" type="text" class="w-full border px-3 py-2 rounded"
                        :class="{ 'border-red-500': form.errors.title }" />
                    <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                        {{ form.errors.title }}
                    </p>
                </div>
                <div class="mb-5">
                    <label>SEO Description</label>
                    <input v-model="form.description" type="text" class="w-full border px-3 py-2 rounded"
                        :class="{ 'border-red-500': form.errors.description }" />
                    <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                        {{ form.errors.description }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Image (Minimum image size:
                        1200×630px (width × height))</label>
                    <input type="file" @change="handleSeo" />
                    <p v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}
                    </p>
                    <div v-if="form.image_preview" class="mt-2">
                        <img :src="`/public${form.image_preview}`" class="h-24 rounded shadow" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="w-full text-right">
            <button @click="submit" :disabled="form.processing"
                class="px-6 py-3 bg-teal-600 text-white rounded hover:bg-teal-700 transition">
                {{ form.processing ? 'Saving...' : 'Save Settings' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const props = defineProps({ seo: Object })

const toast = useToast()
console.log(props.seo)
const form = useForm({
    title: props.seo?.title || '',
    description: props.seo?.description || '',
    image: null,
    image_preview: props.seo?.image || null
})

function handleSeo(e) {
    const file = e.target.files[0]
    form.image = file
    form.image_preview = URL.createObjectURL(file)
}

function submit() {
    form.post(route('admin.site-seo.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('SEO Settings saved successfully'),
        onError: () => toast.error('Failed to save SEO settings'),
    })
}
</script>