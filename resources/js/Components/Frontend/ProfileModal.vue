<template>
    <transition name="fade-scale">
        <div v-show="visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm px-4">
            <div v-if="member" class="bg-heavy text-bison rounded-xl shadow-xl w-full max-w-lg p-6 relative">
                <button @click="close" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl">×</button>

                <div class="flex flex-col items-center text-center">
                    <img :src="member.photo" alt="Profile Photo" class="w-24 h-24 rounded-full mb-4 object-cover" />
                    <h2 class="text-xl font-semibold mb-1 hover:text-white">{{ member.name }}</h2>
                    <h2 class="text-sm font-semibold mb-4">{{ member.designation }}</h2>
                    <p class="text-md text-gray-500 mb-4 text-left hover:text-white">{{ member.bio }}</p>

                    <div class="text-sm text-left w-full space-y-1 mb-4">
                        <p><strong>Email: </strong>
                            <a :href="`mailto:${member.email}`" class="emailColor underline">{{ member.email }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>


<script setup>
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