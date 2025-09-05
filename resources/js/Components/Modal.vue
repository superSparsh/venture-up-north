<template>
    <TransitionRoot appear :show="modelValue" as="template">
        <Dialog as="div" class="relative z-50" @close="$emit('update:modelValue', false)">
            <TransitionChild enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" />
            </TransitionChild>

            <div class="fixed inset-0 flex items-center justify-center p-4" >
                <TransitionChild enter="ease-out duration-300" enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100" leave="ease-in duration-200" leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95">
                    <DialogPanel class="w-full max-w-sm bg-white p-6 rounded shadow-xl transition-all">
                        <DialogTitle class="text-lg font-bold text-gray-900">
                            {{ title }}
                        </DialogTitle>

                        <div class="mt-2 text-sm text-gray-700">
                            <slot />
                        </div>

                        <div class="mt-4 flex justify-end space-x-2">
                            <slot name="footer" v-if="$slots.footer" />
                            <template v-else>
                                <button @click="$emit('update:modelValue', false)"
                                    class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200">
                                    Cancel
                                </button>
                                <button @click="$emit('confirm')"
                                    class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                                    Confirm Delete
                                </button>
                            </template>
                        </div>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

defineProps({
    modelValue: Boolean,
    title: String,
})
</script>