<script setup>
import SliderWrapper from '@/Components/Frontend/SliderWrapper.vue'
import TeamMemberModal from '@/Components/Frontend/ProfileModal.vue'
defineProps({ members: Array })

import { ref } from 'vue'

const showModal = ref(false)
const selectedMember = ref(null)

function openModal(member) {
    selectedMember.value = member
    showModal.value = true
}

function closeModal() {
    showModal.value = false
}
</script>

<template>
    <div class="bg-heavy text-bison">
        <section class="container mx-auto px-4 py-16 ">
            <h2 class="text-4xl font-bold text-left mb-12 text-bison">Meet the Team</h2>
            <SliderWrapper :items="members" :per-page="4" gap="1.5rem">
                <template #default="{ item }">
                    <div class="relative group overflow-hidden rounded-xl mb-10 transition duration-500"
                        data-aos="fade-up" @click="openModal(item)">
                        <!-- Image -->
                        <img :src="item.photo" :alt="item.name"
                            class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105" />

                        <!-- Dark overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 to-black/10 transition-opacity opacity-0 group-hover:opacity-100 duration-300">
                        </div>

                        <!-- Content on hover -->
                        <div class="absolute inset-0 flex flex-col justify-center items-center text-white px-4 group">
                            <!-- ✨ Name (centered by default, moves up on hover) -->
                            <h2
                                class="text-2xl md:text-3xl font-extrabold tracking-widest transition-all duration-300 transform group-hover:-translate-y-10">
                                {{ item.name }}
                            </h2>

                            <!-- ✨ Hover Buttons -->
                            <div
                                class="absolute bottom-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col gap-3 w-full px-6">
                                <p class="text-bison mt-2 text-xl">{{ item.designation }}</p>
                            </div>
                        </div>
                    </div>
                </template>
            </SliderWrapper>
        </section>
    </div>
    <!-- Modal -->
    <TeamMemberModal :visible="showModal" :member="selectedMember" @close="closeModal" />
</template>
<style scoped>
.custom-splide>>>.splide__arrow {
    background: #C3BBA4;
    border-radius: 9999px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
    width: 2.5rem;
    height: 2.5rem;
}

.custom-splide>>>.splide__pagination__page {
    width: 14px !important;
    height: 14px !important;
    background-color: #323B2F !important;
}

.custom-splide>>>.splide__pagination__page.is-active {
    background-color: #ccc !important;
}
</style>
