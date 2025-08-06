<script setup lang="ts">
import { Button } from '@/components/ui/button';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Vue3Marquee } from "vue3-marquee";

import type { HomePageProps } from "@/types/home-page";
defineProps<HomePageProps>();

</script>

<template>
    <DefaultLayout>
        <section id="hero" class="relative h-screen text-white pt-20 bg-cover bg-center bg-black mb-24"
            :style="{ backgroundImage: `url(/storage/${hero.image})` }">
            <div class="relative h-full max-w-screen-xl mx-auto px-8 flex flex-col justify-center gap-8 z-10">
                <h1 class="text-5xl max-w-[50%]">{{ hero.title }}</h1>
                <div class="flex gap-4 font-sans">
                    <a :href="hero.button1.link">
                        <Button variant="secondary" class="bg-white text-black">
                            {{ hero.button1.text }}
                        </Button>
                    </a>
                    <a :href="hero.button2.link">
                        <Button variant="secondary" class="hover:bg-white/20 border border-white text-white">
                            {{ hero.button2.text }}
                        </Button>
                    </a>
                </div>
            </div>
            <div class="absolute inset-0 bg-black z-0 opacity-50 mix-blend-multiply"></div>
        </section>

        <section id="visi-misi" class="max-w-screen-xl mx-auto px-8 mb-32">
            <div class="mb-8">
                <p class="text-3xl md:text-4xl mb-24 font-medium" v-html="visionMission.title"></p>

                <div class="flex flex-col md:flex-row gap-12 mb-24">
                    <div>
                        <p class="mb-4 text-gray-600 font-sans uppercase font-bold">Visi Kami</p>
                        <p class="text-xl leading-normal" v-html="visionMission.visi"></p>
                    </div>
                    <div>
                        <p class="mb-4 text-gray-600 font-sans uppercase font-bold">Misi Kami</p>
                        <p class="text-xl leading-normal" v-html="visionMission.misi"></p>
                    </div>
                </div>

                <a href="/about">
                    <Button
                        class="font-sans font-semibold text-sm rounded-full shadow-none bg-blue-600 hover:bg-blue-500 text-white uppercase tracking-tight"
                        size="lg">
                        Selengkapnya Tentang Hebron
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </Button>
                </a>
            </div>

        </section>

        <!-- Carousel -->
        <section id="carousel" class="mb-32">
            <Vue3Marquee class="w-full" :speed="25" pause-on-hover :gradient="false">
                <div class="flex gap-4 px-2 items-center">
                    <img v-for="n in 5" src="https://placehold.co/600x400" class="aspect-video h-64 object-cover"
                        alt="carousel image" />
                </div>
            </Vue3Marquee>
        </section>

        <section id="services" class="max-w-screen-xl mx-auto px-8 mb-32">
            <div class="bg-black min-h-[512px] flex flex-col justify-end text-white p-4 md:p-8 mb-4 rounded-lg">
                <h4 class="font-sans text-sm md:text-lg uppercase font-bold">Lorem Ipsum Dolor Sit Amet</h4>
                <h2 class="text-3xl md:text-4xl">Lorem Ipsum Dolor Sit Amet</h2>
            </div>
            <div class="flex flex-col md:flex-row gap-4 justify-stretch items-stretch font-sans min-h-24">
                <div v-for="_ in 3"
                    class="bg-gray-100 hover:bg-gray-200 transition w-full py-4 px-4 flex flex-col justify-center rounded-lg">
                    <p class="text-xl tracking-tight">Ibadah Raya 1</p>
                    <p class="text-3xl font-semibold">07.30 WIB</p>
                </div>
                <a href="/division"
                    class="bg-blue-200 hover:bg-blue-300 hover:underline transition w-full py-8 md:py-4 px-4 flex flex-col justify-center cursor-pointer rounded-lg">
                    <p class="text-2xl font-semibold tracking-tighter leading-none">Lihat Ibadah Divisi
                        Hebron Lainnya

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
                    <h4 class="font-sans text-sm lg:text-lg uppercase font-bold opacity-75">Sambutan dari Gembala Kami
                    </h4>
                    <h1 class="text-3xl lg:text-5xl font-semibold">Pdt. Raimond Sumaa & Pdt. Yesie Sumaa </h1>
                    <p class="text-lg leading-relaxed mb-auto">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam sed dolorem obcaecati ex
                        quam
                        blanditiis a deleniti distinctio saepe facilis voluptatum placeat fugit enim eos doloremque,
                        voluptas cupiditate quis modi quisquam porro aspernatur repudiandae earum facere! Eaque, vel sit
                        id neque porro praesentium nam cum reiciendis, repellendus eveniet iste possimus quos alias.
                        Corporis, vel accusamus? Nostrum, autem ea perferendis illo asperiores mollitia debitis officia,
                    </p>

                    <a href="/about#pastoral" class="w-full lg:w-fit">
                        <Button
                            class="w-full font-sans font-semibold text-sm rounded-full shadow-none bg-amber-600 hover:bg-amber-500 text-white uppercase tracking-tight"
                            size="lg">
                            Lihat tim pastoral Hebron
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </Button>
                    </a>
                </div>
                <div class="relative h-full w-full">
                    <img class="h-full w-full object-cover rounded-xl" src="https://placehold.co/600x400" alt="">
                    <img class="absolute bottom-4 right-4 w-48"
                        src="https://d9pqsu5mss31g.cloudfront.net/skyporter3/website/settings/55524_20241020_090010.png" />
                </div>
            </div>
        </section>

    </DefaultLayout>
</template>
