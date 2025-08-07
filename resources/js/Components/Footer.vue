<template>
    <footer class="bg-black text-white px-6 py-12">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-16">
            <!-- Kiri: Informasi dan CTA -->
            <div class="md:col-span-1">
                <h2 class="text-2xl font-semibold mb-4">
                    How can we help you?
                </h2>
                <div class="text-sm mb-6 leading-relaxed" v-html="churchAddress">
                </div>
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
                    <li v-for="social in socialMediaLinks" :key="social.platform">
                        <a :href="social.url" target="_blank" rel="noopener noreferrer" class="hover:text-white transition">{{ social.platform }}</a>
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
                <a v-for="social in socialMediaLinks" :key="social.platform + '-icon'" :href="social.url" target="_blank" rel="noopener noreferrer" :class="getSocialIconClass(social.platform)">
                    <i :class="getSocialIcon(social.platform)"></i>
                </a>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { onMounted, computed, ref } from "vue";
import { gsap } from "gsap";
import Button from "@/components/ui/button/Button.vue";
import { useNavigationStore } from '@/Stores/navigationStore'; // Import the store
import { usePage } from '@inertiajs/vue3'; // Import usePage

const navigationStore = useNavigationStore(); // Use the store
const page = usePage(); // Initialize usePage

// Access general settings from Inertia shared props
const generalSettings = computed(() => page.props.generalSettings || {});

const churchAddress = computed(() => generalSettings.value.church_address || 'Kompleks Pasar Modern Paramount, <br>Jl. Boulevard Raya Gading Serpong Blok L39,');
const socialMediaLinks = computed(() => generalSettings.value.social_media || []);

const menuItems = computed(() => navigationStore.getMenuItems); // Get menu items from the store

const getSocialIcon = (platform) => {
    switch (platform.toLowerCase()) {
        case 'facebook':
            return 'fab fa-facebook';
        case 'instagram':
            return 'fab fa-instagram';
        case 'youtube':
            return 'fab fa-youtube';
        case 'twitter':
            return 'fab fa-twitter';
        case 'linkedin':
            return 'fab fa-linkedin';
        // Add more cases for other platforms as needed
        default:
            return 'fas fa-link'; // Generic link icon
    }
};

const getSocialIconClass = (platform) => {
    switch (platform.toLowerCase()) {
        case 'facebook':
            return 'hover:text-blue-500';
        case 'instagram':
            return 'hover:text-pink-400';
        case 'youtube':
            return 'hover:text-red-500';
        case 'twitter':
            return 'hover:text-blue-400';
        case 'linkedin':
            return 'hover:text-blue-700';
        default:
            return '';
    }
};

onMounted(() => {
    gsap.from("footer", {
        opacity: 0,
        y: 50,
        duration: 1,
        ease: "power3.out",
    });
});
</script>
