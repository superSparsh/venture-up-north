<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import Layout from '@/layouts/FrontendLayout.vue'
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Layout>

        <Head title="Log in" />

        <section class="relative min-h-screen flex items-center justify-center text-white">
            <!-- 🎥 Background Video -->
            <video class="absolute inset-0 w-full h-full object-cover z-0" autoplay loop muted playsinline>
                <source src="/public/videos/ventureupnorth-HomePage-Video.mp4" type="video/mp4" />
            </video>

            <!-- 🟤 Dark Overlay -->
            <div class="absolute inset-0 bg-black/50 z-0"></div>

            <!-- 🔐 Login Card -->
            <div class="relative z-10 w-full max-w-md px-4" data-aos="fade-up">
                <div class="bg-white/20 backdrop-blur-md shadow-xl rounded-xl p-6 space-y-6">
                    <!-- ✅ Status -->
                    <div v-if="status" class="mb-4 text-sm font-medium text-green-300 text-center">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- 📧 Email -->
                        <div>
                            <InputLabel for="email" value="Email" class="text-white" />
                            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                                autofocus autocomplete="username" />
                            <InputError class="mt-2 text-red-300" :message="form.errors.email" />
                        </div>

                        <!-- 🔑 Password -->
                        <div>
                            <InputLabel for="password" value="Password" class="text-white" />
                            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                                required autocomplete="current-password" />
                            <InputError class="mt-2 text-red-300" :message="form.errors.password" />
                        </div>

                        <!-- ✅ Remember + Reset -->
                        <div class="flex items-center justify-between text-sm text-white">
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.remember" />
                                <span class="ml-2">Remember me</span>
                            </label>

                            <Link v-if="canResetPassword" :href="route('password.request')" class="hover:underline">
                            Forgot password?
                            </Link>
                        </div>

                        <!-- 🔘 Submit -->
                        <PrimaryButton class="w-full justify-center bg-bison text-heavy hover:bg-white"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Log in
                        </PrimaryButton>

                        <!-- 🆕 Register Link -->
                        <p class="mt-4 text-center text-white text-sm">
                            Don’t have an account?
                            <Link :href="route('register')" class="font-semibold underline hover:text-bison transition">
                            Register here
                            </Link>
                        </p>
                    </form>
                </div>
            </div>
        </section>
    </Layout>
</template>
