<template>
    <Modal :show="show" @close="emitClose">
        <!-- ✅ Main content -->
        <div class="p-6 space-y-4 mx-auto">
            <h2 class="text-xl font-bold text-blueGray-800">{{ contributor.user.name }}</h2>
            <h6 class="text-md font-normal text-blueGray-800"><span class="font-bold text-gray-600">Display Name:</span>
                {{ contributor.author.display_name }}</h6>
            <p class="text-sm text-gray-600">Email: {{ contributor.user?.email || '-' }}</p>

            <div v-if="contributor.author.avatar_path" class="mt-2">
                <img :src="`/public/storage/${contributor.author.avatar_path}`" alt="Photo"
                    class="h-32 rounded shadow" />
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
    contributor: Object,
})

const emit = defineEmits(['close'])

function emitClose() {
    emit('close')
}
</script>