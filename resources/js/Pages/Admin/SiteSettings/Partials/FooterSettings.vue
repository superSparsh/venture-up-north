<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'


const props = defineProps({
    contact: Object,
    social: Array,
})
const toast = useToast()

const form = useForm({
    contact: {
        email: props.contact.email || '',
        phone: props.contact.phone || '',
        address: props.contact.address || '',
    },
    social: Array.isArray(props.social) && props.social.length
        ? props.social
        : [
            { platform: 'facebook', url: '', icon: 'Facebook' },
            { platform: 'instagram', url: '', icon: 'Instagram' },
        ],
})


function addSocial() {
    form.social.push({ platform: '', url: '', icon: '' })
}

function removeSocial(index) {
    form.social.splice(index, 1)
}

function submit() {
    form.post(route('admin.footer.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Footer Settings saved successfully'),
        onError: () => toast.error('Failed to save footer settings'),
    })
}
</script>

<template>

    <Head title="Footer Settings" />

    <div class="max-w-4xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold mb-6">Footer Settings</h1>

        <!-- Contact Info -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">Contact Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label>Email</label>
                    <input v-model="form.contact.email" type="email" class="w-full border px-3 py-2 rounded"
                        :class="{ 'border-red-500': form.errors['contact.email'] }" />
                    <p v-if="form.errors['contact.email']" class="text-red-500 text-sm mt-1">
                        {{ form.errors['contact.email'] }}
                    </p>
                </div>
                <div>
                    <label>Phone</label>
                    <input v-model="form.contact.phone" type="text" class="w-full border px-3 py-2 rounded"
                        :class="{ 'border-red-500': form.errors['contact.phone'] }" />
                    <p v-if="form.errors['contact.phone']" class="text-red-500 text-sm mt-1">
                        {{ form.errors['contact.phone'] }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <label>Address</label>
                    <textarea v-model="form.contact.address" rows="2" class="w-full border px-3 py-2 rounded"
                        :class="{ 'border-red-500': form.errors['contact.address'] }" />
                    <p v-if="form.errors['contact.address']" class="text-red-500 text-sm mt-1">
                        {{ form.errors['contact.address'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">Social Media Links</h2>

            <div v-for="(item, index) in form.social" :key="index" class="mb-4 border p-4 rounded font-bold">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end justify-content-center">
                    <div>
                        <label class="">Platform</label>
                        <input v-model="item.platform" type="text" class="w-full border px-3 py-2 rounded"
                            :class="{ 'border-red-500': form.errors['item.platform'] }" />
                        <p v-if="form.errors['item.platform']" class="text-red-500 text-sm mt-1">
                            {{ form.errors['item.platform'] }}
                        </p>
                    </div>
                    <div>
                        <label>URL</label>
                        <input v-model="item.url" type="url" class="w-full border px-3 py-2 rounded"
                            :class="{ 'border-red-500': form.errors['item.url'] }" />
                        <p v-if="form.errors['item.url']" class="text-red-500 text-sm mt-1">
                            {{ form.errors['item.url'] }}
                        </p>
                    </div>
                    <div>
                        <label>Icon</label>
                        <input v-model="item.icon" type="text" class="w-full border px-3 py-2 rounded"
                            :class="{ 'border-red-500': form.errors['item.icon'] }" />
                        <p v-if="form.errors['item.icon']" class="text-red-500 text-sm mt-1">
                            {{ form.errors['item.icon'] }}
                        </p>
                    </div>
                    <div class="flex items-end pt-2">
                        <button @click="removeSocial(index)" type="button" class="text-red-600 text-xl" title="Remove">
                            🗑️
                        </button>
                    </div>
                </div>
            </div>


            <button @click="addSocial" type="button"
                class="text-sm px-4 py-2 bg-blue-600 text-white rounded hover:bg-gray-200">+
                Add Social Link</button>
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
