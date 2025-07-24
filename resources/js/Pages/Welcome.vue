<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import ServiceCard from "@/Components/ServiceCard.vue";

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}

import { Vue3Marquee } from "vue3-marquee";

// Jika ingin loop tanpa jeda, gandakan array image
const images = [
    "/storage/images/image7.png",
    "/storage/images/image8.png",
    "/storage/images/image9.png",
];
const loopImages = [...images, ...images];

import { Carousel, Slide } from "vue3-carousel";

const imagePaths = [
    "/storage/images/image7.png",
    "/storage/images/image8.png",
    "/storage/images/image9.png",
];
</script>

<template>
    <Navbar />
    <section id="section-1">
        <div>
            <div class="relative">
                <img
                    src="/storage/images/HeroSection.png"
                    alt="Hero Section"
                    class="w-full h-auto"
                />
                <div
                    class="absolute inset-0"
                    style="
                        background: linear-gradient(
                            to top,
                            black 0%,
                            transparent 75%
                        );
                    "
                ></div>
                <!-- Content overlay -->
                <div
                    class="absolute inset-0 flex items-center justify-content-start"
                >
                    <div
                        class="flex flex-col justify-content-start gap-6 px-6 md:px-12 lg:px-40"
                    >
                        <!-- Main heading -->
                        <p
                            class="hero-title text-white text-xl md:text-5xl w-3/4 font-bold text-left px-4 py-2 bg-black bg-opacity-50 rounded max-w-4xl"
                            style="
                                font-family: 'Georgia', 'Times New Roman', serif;
                            "
                        >
                            There's a place for you to call Home.
                        </p>

                        <!-- Buttons -->
                        <div class="hero-buttons flex flex-row gap-4">
                            <Link
                                :href="route('login')"
                                class="group inline-flex items-center px-6 py-3 bg-white text-black font-semibold text-sm tracking-wider uppercase rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                            >
                                <span>IKUT BERIBADAH</span>
                            </Link>

                            <Link
                                :href="route('register')"
                                class="group inline-flex items-center px-6 py-3 bg-transparent border-2 border-white text-white font-semibold text-sm tracking-wider uppercase rounded-full hover:bg-white hover:text-black transition-all duration-300 transform hover:scale-105"
                            >
                                <span>PERMINTAAN DOA</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="section-2">
        <div class="px-6 md:px-12 lg:px-40 py-24">
            <p class="text-xl md:text-4xl font-bold mb-6">
                Kami adalah Lorem ipsum dolor sit amet, consectetur adipiscing
                elit. Pellentesque sit amet varius purus. Praesent dui nulla,
                eleifend at volutpat ut, porta et leo. 
            </p>

            <div class="flex flex-row gap-6 mt-20">
                <div>
                    <p class="text-xl text-[#525252] font-bold">Visi Kami</p>
                    <p class="text-2xl mt-3">
                        Kami adalah Lorem ipsum dolor sit amet, consectetur
                        adipiscing elit. Pellentesque sit amet varius purus.
                        Praesent dui nulla, eleifend at volutpat ut, porta et
                        leo. 
                    </p>
                </div>
                <div>
                    <p class="text-xl text-[#525252] font-bold">MisiKami</p>
                    <p class="text-2xl mt-3">
                        Kami adalah Lorem ipsum dolor sit amet, consectetur
                        adipiscing elit. Pellentesque sit amet varius purus.
                        Praesent dui nulla, eleifend at volutpat ut, porta et
                        leo. 
                    </p>
                </div>
            </div>

            <button
                class="flex items-center justify-center gap-3 px-6 py-3 bg-[#19426C] text-white text-sm sm:text-base font-bold uppercase rounded-full hover:bg-[#153654] transition-all duration-200 mt-20"
            >
                Selengkapnya Tentang Hebron
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </button>
        </div>

        <!-- Carousel -->

        <div class="py-6">
            <Vue3Marquee
                class="w-full"
                :speed="50"
                pause-on-hover
                :gradient="false"
            >
                <div class="flex gap-4 px-2 items-center">
                    <img
                        v-for="(img, index) in loopImages"
                        :key="index"
                        :src="img"
                        class="w-64 md:w-80 h-40 md:h-56 rounded-lg object-cover"
                    />
                </div>
            </Vue3Marquee>
        </div>
    </section>

    <section id="section-3">
        <div class="px-6 md:px-12 lg:px-40 py-24">
            <p class="text-xl font-bold text-[#525252]">FOR HIS RENOWN</p>
            <p class="text-6xl w-1/2 font-medium mt-3">
                Serving Through Hebron Ministries
            </p>

            <div class="grid grid-cols-3 grid-rows-3 gap-4 mt-24">
                <div class="col-span-3">
                    <ServiceCard
                        title="Ibadah Raya Umum"
                        image="/storage/images/image8.png"
                        desc="Grow together by digging into the biblical principles shared in a recent sermon. Grow together by digging into the biblical principles shared in a recent sermon."
                    />
                </div>
                <div class="row-start-2">
                    <ServiceCard
                        title="/storage/images/hebron-small.png"
                        :isLogo="true"
                        image="/storage/images/image7.png"
                    />
                </div>
                <div class="row-start-2">
                    <ServiceCard
                        title="Seeds of Truth"
                        image="/storage/images/image8.png"
                    />
                </div>
                <div class="row-start-2">
                    <ServiceCard
                        title="Descendants of Truth"
                        image="/storage/images/image8.png"
                    />
                </div>
                <div class="col-span-3 row-start-3">
                    <button
                        class="bg-black text-white w-full font-semibold px-6 py-3 rounded-md hover:bg-gray-800 transition"
                    >
                        PELAYANAN KAMI LAINNYA →
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section id="section-4">
        <div class="w-full max-w-screen-xl mx-auto py-10">
            <Carousel
                :itemsToShow="1"
                :wrapAround="true"
                :autoplay="3000"
                :pauseAutoplayOnHover="false"
                class="w-full h-64 rounded-lg overflow-hidden"
            >
                <Slide v-for="(img, index) in imagePaths" :key="index">
                    <img
                        :src="img"
                        class="w-full h-64 object-cover"
                        alt="carousel image"
                    />
                </Slide>
            </Carousel>

            <div
                class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 py-24"
            >
                <!-- Teks -->
                <div>
                    <h2
                        class="text-2xl md:text-4xl font-medium text-gray-800 leading-snug"
                    >
                        See Upcoming Events at<br />
                        <span>GPdI Hebron</span>
                    </h2>
                </div>

                <!-- Tombol -->
                <div>
                    <button
                        class="bg-[#183B6A] text-white text-xs font-semibold px-6 py-2 rounded-full flex items-center gap-2 hover:bg-[#102848] transition"
                    >
                        BACA SELENGKAPNYA
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section id="section-5">
        <div>
            <div class="relative">
                <img
                    src="/storage/images/image7.png"
                    alt="Hero Section"
                    class="w-full h-auto"
                />
                <div
                    class="absolute inset-0"
                    style="
                        background: linear-gradient(
                            to right,
                            black 0%,
                            transparent 50%
                        );
                    "
                ></div>
                <!-- Content overlay -->
                <div
                    class="absolute inset-0 flex items-center justify-content-start"
                >
                    <div
                        class="flex flex-col justify-content-start gap-6 px-6 md:px-12 lg:px-40"
                    >
                        <!-- Main heading -->
                        <p
                            class="hero-title text-white text-xl md:text-5xl w-3/4 font-bold text-left px-4 py-2 bg-black bg-opacity-50 rounded max-w-4xl"
                            style="
                                font-family: 'Georgia', 'Times New Roman', serif;
                            "
                        >
                            There's a place for you to call Home.
                        </p>

                        <!-- Buttons -->
                        <div class="hero-buttons flex flex-row gap-4">
                            <Link
                                :href="route('login')"
                                class="group inline-flex items-center px-6 py-3 bg-white text-black font-semibold text-sm tracking-wider uppercase rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                            >
                                <span>IKUT BERIBADAH</span>
                            </Link>

                            <Link
                                :href="route('register')"
                                class="group inline-flex items-center px-6 py-3 bg-transparent border-2 border-white text-white font-semibold text-sm tracking-wider uppercase rounded-full hover:bg-white hover:text-black transition-all duration-300 transform hover:scale-105"
                            >
                                <span>PERMINTAAN DOA</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <Footer />
</template>
