<script setup>
import { Head } from '@inertiajs/vue3'
import { onMounted } from 'vue'

onMounted(() => {
    if (props.structuredData) {
        const tag = document.createElement('script')
        tag.setAttribute('type', 'application/ld+json')
        tag.textContent = JSON.stringify(props.structuredData)
        document.head.appendChild(tag)
    }
})

const props = defineProps({
    title: String,
    description: String,
    image: String,
    site_name: String,
    canonical: {
        type: String,
        default: () => window.location.href,
    },
    type: {
        type: String,
        default: 'website',
    },
    index: {
        type: Boolean,
        default: true,
    },
    follow: {
        type: Boolean,
        default: true,
    },
    publishedAt: String, // Optional
    structuredData: Object // Optional JSON-LD
})

const robotsContent = `${props.index ? 'index' : 'noindex'}, ${props.follow ? 'follow' : 'nofollow'}`
</script>

<template>

    <Head>
        <!-- Standard Meta -->
        <meta name="description" :content="description" />
        <meta name="robots" :content="robotsContent" />
        <link rel="canonical" :href="canonical" />

        <!-- Open Graph -->
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" :content="image" />
        <meta property="og:url" :content="canonical" />
        <meta property="og:type" :content="type" />
        <meta property="article:published_time" :content="publishedAt" v-if="publishedAt" />
        <meta property="article:author" content="Venture Up North" />
        <meta property="og:image:alt" :content="title" />
        <meta property="og:site_name" :content="site_name"> 

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" :content="image" />
    </Head>
</template>
