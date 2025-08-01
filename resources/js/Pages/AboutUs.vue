<template>

    <Head title="About Us" />
    <DefaultLayout>
        <section id="hero" class="h-screen text-white pt-20 bg-cover bg-center"
            :style="{ backgroundImage: `url(/storage/${hero.image})` }">
            <div class="h-full max-w-screen-xl mx-auto py-16 px-8 flex flex-col justify-between">
                <h1 class="text-5xl">{{ hero.title }}</h1>
                <p class="leading-relaxed text-lg max-w-3xl" v-html="hero.subtitle"></p>
            </div>
        </section>

        <section id="visimisi" class="max-w-screen-xl mx-auto my-32 px-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div class="w-full h-[24em] lg:h-full bg-gray-800">
                <img class="h-full w-full object-cover" :src="`/storage/${visionMission.image}`" />
            </div>
            <div class="flex flex-col gap-12">
                <div>
                    <h3 class="uppercase font-sans font-bold text-gray-600 mb-4">{{ visionMission.visi.title }}</h3>
                    <p class="text-xl leading-relaxed" v-html="visionMission.visi.desc"></p>
                </div>
                <div>
                    <h3 class="uppercase font-sans font-bold text-gray-600 mb-4">{{ visionMission.misi.title }}</h3>
                    <p class="text-xl leading-relaxed" v-html="visionMission.misi.desc"></p>
                </div>
            </div>
        </section>

        <section id="values" class="max-w-screen-xl mx-auto my-32 px-8">
            <h1 class="text-4xl mb-16 text-center">Our Values</h1>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 justify-center items-center">
                <div v-for="val in values" :key="val.title" class="flex flex-col items-center border border-gray-200 rounded-lg p-8">
                    <div class="rounded-full aspect-square h-36 mb-8 bg-gray-800 flex items-center justify-center text-white text-5xl">
                        <i :class="val.icon"></i>
                    </div>
                    <h4 class="text-2xl mb-2">{{ val.title }}</h4>
                    <p class="text-center" v-html="val.desc"></p>
                </div>
            </div>
        </section>

        <section id="history" class="my-32 bg-gray-100">
            <div class="max-w-screen-xl mx-auto py-16 px-8">
                <h1 class="text-4xl mb-16 text-center">The Story behind Our Journey</h1>
                <div id="stories-list"
                    class="relative pl-10 md:pl-0 py-16 flex flex-col items-center justify-center gap-32">

                    <div id="stories-progressBar"
                        class="origin-top absolute top-0 left-1.5 md:left-auto h-full w-1 md:w-0.5 bg-gray-800 rounded-full z-10" />
                    <div id="stories-progressBarBg"
                        class="absolute top-0 left-1.5 md:left-auto h-full w-1 md:w-0.5 bg-gray-200 rounded-full" />

                    <div v-for="(story, index) in history" :key="index"
                        class="stories-item relative grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-48 z-10"
                        :class="{ 'md:grid-flow-col-dense': index % 2 !== 0 }">

                        <div
                            class="story-dot absolute -left-10 md:left-1/2 md:-translate-x-1/2 top-0 rounded-full aspect-square w-4 mt-2 bg-gray-600">
                        </div>

                        <div class="flex flex-col"
                            :class="{ 'md:order-2 items-start': index % 2 !== 0, 'md:items-end md:text-right': index % 2 === 0 }">
                            <h3 class="uppercase font-sans text-2xl font-bold tracking-tight mb-4">
                                {{ story.title }}
                            </h3>
                            <div v-if="story.image" class="w-full max-w-lg aspect-video bg-gray-900">
                                <img :src="`/storage/${story.image}`" alt="" class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <div class="flex flex-col"
                            :class="{ 'md:order-1': index % 2 !== 0, 'md:items-end md:text-right': index % 2 !== 0 }">
                            <p class="font-sans text-sm font-bold text-gray-600 uppercase mt-2 mb-4">{{ story.date }}
                            </p>
                            <p class="text-lg leading-relaxed" v-html="story.desc"></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="pastoral" class="max-w-screen-xl mx-auto my-32 px-8">
            <h1 class="text-4xl mb-16 text-center">Pastoral Team</h1>
            <div class="flex flex-wrap gap-8 justify-center items-center">
                <div v-for="pastor in pastoral" class="max-w-sm w-full flex flex-col gap-4">
                    <div class="aspect-square h-full w-full bg-gray-800 mb-4 rounded-lg overflow-clip">
                        <img v-if="pastor.image" class="w-full h-full object-cover" :src="pastor.image" alt="" />
                    </div>
                    <h4 class="uppercase text-sm text-gray-600 font-bold font-sans leading-none">{{ pastor.position }}
                    </h4>
                    <h2 class="text-3xl leading-none">{{ pastor.name }}</h2>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Head } from "@inertiajs/vue3";
import { onMounted, onUnmounted } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
gsap.registerPlugin(ScrollTrigger);

import type { AboutPageProps, ValueItem, HistoryItem } from '@/types/about-page';

const props = defineProps<AboutPageProps>();

// The following refs are no longer needed as data comes from props
// type Values = {
//     title: string,
//     desc: string,
//     icon?: string
// }

// type Stories = {
//     title: string,
//     desc: string,
//     date: string,
//     image?: string
// }

type Pastoral = {
    name: string,
    position: string,
    image?: string
}

// const homeValues = ref<Values[]>([
//     {
//         title: "Holiness",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo."
//     },
//     {
//         title: "Obedience",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo."
//     },
//     {
//         title: "Ministry",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo."
//     },
//     {
//         title: "Evangelism",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo."
//     },
// ])

// const stories = ref<Stories[]>([
//     {
//         title: "Awal Mula GPdI Hebron",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo.",
//         date: "2020",
//         image: "https://placehold.co/400"
//     },
//     {
//         title: "Test GPdI Hebron",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo.",
//         date: "2020",
//         image: "https://placehold.co/400"
//     },
//     {
//         title: "AAA GPdI Hebron",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo.",
//         date: "2020",
//     },
//     {
//         title: "BBB GPdI Hebron",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo.",
//         date: "2020",
//         image: "https://placehold.co/400"
//     },
//     {
//         title: "CCC Mula GPdI Hebron",
//         desc: "Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet varius purus. Praesent dui nulla, eleifend at volutpat ut, porta et leo.",
//         date: "2020",
//         image: "https://placehold.co/400"
//     },
// ])

const pastoral = ref<Pastoral[]>([
    {
        name: "Rivo Juicer Wowor",
        position: "Senior Associate Pastor",
        image: "https://placehold.co/512",
    },
    {
        name: "Rivo Juicer Wowor",
        position: "Senior Associate Pastor",
        image: "https://placehold.co/512",
    },
    {
        name: "Rivo Juicer Wowor",
        position: "Senior Associate Pastor",
    },
    {
        name: "Rivo Juicer Wowor",
        position: "Senior Associate Pastor",
        image: "https://placehold.co/512",
    },
])

let ctx: gsap.Context;

onMounted(() => {
    let history_item = gsap.utils.toArray<HTMLElement>(".stories-item")
    ctx = gsap.context(() => {
        gsap.from(
            "#stories-progressBar",
            {
                scaleY: 0,
                ease: "none",
                scrollTrigger: {
                    trigger: "#stories-list",
                    start: "top center",
                    end: "bottom center",
                    scrub: true,
                }
            }
        )

        history_item.forEach(item => {
            gsap.fromTo(
                item,
                {
                    opacity: "30%"
                },
                {
                    opacity: "100%",
                    duration: 0.5,
                    scrollTrigger: {
                        trigger: item,
                        start: "top center",
                        end: "bottom center",
                        toggleActions: "play none none reverse",
                        onEnter: () => {
                            item.children[0].classList.add("bg-blue-800");
                            item.children[0].classList.remove("bg-gray-600");
                        },
                        onLeaveBack: () => {
                            item.children[0].classList.remove("bg-blue-800");
                            item.children[0].classList.add("bg-gray-600");
                        }
                    },
                }
            )
        })
    })
})

onUnmounted(() => {
    ctx.revert();
})
</script>
