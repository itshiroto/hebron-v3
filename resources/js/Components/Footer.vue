<template>
    <footer class="bg-black text-white px-6 py-12">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-16">
            <!-- Kiri: Informasi dan CTA -->
            <div class="md:col-span-1">
                <h2 class="text-2xl font-semibold mb-4">
                    How can we help you?
                </h2>
                <p class="text-sm mb-6 leading-relaxed">
                    Kompleks Pasar Modern Paramount, <br>
                    Jl. Boulevard Raya Gading Serpong Blok L39,
                </p>
                <a href="/contact">
                    <Button variant="outline">
                        CONTACT US
                    </Button>
                </a>
            </div>

            <!-- Tiga Kolom Navigasi -->
            <div class="space-y-2">
                <h3 class="font-semibold mb-4">GPDI Hebron</h3>
                <ul class="text-sm text-gray-400 space-y-2">
                    <li v-for="item in menuItems" :key="item.name">
                        <a :href="item.href" class="hover:text-white transition">{{ item.name }}</a>
                    </li>
                </ul>
            </div>
            <div class="space-y-2">
                <h3 class="font-semibold mb-4">Connect With Us</h3>
                <ul class="text-sm text-gray-400 space-y-2">
                    <li>
                        <a href="#" class="hover:text-white transition">Facebook</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-white transition">Instagram</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-white transition">YouTube</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-800 my-10"></div>

        <!-- Footer Bottom -->
        <div
            class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-sm text-gray-400 gap-6 px-1">
            <div class="text-center md:text-left">
                <p class="mt-2 text-xs text-gray-500">
                    ©2025 GPdI Hebron Production Team w/ <a href="https://rivo.my.id">34&Co Creative Labs</a>. All
                    Rights Reserved
                </p>
            </div>

            <!-- Icon Sosial -->
            <div class="flex justify-center space-x-4 text-white text-lg">
                <a href="#" class="hover:text-red-500"><i class="fab fa-youtube"></i></a>
                <a href="#" class="hover:text-pink-400"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-blue-500"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { onMounted, computed } from "vue";
import { gsap } from "gsap";
import Button from "@/components/ui/button/Button.vue";
import { useNavigationStore } from '@/Stores/navigationStore'; // Import the store

const navigationStore = useNavigationStore(); // Use the store

const menuItems = computed(() => navigationStore.getMenuItems); // Get menu items from the store

onMounted(() => {
    gsap.from("footer", {
        opacity: 0,
        y: 50,
        duration: 1,
        ease: "power3.out",
    });
});
</script>
