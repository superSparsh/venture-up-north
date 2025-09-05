<template>
    <AdminLayout>

        <Head title="Edit Contributor" />

        <div class="flex justify-center mt-6">
            <div class="w-full bg-white p-8 rounded-xl shadow-xl">
                <div class="mb-6 border-b pb-4">
                    <h2 class="text-2xl font-semibold text-blueGray-800">Edit Contributor ({{ form.display_name }})</h2>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- LEFT COLUMN -->
                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Name <span
                                    class="text-red-600">*</span></label>
                            <input v-model="form.name" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                        </div>


                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">
                                Email <span class="text-red-600">*</span>
                            </label>
                            <input v-model="form.email" type="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                        </div>


                        <!-- Active -->
                        <div class="flex items-center">
                            <input type="checkbox" id="status" class="form-checkbox h-5 w-5 text-emerald-500"
                                :checked="form.status === 'active'"
                                @change="form.status = $event.target.checked ? 'active' : 'blocked'" />
                            <label for="status" class="ml-2 text-sm text-blueGray-700">
                                Mark as active
                            </label>
                        </div>


                        <!-- Photo Upload -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Photo (Minimum image size:
                                738×500px (width × height)</label>
                            <input type="file" @change="handleFile" />
                            <p v-if="form.errors.photo" class="text-red-500 text-sm mt-1">{{ form.errors.photo }}</p>

                            <div v-if="contributor.photo" class="mt-2">
                                <img :src="`/public/storage/${contributor.photo}`" alt="Current Photo"
                                    class="h-24 rounded shadow" />
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN - SEO Fields -->
                    <div class="space-y-6">
                        <!-- DisplayName -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Display Name <span
                                    class="text-red-600">*</span></label>
                            <input v-model="form.display_name" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.display_name" class="text-red-500 text-sm mt-1">{{
                                form.errors.display_name }}</p>
                        </div>
                        <!-- <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Title</label>
                            <input v-model="form.seo_title" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.seo_title" class="text-red-500 text-sm mt-1">{{ form.errors.seo_title
                                }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Description</label>
                            <textarea v-model="form.seo_description" rows="2"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.seo_description" class="text-red-500 text-sm mt-1">{{
                                form.errors.seo_description }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">SEO Image (Minimum image size:
                                1200×630px (width × height)</label>
                            <input type="file" @change="handleSeoImage" />
                            <p v-if="form.errors.seo_image" class="text-red-500 text-sm mt-1">{{ form.errors.seo_image
                                }}</p>

                            <div v-if="teamMember.seo_image" class="mt-2">
                                <img :src="`/public/storage/${teamMember.seo_image}`" alt="Current SEO Image"
                                    class="h-24 rounded shadow" />
                            </div>
                        </div> -->
                    </div>


                    <!-- Footer buttons -->
                    <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" @click="goBack"
                            class="bg-white border border-gray-300 text-blueGray-700 hover:bg-gray-100 font-semibold px-5 py-2 rounded-md shadow-sm">
                            Cancel
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2 rounded-md shadow">
                            Update Contributor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'

const props = defineProps({
    contributor: Object
})

const toast = useToast()

const form = useForm({
    _method: 'PUT',
    name: props.contributor.user.name,
    display_name: props.contributor.author.display_name,
    email: props.contributor.user?.email || '',
    status: props.contributor.status,
    photo: null,
})

console.log('🔍 Form init:', form);


function handleFile(e) {
    form.photo = e.target.files[0]
}

function handleSeoImage(e) {
    form.seo_image = e.target.files[0]
}

function submit() {
    form.post(route('admin.contributors.update', props.contributor.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Contributor updated successfully!'),
        onError: () => toast.error('Failed to update contributor'),
    })
}

function goBack() {
    router.visit(route('admin.contributors.index'))
}
</script>