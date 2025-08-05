<template>

    <Head title="Divisions" />
    <DefaultLayout>
        <section id="section1" class="bg-black bg-cover bg-center"
            :style="{ backgroundImage: 'url(' + (hero.image ? '/storage/' + hero.image : '/storage/images/pemandangan.jpg') + ')' }">
            <div class="min-h-[40vh] max-w-screen-xl px-8 mx-auto flex justify-center items-center">
                <p class="text-white text-5xl">
                    {{ hero.title || 'Our Divisions' }}
                </p>
            </div>
        </section>

        <section id="section2">
            <div v-for="(division, index) in props.data" :key="division.id || index">
                <DivisionCard :index="index" :title="division.title" :description="division.description"
                    :image="division.image" />
            </div>
        </section>
    </DefaultLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import DefaultLayout from "@/Layouts/DefaultLayout.vue";
import DivisionCard from "@/Components/DivisionCard.vue";
const props = defineProps({
    data: Array,
    hero: Object // Define hero prop
});
</script>
