<script setup>
import Layout from '@/layouts/FrontendLayout.vue'
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Layout>

        <Head title="Register" />
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
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Name" />

                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                                autofocus autocomplete="name" />

                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="email" value="Email" />

                            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                                autocomplete="username" />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password" value="Password" />

                            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                                required autocomplete="new-password" />

                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password_confirmation" value="Confirm Password" />

                            <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                                v-model="form.password_confirmation" required autocomplete="new-password" />

                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <PrimaryButton class="mt-4 w-full justify-center bg-bison text-heavy hover:bg-white"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Register
                        </PrimaryButton>
                        <p class="mt-4 text-center text-white text-sm">
                            <Link :href="route('login')"
                                class="font-semibold underline hover:text-bison transition">
                            Already registered?
                            </Link>
                        </p>
                    </form>
                </div>
            </div>
        </section>
    </Layout>
</template>
