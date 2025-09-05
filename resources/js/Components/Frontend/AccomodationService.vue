<template>
    <transition name="fade-scale">
        <div v-show="visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm px-4">
            <div class="bg-heavy text-bison rounded-xl shadow-xl w-full max-w-lg p-6 relative">
                <button @click="close" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl">×</button>
                <div class="flex flex-col items-center text-center mt-5">
                    <p class="text-lg text-gray-500 mb-4 text-left hover:text-white">
                        We have access to an accommodation booking service that can secure you the most competitive
                        rates across a range of accommodation properties down south.
                        Contact our Luxe Venturer (
                        <a href="mailto:robyn@venturedownsouth.com.au"
                            class="text-teal-400 hover:underline">robyn@venturedownsouth.com.au</a>
                        /
                        <a href="tel:+61409082731" class="text-teal-400 hover:underline">+61 409 082 731</a>
                        ).
                    </p>
                </div>
            </div>
        </div>
    </transition>
</template>


<script setup>
const props = defineProps({
    visible: Boolean
})

const emit = defineEmits(['close', 'action'])

function close() {
    emit('close')
}

function action() {
    emit('action')
}
</script>

<style scoped>
/* Fade + Scale transition */
.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: all 0.3s ease;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

/* Custom email color */
.emailColor {
    color: #66C2A5;
}
</style>