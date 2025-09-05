<template>
    <AdminLayout>

        <Head title="Edit Team Member" />

        <div class="flex justify-center mt-6">
            <div class="w-full bg-white p-8 rounded-xl shadow-xl">
                <div class="mb-6 border-b pb-4">
                    <h2 class="text-2xl font-semibold text-blueGray-800">Edit Team Member ({{ form.name }})</h2>
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

                        <!-- Designation -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Designation <span
                                    class="text-red-600">*</span></label>
                            <input v-model="form.designation" type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.designation" class="text-red-500 text-sm mt-1">{{
                                form.errors.designation }}</p>
                        </div>

                        <!-- Display Order -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Display Order</label>
                            <input v-model="form.order" type="number"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.order" class="text-red-500 text-sm mt-1">{{ form.errors.order }}</p>
                        </div>

                        <!-- Active -->
                        <div class="flex items-center">
                            <input v-model="form.is_active" type="checkbox" id="is_active"
                                class="form-checkbox h-5 w-5 text-emerald-500" />
                            <label for="is_active" class="ml-2 text-sm text-blueGray-700">Mark as active</label>
                        </div>

                        <!-- Bio -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Bio</label>
                            <textarea v-model="form.bio" rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                            <p v-if="form.errors.bio" class="text-red-500 text-sm mt-1">{{ form.errors.bio }}</p>
                        </div>

                        <!-- Photo Upload -->
                        <div>
                            <label class="block text-sm font-bold text-blueGray-700 mb-1">Photo (Minimum image size:
                                738×500px (width × height)</label>
                            <input type="file" @change="handleFile" />
                            <p v-if="form.errors.photo" class="text-red-500 text-sm mt-1">{{ form.errors.photo }}</p>

                            <div v-if="teamMember.photo" class="mt-2">
                                <img :src="`/public/storage/${teamMember.photo}`" alt="Current Photo"
                                    class="h-24 rounded shadow" />
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN - SEO Fields -->
                    <div class="space-y-6">
                        <div>
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
                        </div>
                    </div>

                    <!-- Footer buttons -->
                    <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" @click="goBack"
                            class="bg-white border border-gray-300 text-blueGray-700 hover:bg-gray-100 font-semibold px-5 py-2 rounded-md shadow-sm">
                            Cancel
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2 rounded-md shadow">
                            Update Member
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
    teamMember: Object
})

const toast = useToast()

const form = useForm({
    _method: 'PUT',
    name: props.teamMember.name,
    email: props.teamMember.user?.email || '',
    designation: props.teamMember.designation,
    bio: props.teamMember.bio,
    order: props.teamMember.order,
    is_active: Boolean(props.teamMember.is_active),
    photo: null,
    seo_title: props.teamMember.seo_title,
    seo_description: props.teamMember.seo_description,
    seo_image: null,
})

console.log('🔍 Form init:', form);


function handleFile(e) {
    form.photo = e.target.files[0]
}

function handleSeoImage(e) {
    form.seo_image = e.target.files[0]
}

function submit() {
    form.post(route('admin.team-members.update', props.teamMember.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Team member updated successfully!'),
        onError: () => toast.error('Failed to update member'),
    })
}

function goBack() {
    router.visit(route('admin.team-members.index'))
}
</script>