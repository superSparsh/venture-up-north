<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import AdminLayout from '../Layout.vue'
import { useToast } from 'vue-toastification'

const props = defineProps({ terms: Object })
const toast = useToast()

// helper: cast values coming from DB/JSON into proper shapes
const normalizeBoxes = (boxes = []) =>
    boxes.map(b => ({
        label: b?.label ?? '',
        enabled: b?.enabled === true || b?.enabled === 1 || b?.enabled === '1',
        required: b?.required === true || b?.required === 1 || b?.required === '1',
    }))

const form = useForm({
    body: '',
    is_active: true,
    boxes: []
})

// hydrate on first render AND whenever server props change (partial reloads, back/forward nav, etc)
watch(
    () => props.terms,
    (t) => {
        form.defaults({
            body: t?.body ?? '',
            is_active: !!t?.is_active,
            boxes: normalizeBoxes(t?.boxes ?? []),
        })
        // reset to defaults so UI shows the prefilled values
        form.reset()
    },
    { immediate: true }
)

function addBox() {
    form.boxes.push({ label: '', enabled: false, required: false })
}
function removeBox(i) {
    form.boxes.splice(i, 1)
}

const enabledCount = computed(() => form.boxes.filter(b => b.enabled).length)

// keep required=false when a box is disabled
watch(
    () => form.boxes.map(b => b.enabled),
    () => form.boxes.forEach(b => { if (!b.enabled) b.required = false }),
    { deep: true }
)

function save() {
    form.put(route('admin.event.terms.update'), {
        onSuccess: () => toast.success('Terms & Conditions updated successfully!'),
        onError: () => toast.error('Failed to update terms & condition'),
    })
}
console.log(props.terms)
</script>


<template>
    <AdminLayout>

        <Head title="Terms & Conditions" />
        <div class="max-w-4xl mx-auto space-y-6">
            <h1 class="text-2xl font-bold">Terms & Conditions (Contributor Submission)</h1>

            <div class="bg-white p-6 rounded-xl shadow space-y-4" :key="JSON.stringify(terms)">
                <label class="block text-sm font-medium">Terms Body (Markdown/HTML)</label>
                <textarea v-model="form.body" rows="8" class="w-full rounded border p-3"></textarea>

                <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" v-model="form.is_active" id="active" />
                    <label for="active">Active</label>
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold">Tick Boxes (you can enable up to 3)</h2>
                        <div class="text-sm text-gray-600">{{ enabledCount }}/3 selected</div>
                        <button type="button" class="px-3 py-2 rounded bg-bison text-white" @click="addBox">
                            + Add Box
                        </button>
                    </div>

                    <div class="space-y-4 mt-4">
                        <div v-for="(box, i) in form.boxes" :key="i" class="border rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <!-- Disable enabling if already at 3 and this one is currently off -->
                                <input type="checkbox" v-model="box.enabled" :id="`enabled-${i}`"
                                    :disabled="!box.enabled && enabledCount >= 3" />
                                <label :for="`enabled-${i}`" class="mr-6">Enabled</label>

                                <input type="checkbox" v-model="box.required" :id="`required-${i}`"
                                    :disabled="!box.enabled" />
                                <label :for="`required-${i}`">Required</label>

                                <button type="button" class="ml-auto text-red-600" @click="removeBox(i)">Remove</button>
                            </div>

                            <input v-model="box.label" type="text" class="w-full rounded border p-2"
                                placeholder="Checkbox label e.g., I have read and agree to T&Cs" />
                        </div>
                    </div>

                    <div v-if="form.errors && Object.keys(form.errors).length" class="text-red-600 mt-2 text-sm">
                        <div v-for="(msg, k) in form.errors" :key="k">{{ msg }}</div>
                    </div>

                    <div class="mt-6 text-end">
                        <button @click="save" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-md shadow">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
