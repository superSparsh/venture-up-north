<template>
    <Modal :show="show" @close="emitClose">
        <!-- ✅ Main content -->
        <div class="p-6 space-y-4">
            <h2 class="text-xl font-bold text-blueGray-800">{{ contact.name }}</h2>
            <p class="text-sm text-gray-600">Email: {{ contact.email }}</p>
            <p class="text-sm text-gray-600">Subject: {{ contact?.subject || '-' }}</p>

            <div class="mt-4">
                <h3 class="text-sm font-semibold text-gray-800">Message</h3>
                <p class="text-sm text-gray-600">{{ contact.message }}</p>
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
    contact: Object,
})

const emit = defineEmits(['close'])

function emitClose() {
    emit('close')
}
</script>