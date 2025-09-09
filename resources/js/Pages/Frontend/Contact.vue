<script setup>
import { useForm } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    captcha: '',
})

const toast = useToast()

const props = defineProps({
    captcha_question: String,
})

async function submit() {
    form.post(route('contact.submit'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success('Your message has been sent!')
            form.reset()
        },
        onError: () => toast.error('Failed to send message'),
    })
}
</script>

<template>
    <Layout>

        <Head :title="`Contact Us`" />
        <SeoMeta :title="`Contact Us - Venture Up North`"
            :description="`Have a question, suggestion, or partnership idea? We'd love to hear from you!
Whether you're planning your next adventure or want to collaborate, reach out and our team will get back to you as soon as possible.s`"
            :image="`/public/images/Venture-Up-North.png`" :canonical="`https://ventureupnorth.com/towns/contact`"
            :index="true" :follow="true" />
        <div class="min-h-screen bg-white flex flex-col">
            <!-- Hero Section -->
            <section class="relative w-full h-screen overflow-hidden text-white">
                <!-- Background Image -->
                <img src="/public/images/footer-bg.jpg" alt="Contact Us"
                    class="absolute inset-0 w-full h-full object-cover z-0" />

                <!-- Dark overlay (optional for better text readability) -->
                <div class="absolute inset-0 bg-black/40 z-10"></div>

                <!-- Content Wrapper -->
                <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                    <div class="container mx-auto px-4">
                        <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                            Contact Us
                        </h1>
                    </div>
                </div>
            </section>

            <section class="container mx-auto px-4 py-12 mt-10 mb-10 max-w-3xl bg-bison text-heavy rounded-lg"
                data-aos="fade-up">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Name <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded" />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.email" type="email" class="w-full px-4 py-2 border rounded" />
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Subject</label>
                        <input v-model="form.subject" type="text" class="w-full px-4 py-2 border rounded" />
                        <p v-if="form.errors.subject" class="text-red-500 text-sm mt-1">{{ form.errors.subject }}</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Message <span
                                class="text-red-500">*</span></label>
                        <textarea v-model="form.message" rows="5" class="w-full px-4 py-2 border rounded"></textarea>
                        <p v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</p>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">
                            What is {{ captcha_question }}?
                        </label>
                        <input v-model="form.captcha" type="text" class="w-full px-4 py-2 border rounded" />
                        <p v-if="form.errors.captcha" class="text-red-500 text-sm mt-1">{{ form.errors.captcha }}</p>
                    </div>
                    <div class="w-full text-right">
                        <button type="submit" :disabled="form.processing"
                            class="px-6 py-2 bg-heavy text-white rounded hover:bg-teal-700 transition">
                            {{ form.processing ? 'Sending...' : 'Send Message' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </Layout>
</template>
