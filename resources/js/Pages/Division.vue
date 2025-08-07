<template>

    <Head title="Divisions" />
    <DefaultLayout>
        <section id="section1" class="bg-black bg-cover bg-center relative"
            :style="{ backgroundImage: `url(/storage/${hero.image})` }">
            <div class="absolute inset-0 bg-black opacity-50 mix-blend-multiply z-0"></div>
            <div class="relative min-h-[40vh] max-w-screen-xl px-8 mx-auto flex justify-center items-center pt-20 z-10">
                <p class="text-white text-5xl">
                    {{ hero.title || 'Our Divisions' }}
                </p>
            </div>
        </section>

        <section id="section2">
            <div v-for="(division, index) in props.data" :key="division.id || index">
                <DivisionCard :index="index" :title="division.title" :description="division.description"
                    :image="division.image" :link="division.link" :time="division.time" />
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
