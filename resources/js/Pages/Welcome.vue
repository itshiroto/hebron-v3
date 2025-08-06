<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Vue3Marquee } from "vue3-marquee";

import type { HomePageProps } from "@/types/home-page";

const props = withDefaults(defineProps<HomePageProps>(), {
    hero: () => ({
        title: "Welcome to Our Church",
        image: "",
        primary_button: { text: "Learn More", link: "#" },
        secondary_button: { text: "Contact Us", link: "#" },
    }),
    visionMission: () => ({
        title: "Our Vision & Mission",
        visi: { title: "Vision", desc: "To be a church that glorifies God." },
        misi: { title: "Mission", desc: "To make disciples of all nations." },
        about_button_text: "About Us",
    }),
    carousel: () => ([]),
    schedule: () => ({
        title: "Service Schedule",
        subtitle: "Join Us",
        img: "",
        services: [
            { title: "Sunday Service", time: "10:00 AM" },
        ],
        division_button_text: "View All Services",
    }),
    pastorGreeting: () => ({
        subtitle: "From Our Pastor",
        pastors_name: "Pastor John Doe",
        pastor_img: "",
        pastor_signature: "",
        desc: "Welcome to our church family! We are delighted to have you join us.",
        pastoral_button_text: "Meet Our Team",
    }),
});
</script>

<template>

    <Head title="Welcome" />
    <DefaultLayout>
        <section id="hero" class="relative h-screen text-white pt-20 bg-cover bg-center bg-black mb-24"
            :style="{ backgroundImage: `url(/storage/${hero.image})` }">
            <div class="relative h-full max-w-screen-xl mx-auto px-8 flex flex-col justify-center gap-8 z-10">
                <h1 class="text-5xl max-w-[50%]">{{ hero.title }}</h1>
                <div class="flex gap-4 font-sans">
                    <a :href="hero.primary_button.link">
                        <Button variant="secondary" class="bg-white text-black">
                            {{ hero.primary_button.text }}
                        </Button>
                    </a>
                    <a :href="hero.secondary_button.link">
                        <Button variant="secondary" class="hover:bg-white/20 border border-white text-white">
                            {{ hero.secondary_button.text }}
                        </Button>
                    </a>
                </div>
            </div>
            <div class="absolute inset-0 bg-black z-0 opacity-50 mix-blend-multiply"></div>
        </section>

        <section id="visi-misi" class="max-w-screen-xl mx-auto px-8 mb-32">
            <div class="mb-8">
                <p class="text-4xl mb-24 font-medium" v-html="visionMission.title"></p>

                <div class="flex flex-col md:flex-row gap-12 mb-24">
                    <div>
                        <p class="mb-4 text-gray-600 font-sans uppercase font-bold">{{ visionMission.visi.title }}</p>
                        <p class="text-xl leading-normal" v-html="visionMission.visi.desc"></p>
                    </div>
                    <div>
                        <p class="mb-4 text-gray-600 font-sans uppercase font-bold">{{ visionMission.misi.title }}</p>
                        <p class="text-xl leading-normal" v-html="visionMission.misi.desc"></p>
                    </div>
                </div>

                <a href="/about">
                    <Button
                        class="font-sans font-semibold text-sm rounded-full shadow-none bg-blue-600 hover:bg-blue-500 text-white uppercase tracking-tight"
                        size="lg">
                        {{ visionMission.about_button_text }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </Button>
                </a>
            </div>

        </section>

        <!-- Carousel -->
        <section id="carousel" class="mb-32">
            <Vue3Marquee class="w-full overflow-y-hidden" :speed="25" pause-on-hover :gradient="false">
                <img v-for="(image, index) in carousel" :key="index" :src="`/storage/${image}`"
                    class="h-80 object-cover mx-2 hover:scale-110 transition" alt="carousel image" />
            </Vue3Marquee>
        </section>

        <section id="services" class="max-w-screen-xl mx-auto px-8 mb-32">
            <div class="relative bg-black min-h-[512px] flex flex-col justify-end text-white p-4 md:p-8 mb-4 rounded-lg bg-cover bg-center overflow-hidden"
                :style="{ backgroundImage: `url(/storage/${schedule.img})` }">
                <div class="absolute inset-0 bg-gradient-to-b from-black/0 to-black/50"></div>
                <h4 class="relative z-10 font-sans text-sm md:text-lg uppercase font-bold">{{ schedule.subtitle }}</h4>
                <h2 class="relative z-10 text-3xl md:text-4xl">{{ schedule.title }}</h2>
            </div>
            <div class="flex flex-col md:flex-row gap-4 justify-stretch items-stretch font-sans min-h-24">
                <div v-for="(service, index) in schedule.services" :key="index"
                    class="bg-gray-100 hover:bg-gray-200 transition w-full py-4 px-4 flex flex-col justify-center rounded-lg cursor-default">
                    <p class="text-xl tracking-tight">{{ service.title }}</p>
                    <p class="text-3xl font-semibold">{{ service.time }}</p>
                </div>
                <a href="/division"
                    class="bg-blue-200 hover:bg-blue-300 hover:underline transition w-full py-8 md:py-4 px-4 flex flex-col justify-center cursor-pointer rounded-lg">
                    <p class="text-2xl font-semibold tracking-tighter leading-none">{{ schedule.division_button_text }}

                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </p>

                </a>
            </div>
        </section>

        <section id="sambutan-gembala" class="mb-32 bg-amber-50 lg:mx-16 lg:rounded-xl">
            <div class="max-w-screen-xl mx-auto px-8 py-8 lg:py-24 grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                <div class="h-full flex flex-col gap-8 order-2 lg:order-none">
                    <h4 class="font-sans text-sm lg:text-lg uppercase font-bold opacity-75">{{ pastorGreeting.subtitle
                        }}
                    </h4>
                    <h1 class="text-3xl lg:text-5xl font-semibold">{{ pastorGreeting.pastors_name }} </h1>
                    <p class="text-lg leading-relaxed mb-auto" v-html="pastorGreeting.desc">
                    </p>

                    <a href="/about#pastoral" class="w-full lg:w-fit">
                        <Button
                            class="w-full font-sans font-semibold text-sm rounded-full shadow-none bg-amber-600 hover:bg-amber-500 text-white uppercase tracking-tight"
                            size="lg">
                            {{ pastorGreeting.pastoral_button_text }}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </Button>
                    </a>
                </div>
                <div class="relative h-full w-full">
                    <img class="h-full w-full object-cover rounded-xl" :src="`/storage/${pastorGreeting.pastor_img}`"
                        alt="">
                    <img class="absolute bottom-4 right-4 h-32" :src="`/storage/${pastorGreeting.pastor_signature}`" />
                </div>
            </div>
        </section>

    </DefaultLayout>
</template>
