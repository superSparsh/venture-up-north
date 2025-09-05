<template>
    <transition name="fade-scale">
        <div v-show="visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm px-4">
            <div class="bg-heavy text-bison rounded-xl shadow-xl w-full max-w-lg p-6 relative
         md:max-h-none md:mt-0 md:h-auto
         h-[694px] mt-[40%] overflow-y-auto"> <button @click="close"
                    class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl">×</button>

                <!-- Outer wrapper inside the modal -->
                <div class="flex flex-col items-center text-center px-4 py-6">
                    <!-- Stage 3 -->
                    <h2 class="text-xl font-semibold mb-10 border-l-4 border-yellow-500 pl-3">
                        🟢 Stage 3 — Launching Soon
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 justify-center max-w-md w-full">
                        <FeatureCard title="A Trip and Itinerary Planner" />
                        <FeatureCard title="Ability for providers to submit their own events" />
                        <FeatureCard title="Guest submissions to our Magazine" />
                    </div>
                </div>

            </div>
        </div>
    </transition>
</template>


<script setup>
import FeatureCard from '@/Components/Frontend/FeatureCard.vue'
const props = defineProps({
    member: {
        type: Object,
        default: () => null
    },
    visible: Boolean
})
console.log(props.member)

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