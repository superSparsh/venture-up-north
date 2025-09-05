<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <div class="p-6 text-heavy text-2xl">
                        Welcome, {{ $page.props.auth.user.name }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
