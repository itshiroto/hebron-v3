<template>
    <footer class="bg-black text-white px-6 py-12">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">
            <!-- Kiri: Informasi dan CTA -->
            <div class="md:col-span-1">
                <h2 class="text-2xl font-semibold mb-4">
                    How can we help you?
                </h2>
                <p class="text-sm mb-6 leading-relaxed">
                    Kompleks Pasar Modern Paramount, Jl. Boulevard Raya Gading
                    Serpong Blok L39,
                </p>
                <button
                    class="bg-white text-black font-semibold px-5 py-2 rounded-full shadow hover:bg-gray-100 transition w-fit"
                >
                    CONTACT US
                </button>
            </div>

            <!-- Tiga Kolom Navigasi -->
            <div v-for="n in 3" :key="n" class="space-y-2">
                <h3 class="font-semibold mb-4">GPDI Hebron</h3>
                <ul class="text-sm text-gray-400 space-y-2">
                    <li>
                        <a href="#" class="hover:text-white transition"
                            >Lorem Ipsum</a
                        >
                    </li>
                    <li>
                        <a href="#" class="hover:text-white transition"
                            >Lorem Ipsum</a
                        >
                    </li>
                    <li>
                        <a href="#" class="hover:text-white transition"
                            >Lorem Ipsum</a
                        >
                    </li>
                    <li>
                        <a href="#" class="hover:text-white transition"
                            >Lorem Ipsum</a
                        >
                    </li>
                    <li>
                        <a href="#" class="hover:text-white transition"
                            >Lorem Ipsum</a
                        >
                    </li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-800 my-10"></div>

        <!-- Footer Bottom -->
        <div
            class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-sm text-gray-400 gap-6 px-1"
        >
            <div class="text-center md:text-left">
                <p class="font-semibold text-white">GPDI Hebron</p>
                <p>
                    Kompleks Pasar Modern Paramount, Jl. Boulevard Raya Gading
                    Serpong Blok L39, Tangerang Regency, Banten 15810
                </p>
                <p class="mt-2 text-xs text-gray-500">
                    ©2024 GPDI Hebron. All Rights Reserved
                </p>
            </div>

            <!-- Icon Sosial -->
            <div class="flex justify-center space-x-4 text-white text-lg">
                <a href="#" class="hover:text-red-500"
                    ><i class="fab fa-youtube"></i
                ></a>
                <a href="#" class="hover:text-pink-400"
                    ><i class="fab fa-instagram"></i
                ></a>
                <a href="#" class="hover:text-blue-500"
                    ><i class="fab fa-facebook"></i
                ></a>
            </div>
        </div>
    </footer>
</template>

<script setup>
// Opsional: GSAP animasi
import { onMounted } from "vue";
import { gsap } from "gsap";

onMounted(() => {
    gsap.from("footer", {
        opacity: 0,
        y: 50,
        duration: 1,
        ease: "power3.out",
    });
});
</script>
