<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth?.user ?? {}
const form = useForm({
  name: user.name ?? '',
  display_name: user.display_name ?? '',
  email: user.email ?? '',
  photo: null,
})

function handlePhoto(e) {
    form.photo = e.target.files[0];
}

const submit = () => {
    form.post(route('contributor.profile.update'), {
        forceFormData: true,
        preserveScroll: true,
    })
}

</script>

<template>
    <section>
        <header>
            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6" enctype="multipart/form-data">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="display_name" value="Display Name" />

                <TextInput id="display_name" type="text" class="mt-1 block w-full" v-model="form.display_name" required autofocus
                    autocomplete="display_name" />

                <InputError class="mt-2" :message="form.errors.display_name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>


            <div>
                <InputLabel for="photo" value="Photo" />
                <input id="photo" type="file" accept="image/*" class="mt-1 block w-full" @change="handlePhoto" />
                <InputError class="mt-2" :message="form.errors.photo" />

                <div v-if="user.photo" class="mt-2">
                    <img :src="`/public/storage/${user.photo}`" alt="Current Photo" class="h-24 rounded shadow" />
                </div>
            </div>


            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Click here to re-send the verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
