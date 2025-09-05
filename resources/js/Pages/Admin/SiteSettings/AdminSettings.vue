<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '../Layout.vue'
import FooterSettings from './Partials/FooterSettings.vue'
import SiteSettings from './Partials/SiteSettings.vue'

const props = defineProps({
    contact: {
        type: Object,
        default: () => ({})
    },
    social: {
        type: Array,
        default: () => []
    },
    seo: {
        type: Object,
        default: () => []
    }
})


const currentTab = ref('site')

const components = {
    site: SiteSettings,
    footer: FooterSettings,
    // future: general: GeneralSettings, etc.
}

const CurrentComponent = computed(() => components[currentTab.value] || null)
</script>
<template>
    <AdminLayout title="Site Settings">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside class="w-64 bg-gray-100 p-4 border-r">
                <h2 class="text-lg font-bold mb-4">Settings</h2>
                <ul class="space-y-2">
                    <li>
                        <button @click="currentTab = 'site'" :class="{ 'font-bold text-envy': currentTab === 'site' }">
                            Site Settings
                        </button>
                    </li>
                    <li>
                        <button @click="currentTab = 'footer'"
                            :class="{ 'font-bold text-envy': currentTab === 'footer' }">
                            Footer Settings
                        </button>
                    </li>
                </ul>
            </aside>

            <!-- Dynamic Component -->
            <div class="flex-1 p-8">
                <component :is="CurrentComponent" :contact="props.contact" :social="props.social" :seo="props.seo" />
            </div>
        </div>
    </AdminLayout>
</template>