<template>
    <Head title="Divisions" />
    <DefaultLayout>
        <section id="section1">
            <div
                class="bg-cover bg-center bg-[url('/storage/images/pemandangan.jpg')]"
            >
                <div
                    class="container-hebron h-screen flex md:py-20 items-center"
                >
                    <p
                        class="text-white text-5xl md:text-7xl md:px-40 font-playfair w-full text-center md:text-left"
                    >
                        Our Divisions
                    </p>
                </div>
            </div>
        </section>

        <section id="section2">
            <div
                v-for="(division, index) in props.data"
                :key="division.id || index"
            >
                <DivisionCard
                    :index="index"
                    :title="division.title"
                    :description="division.description"
                />
            </div>
        </section>
    </DefaultLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import DefaultLayout from "@/Layouts/DefaultLayout.vue";
import DivisionCard from "@/Components/DivisionCard.vue";
const props = defineProps(["data"]);
</script>
