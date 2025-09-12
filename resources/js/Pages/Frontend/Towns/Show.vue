<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/layouts/FrontendLayout.vue'
import EditorJSHTML from 'editorjs-html'
import { computed } from 'vue'
import ExploreOtherTowns from '@/Components/Frontend/ExploreOtherTowns.vue'
import SeoMeta from '@/Components/Frontend/SeoMeta.vue'

const props = defineProps({
    town: {
        type: Object,
        required: true,
    },
    tourImage: String,
    accommodationImage: String,
    allTowns: Object,
    tags: Object
})

const edjsParser = EditorJSHTML({
    linkTool: (block) => {
        const { link, meta } = block.data
        const title = meta?.title || link
        const description = meta?.description || ''
        const imageUrl = meta?.image?.url

        return `
      <a href="${link}" target="_blank" rel="noopener noreferrer" class="block rounded hover:text-heavy transition bg-white no-underline">
        <div class="text-lg font-semibold text-blue-600">${title}</div>
        ${imageUrl ? `<img src="${imageUrl}" class="mt-2 object-contain rounded" />` : ''}
      </a>
    `
    }
})

const renderedDescription = computed(() => {
    if (!props.town.description) return ''
    try {
        const json = typeof props.town.description === 'string'
            ? JSON.parse(props.town.description)
            : props.town.description

        const parsedBlocks = edjsParser.parse(json)
        let html = Array.isArray(parsedBlocks) ? parsedBlocks.join('') : parsedBlocks

        // Inject your custom block before the first <a> tag
        const tourHtml = `
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-5">

    <!-- Tours -->
    <a href="/tours?town=${props.town.slug}"
       class="relative block h-72 overflow-hidden rounded-2xl group hover:text-bison transition-all duration-300">
       
      <img src="${props.tourImage}" alt="${props.town.name}"
           class="absolute inset-0 w-full h-full object-cover rounded-2xl transition-transform duration-500 group-hover:text-bison" />

      <!-- Gradient bottom overlay -->
      <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 via-black/40 to-transparent z-10 rounded-b-2xl"></div>

      <!-- Text on bottom left -->
      <div class="absolute bottom-5 left-5 z-20 ">
        <h3 class="text-white text-xl font-bold leading-snug drop-shadow-sm  hover:text-bison">
          Find Tours in <br /> ${props.town.name}
        </h3>
      </div>
    </a>

    <!-- Accommodation -->
    <a href="/explore/accommodation-up-north?town=${props.town.slug}"
       class="relative block h-72 overflow-hidden rounded-2xl shadow-md group hover:text-bison transition-all duration-300">

      <img src="${props.accommodationImage}" alt="${props.town.name}"
           class="absolute inset-0 w-full h-full object-cover rounded-2xl transition-transform duration-500 group-hover:text-bison" />

      <!-- Gradient bottom overlay -->
      <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 via-black/40 to-transparent z-10 rounded-b-2xl"></div>

      <!-- Text on bottom left -->
      <div class="absolute bottom-5 left-5 z-20">
        <h3 class="text-white text-xl font-bold leading-snug drop-shadow-sm hover:text-bison">
          Find Accommodation in <br /> ${props.town.name}
        </h3>
      </div>
    </a>

  </div>
`




        const firstAnchor = html.indexOf('<a ')
        if (firstAnchor !== -1) {
            html = html.slice(0, firstAnchor) + tourHtml + html.slice(firstAnchor)
        } else {
            html += tourHtml // fallback: add at the end
        }

        return html
    } catch (e) {
        console.error('Failed to parse Editor.js content', e)
        return ''
    }
})

</script>

<template>
    <Layout>

        <Head :title="town.name" />
        <SeoMeta :title="`Explore ${town.name} - Venture Up North`" :description="town.summary"
            :image="town.seo_image" :canonical="`https://ventureupnorth.com/towns/${town.slug}`" :index="true"
            :follow="true" />

        <!-- Hero Section -->
        <section class="relative w-full h-screen overflow-hidden text-white">
            <!-- Background Image -->
            <img :src="town.big_hero_image" :alt="town.name" class="absolute inset-0 w-full h-full object-cover z-0" />

            <!-- Dark overlay (optional for better text readability) -->
            <div class="absolute inset-0 bg-black/40 z-10"></div>

            <!-- Content Wrapper -->
            <div class="relative z-20 h-full flex items-center" data-aos="fade-up">
                <div class="container mx-auto px-4">
                    <h1 class="text-white text-4xl md:text-6xl font-extrabold tracking-widest uppercase">
                        {{ town.name }}
                    </h1>
                </div>
            </div>
        </section>
        <!-- Description Section -->
        <section class="container mx-auto px-4" data-aos="fade-up">
            <div class="component component-text component-text-introduction lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0">
                <div class="text-content mt-8 lg:mt-10 text-xl text-gray-700 font-medium">
                    <p class="text-lg text-bison leading-relaxed max-w-3xl font-bold tracking-wide">
                        {{ town.summary }}
                    </p>
                </div>
            </div>
            <div class="component component-text lg:w-3/4 xl:w-1/2 lg:mx-auto px-5 lg:px-0 mt-8 lg:mt-10">
                <article
                    class="prose prose-lg text-heavy first-letter:text-6xl first-letter:leading-none first-letter:float-left first-letter:pr-2 first-letter:font-serif text-md tracking-small leading-6 mb-10"
                    v-html="renderedDescription">
                </article>
                <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Link :href="`/tours?town=${town.slug}`"
                        class="block relative group overflow-hidden rounded-xl shadow-lg transition duration-300 hover:shadow-xl">
                    <img :src="tourImage" :alt="town.name"
                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />

                    <div
                        class="absolute inset-0 bg-black/40 flex items-center justify-center text-white px-4 text-center text-2xl font-bold tracking-wide leading-snug break-words">
                        Find Tours in <br /> {{ town.name }}
                    </div>
                    </Link>

                    <Link :href="`/explore/accommodation-up-north?town=${town.slug}`"
                        class="block relative group overflow-hidden rounded-xl shadow-lg transition duration-300 hover:shadow-xl">
                    <img :src="accommodationImage" :alt="town.name"
                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />

                    <div
                        class="absolute inset-0 bg-black/40 flex items-center justify-center text-white px-4 text-center text-2xl font-bold tracking-wide leading-snug break-words">
                        Find Accommodation in <br /> {{ town.name }}
                    </div>
                    </Link>
                </div> -->

            </div>
        </section>
        <ExploreOtherTowns :allTowns="allTowns" :currentSlug="town.slug" :tags="tags" />
    </Layout>
</template>
