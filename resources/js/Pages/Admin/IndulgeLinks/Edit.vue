<template>
    <AdminLayout>

        <Head title="Edit Indulge Menu Link" />

        <div class="w-full mx-auto p-6 bg-white rounded-xl shadow-xl">
            <div class="mb-6 border-b pb-4">
                <h2 class="text-2xl font-semibold text-blueGray-800">Edit Indulge Menu Link ({{ form.title }})</h2>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Title<span
                                class="text-red-600">*</span></label>
                        <input v-model="form.title" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                    </div>


                    <!-- Link -->
                    <div>
                        <label class="block text-sm font-bold text-blueGray-700 mb-1">Link (URL)<span
                                class="text-red-600">*</span></label>
                        <input v-model="form.url" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                        <p v-if="form.errors.url" class="text-red-500 text-sm mt-1">{{ form.errors.url }}</p>
                    </div>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-bold text-blueGray-700 mb-1">Sort Order<span
                            class="text-red-600">*</span></label>
                    <input v-model="form.sort_order" type="number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none" />
                    <p v-if="form.errors.sort_order" class="text-red-500 text-sm mt-1">{{ form.errors.sort_order }}
                    </p>
                </div>

                <!-- Is Active Toggle -->
                <div class="flex items-center">
                    <input v-model="form.is_active" type="checkbox" id="is_active"
                        class="form-checkbox h-5 w-5 text-emerald-500 rounded" />
                    <label for="is_active" class="ml-3 block text-sm text-blueGray-700">Mark as active</label>
                </div>

                <!-- Actions -->
                <div class="col-span-1 md:col-span-2 flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('admin.indulge-links.index')"
                        class="text-sm px-5 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
                    Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Update Link
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'
import 'vue-multiselect/dist/vue-multiselect.css'

const toast = useToast()

const props = defineProps({
    indulgeLink: Object,
})

const form = useForm({
    _method: 'PUT',
    title: props.indulgeLink.title,
    url: props.indulgeLink.url,
    is_active: true,
    sort_order: props.indulgeLink.sort_order,
})



function submit() {
    form.post(route('admin.indulge-links.update', props.indulgeLink.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success('Indulge Link updated successfully!'),
        onError: () => toast.error('Failed to update indulge link'),
    })
}
</script>