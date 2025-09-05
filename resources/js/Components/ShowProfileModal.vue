<template>
    <Modal :show="show" @close="emitClose">
        <!-- ✅ Main content -->
        <div class="p-6 space-y-4 mx-auto">
            <h2 class="text-xl font-bold text-blueGray-800">{{ member.name }}</h2>
            <p class="text-sm text-gray-500">{{ member.designation }}</p>
            <p class="text-sm text-gray-600">Email: {{ member.user?.email || '-' }}</p>

            <div v-if="member.photo" class="mt-2">
                <img :src="`/public/storage/${member.photo}`" alt="Photo" class="h-32 rounded shadow" />
            </div>

            <div class="mt-4">
                <h3 class="text-sm font-semibold text-gray-800">Bio</h3>
                <p class="text-sm text-gray-600">{{ member.bio }}</p>
            </div>

            <div class="mt-4 border-t pt-4">
                <h3 class="text-sm font-semibold text-gray-800">SEO Info</h3>
                <p class="text-sm text-gray-600">Title: {{ member.seo_title || '-' }}</p>
                <p class="text-sm text-gray-600">Description: {{ member.seo_description || '-' }}</p>
                <img v-if="member.seo_image" :src="`/public/storage/${member.seo_image}`" alt="SEO Image" class="mt-2 h-24" />
            </div>
        </div>

        <!-- ✅ Footer slot outside the main content -->
        <template #footer>
            <button @click="emitClose" class="px-4 py-2 rounded bg-gray-200 text-sm font-medium hover:bg-gray-300">
                Close
            </button>
        </template>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'

defineProps({
    show: Boolean,
    member: Object,
})

const emit = defineEmits(['close'])

function emitClose() {
    emit('close')
}
</script>