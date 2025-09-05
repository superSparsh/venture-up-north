<script setup>
import ContributorLayout from '@/Layouts/ContributorLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const contributor = computed(() => page.props.contributor || {
    status: 'active',
    submissions_count: 0,
    approved_count: 0,
    rejected_count: 0,
    last_submission_at: null
})

const total = computed(() => contributor.value.submissions_count || 0)
const approved = computed(() => contributor.value.approved_count || 0)
const rejected = computed(() => contributor.value.rejected_count || 0)
const pending = computed(() => Math.max(total.value - approved.value - rejected.value, 0))

const approvalRate = computed(() => {
    const t = approved.value + rejected.value
    return t ? Math.round((approved.value / t) * 100) : 0
})
</script>

<template>

    <Head title="Dashboard" />

    <ContributorLayout>
        <!-- Branded background -->


        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

            <!-- Status banner -->
            <div v-if="contributor.status === 'blocked'"
                class="mb-6 rounded-2xl border border-red-200/60 bg-red-50/90 px-4 py-3 text-red-800 shadow-sm backdrop-blur">
                Your contributor account is <strong>blocked</strong>. You can’t submit or edit content. Please
                contact admin.
            </div>

            <!-- Welcome / hero -->
            <div
                class="relative overflow-hidden rounded-2xl bg-white/10 backdrop-blur-md p-6 text-white shadow-xl ring-1 ring-white/15">
                <div class="relative z-10">
                    <h1 class="text-2xl font-semibold">Welcome, {{ user.name }}</h1>
                    <!-- <p class="mt-1 text-white/80">Track your submissions and start a new one below.</p> -->

                    <div class="mt-5 flex flex-wrap gap-3">
                        <!-- Restore these when routes are ready -->
                        <!--
                <Link :href="route('user.blogs.create')"
                  class="rounded-xl bg-white text-heavy px-4 py-2 font-medium shadow hover:bg-white/90">
                  ✍️ Submit Blog
                </Link>
                <Link :href="route('user.events.create')"
                  class="rounded-xl bg-white/10 px-4 py-2 font-medium text-white ring-1 ring-white/30 hover:bg-white/20">
                  📅 Submit Event
                </Link>
                -->
                    </div>
                </div>
                <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/15 blur-2xl">
                </div>
            </div>

            <!-- Stat cards -->
            <!-- <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-white/80 backdrop-blur p-5 shadow ring-1 ring-black/5">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Submissions</div>
                    <div class="mt-1 text-3xl font-semibold text-heavy">{{ total }}</div>
                </div>

                <div class="rounded-2xl bg-white/80 backdrop-blur p-5 shadow ring-1 ring-black/5">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Approved</div>
                    <div class="mt-1 text-3xl font-semibold text-emerald-600">{{ approved }}</div>
                </div>

                <div class="rounded-2xl bg-white/80 backdrop-blur p-5 shadow ring-1 ring-black/5">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Pending Review</div>
                    <div class="mt-1 text-3xl font-semibold text-amber-600">{{ pending }}</div>
                </div>

                <div class="rounded-2xl bg-white/80 backdrop-blur p-5 shadow ring-1 ring-black/5">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Rejected</div>
                    <div class="mt-1 text-3xl font-semibold text-rose-600">{{ rejected }}</div>
                </div>
            </div> -->
        </div>

    </ContributorLayout>
</template>
