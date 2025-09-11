<template>
    <div class="min-h-screen flex bg-[#f9fafb]">
        <!-- Dark Sidebar -->
        <aside class="w-64 bg-gray text-heavy shadow-lg font-semibold tracking-wide">
            <div class="p-6 border-b border-gray-800">
                <h1 class="text-xl text-center font-semibold">Venture Up North</h1>
                <h2 class="text-center text-xl font-semibold">Admin Panel</h2>
            </div>
            <nav class="p-4 space-y-2 text-sm">
                <Link :href="route('admin.dashboard')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url === '/admin/dashboard' }">
                Dashboard
                </Link>
                <Link :href="route('admin.home-sections.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/home-sections') }">
                Manage Home Sections
                </Link>
                <Link :href="route('admin.team-members.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/team-members') }">
                Manage Team Members
                </Link>
                <Link :href="route('admin.contributors.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/contributors') }">
                Manage Contributors
                </Link>
                <Link :href="route('admin.venture-magazines.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/venture-magazines') }">
                Manage Venture Magazines
                </Link>
                <Link :href="route('admin.towns.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/towns') }">
                Manage Towns
                </Link>
                <Link :href="route('admin.experiences.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/experiences') }">
                Manage Experiences
                </Link>
                <Link :href="route('admin.events.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/events') }">
                Manage Events
                </Link>
                <Link :href="route('admin.tour-tiles.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/tour-tiles') }">
                Manage Tour Listings
                </Link>
                <!-- Manage Contents with submenu -->
                <div>
                    <button @click="toggleMenu('contents')" type="button"
                        class="w-full text-left px-4 py-2 rounded-md hover:bg-bison flex items-center justify-between"
                        :class="{ 'bg-bison font-semibold': openMenus.contents }">
                        Manage Contents
                        <svg class="w-4 h-4 ml-2 transform transition-transform duration-200"
                            :class="{ 'rotate-90': openMenus.contents }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div v-if="openMenus.contents" class="ml-4 space-y-1 mt-1">
                        <Link :href="route('admin.categories.index')" class="block px-4 py-2 rounded hover:bg-bison"
                            :class="{ 'bg-bison font-semibold': pageUrl.startsWith('/admin/categories') }">
                        Contents
                        </Link>
                        <Link :href="route('admin.category-listings.index')"
                            class="block px-4 py-2 rounded hover:bg-bison"
                            :class="{ 'bg-bison font-semibold': pageUrl.startsWith('/admin/category-listings') }">
                        Content Listings
                        </Link>
                        <Link :href="route('admin.collections.index')" class="block px-4 py-2 rounded hover:bg-bison"
                            :class="{ 'bg-bison font-semibold': pageUrl.startsWith('/admin/collections') }">
                        Content Collections
                        </Link>
                    </div>
                </div>
                <!-- Manage Contents with submenu -->
                <div>
                    <button @click="toggleMenu('homepage')" type="button"
                        class="w-full text-left px-4 py-2 rounded-md hover:bg-bison flex items-center justify-between"
                        :class="{ 'bg-bison font-semibold': openMenus.homepage }">
                        Manage Home Page
                        <svg class="w-4 h-4 ml-2 transform transition-transform duration-200"
                            :class="{ 'rotate-90': openMenus.homepage }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div v-if="openMenus.homepage" class="ml-4 space-y-1 mt-1">
                        <Link :href="route('admin.indulge-links.index')" class="block px-4 py-2 rounded hover:bg-bison"
                            :class="{ 'bg-bison font-semibold': pageUrl.startsWith('/admin/indulge-links') }">
                        Manage Indulge Menu Links
                        </Link>
                    </div>
                    <div v-if="openMenus.homepage" class="ml-4 space-y-1 mt-1">
                        <Link :href="route('admin.explore-tiles.index')" class="block px-4 py-2 rounded hover:bg-bison"
                            :class="{ 'bg-bison font-semibold': pageUrl.startsWith('/admin/explore-tiles') }">
                        Image Tiles
                        </Link>
                    </div>
                </div>
                <Link :href="route('admin.tags.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/tags') }">
                Manage Classifications
                </Link>

                <Link :href="route('admin.contacts.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/contacts') }">
                Manage Contact Submissions
                </Link>
                <Link :href="route('admin.terms.edit')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/terms-conditions') }">
                Manage Magazines Terms & Conditions
                </Link>
                <Link :href="route('admin.event.terms.edit')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/event-terms-conditions') }">
                Manage Events Terms & Conditions
                </Link>
                <Link :href="route('admin.popups.edit')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/popups') }">
                Manage Events & Magazine Popup
                </Link>
                <Link :href="route('admin.ventures.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/ventures') }">
                Manage Ventures
                </Link>
                <Link :href="route('admin.settings.index')" class="block px-4 py-2 rounded-md hover:bg-bison"
                    :class="{ 'bg-bison font-semibold': $page.url.startsWith('/admin/site/settings') }">
                Manage Site Settings
                </Link>
            </nav>

            <!-- Logout -->
            <div class="mt-auto p-4 border-t border-gray-700">
                <button @click="logout"
                    class="w-full text-left px-4 py-2 rounded-md text-red-400 hover:bg-red-600 hover:text-heavy font-medium">
                    Logout
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 px-8 py-6 text-gray-800">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { router, Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const page = usePage()
const pageUrl = computed(() => page.url)

const openMenus = ref({
    contents: pageUrl.value.startsWith('/admin/categories') ||
        pageUrl.value.startsWith('/admin/category-listings') ||
        pageUrl.value.startsWith('/admin/collections'),

    homepage: pageUrl.value.startsWith('/admin/explore-tiles'),
})


const toggleMenu = (key) => {
    openMenus.value[key] = !openMenus.value[key]
}

function logout() {
    router.post(route('logout'))
}
</script>