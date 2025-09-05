<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
    terms: { type: Object, default: null },           // { body, boxes[], is_active }
    modelValue: { type: Array, default: () => [] },   // booleans per enabled box index
    disabled: { type: Boolean, default: false },      // disable interactions (e.g. while submitting)
    error: { type: String, default: '' },             // optional error message to display
    title: { type: String, default: 'Terms & Conditions' }
})
const emit = defineEmits(['update:modelValue'])

function toggle(i, val) {
    if (props.disabled) return
    const arr = [...(props.modelValue ?? [])]
    arr[i] = !!val
    emit('update:modelValue', arr)
}

// Enabled boxes (dense indices for modelValue)
const enabledBoxes = computed(() => {
    const boxes = Array.isArray(props.terms?.boxes) ? props.terms.boxes : []
    return boxes.filter(b => b && typeof b === 'object' && b.enabled)
})

const expanded = ref(false)
const hasRequired = computed(() => enabledBoxes.value.some(b => b.required))
const selectedCount = computed(() => (props.modelValue || []).filter(Boolean).length)
const requiredCount = computed(() => enabledBoxes.value.filter(b => b.required).length)
</script>

<template>
    <div v-if="terms && terms.is_active" class="w-full">
        <!-- Card -->
        <section class="relative rounded-2xl border border-heavy bg-white shadow-sm"
            :class="[{ 'opacity-60 pointer-events-none': disabled }]">
            <!-- Header -->
            <header class="flex items-start gap-3 px-5 pt-5">
                <div class="shrink-0 mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-bison/10">
                    <!-- subtle shield icon (SVG) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-bison" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path d="M12 2l7 3v6c0 5.55-3.84 10.74-7 12-3.16-1.26-7-6.45-7-12V5l7-3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Please review and confirm the statements below to continue.
                    </p>
                </div>

                <!-- Progress chip -->
                <div class="ml-auto rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700"
                    :title="hasRequired ? 'Required items must be checked' : 'Optional confirmations'">
                    {{ selectedCount }} / {{ enabledBoxes.length }} selected
                </div>
            </header>

            <!-- Terms body -->
            <div class="px-5 mt-4">
                <div class="relative rounded-xl border border-bison bg-gray-50">
                    <div class="prose max-w-none px-4 py-4 text-gray-800" :class="expanded ? '' : 'line-clamp-5'"
                        v-html="terms.body" />
                    <div class="flex justify-end px-4 pb-4 -mt-2">
                        <button type="button" class="text-sm font-medium text-bison hover:underline focus:outline-none"
                            @click="expanded = !expanded">
                            {{ expanded ? 'Show less' : 'Read full terms' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="px-5 py-5">
                <div class="space-y-3">
                    <div v-for="(box, i) in enabledBoxes" :key="i"
                        class="group relative flex gap-3 rounded-xl border border-bison bg-white px-4 py-3 hover:shadow-sm transition-shadow">
                        <div class="">
                            <input :id="`agree-${i}`" type="checkbox"
                                class="h-5 w-5 rounded-md border-gray-300 focus:ring-2 focus:ring-bison focus:ring-offset-1"
                                :checked="!!(modelValue && modelValue[i])" :disabled="disabled"
                                @change="toggle(i, $event.target.checked)" />
                        </div>

                        <label :for="`agree-${i}`" class="flex-1 cursor-pointer select-none text-sm text-gray-800">
                            <span class="font-medium">{{ box.label }}</span>
                            <span v-if="box.required"
                                class="ml-2 inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-600"
                                title="Required to proceed">
                                Required
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Footer helper / error -->
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        <template v-if="hasRequired">
                            Please check all required statements ({{ requiredCount }}) to proceed.
                        </template>
                        <template v-else>
                            All confirmations here are optional.
                        </template>
                    </p>

                    <p v-if="error" class="text-xs font-medium text-rose-600">
                        {{ error }}
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
/* line clamp utility for prose (if not using @tailwind line-clamp plugin) */
.line-clamp-5 {
    display: -webkit-box;
    -webkit-line-clamp: 5;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
