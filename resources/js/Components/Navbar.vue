<template>
    <nav ref="navRef" :class="[
        'fixed top-0 inset-x-0 z-50 transition-all duration-300',
        isScrolled ? 'bg-black shadow-lg' : 'bg-blend-multiply bg-gradient-to-b from-black/50 to-black/0 pt-4',
    ]">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-16">
                <!-- Logo -->
                <div ref="logoRef" class="flex items-center space-x-3 cursor-pointer group" @click="goToHome">
                    <img :src="churchLogo" alt="Church Logo"
                        class="size-8 lg:size-10 rounded-full object-contain shadow-lg transition" />
                    <div class="text-white">
                        <h1
                            class="text-sm lg:text-normal font-bold group-hover:text-blue-300 transition tracking-tight">
                            {{ churchName }}
                        </h1>
                        <p class="text-xs text-white font-medium group-hover:text-blue-200 transition">
                            {{ churchSubtitle }}
                        </p>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a v-for="(item, index) in menuItems" :key="item.name" :href="item.href"
                        class="uppercase text-white hover:text-blue-300 text-sm font-medium relative group transition py-2 px-1">
                        {{ item.name }}
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button @click="toggleMenu"
                    class="md:hidden p-2 rounded-md text-white hover:text-blue-300 hover:bg-white/20 transition bg-black/30">
                    <svg v-if="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <transition name="fade">
            <div v-if="isOpen"
                class="md:hidden inset-0 z-40 bg-black/90 backdrop-blur-sm flex flex-col justify-center items-center"
                @click.self="closeMenu">
                <div class="w-full flex flex-col space-y-4 p-6">
                    <a v-for="(item, index) in menuItems" :key="item.name" :href="item.href"
                        class="text-white text-lg font-medium hover:text-blue-300 transition py-2 border-b border-white/10">
                        {{ item.name }}
                    </a>
                </div>
            </div>
        </transition>
    </nav>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { gsap } from "gsap";
import { ScrollToPlugin } from "gsap/ScrollToPlugin";
import { useNavigationStore } from '@/Stores/navigationStore'; // Import the store
import { usePage } from '@inertiajs/vue3'; // Import usePage

gsap.registerPlugin(ScrollToPlugin);

const props = defineProps({
    brand: {
        type: Object,
        default: () => ({
            title: "GPdi Hebron",
            subtitle: "Gading Serpong",
        }),
    },
    scrollOffset: {
        type: Number,
        default: 80,
    },
});

const navigationStore = useNavigationStore(); // Use the store
const page = usePage(); // Initialize usePage

const navRef = ref(null);
const logoRef = ref(null);
const isOpen = ref(false);
const isScrolled = ref(false);

// Access general settings from Inertia shared props
const generalSettings = computed(() => page.props.generalSettings || {});

const churchName = computed(() => generalSettings.value.church_name || 'Hebron Family Church');
const churchSubtitle = ref('Gading Serpong'); // Subtitle is not in general settings, keep default or remove if not needed
const churchLogo = computed(() => generalSettings.value.church_logo ? `/storage/${generalSettings.value.church_logo}` : '/storage/hebron.png');

const menuItems = computed(() => navigationStore.getMenuItems); // Get menu items from the store

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const initScrollListener = () => {
    window.addEventListener("scroll", handleScroll);
};

const removeScrollListener = () => {
    window.removeEventListener("scroll", handleScroll);
};

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

const closeMenu = () => {
    isOpen.value = false;
};


const goToHome = () => {
    window.location.href = "/";
};

onMounted(() => {
    initScrollListener();
    gsap.from(logoRef.value, {
        duration: 1,
        x: -50,
        opacity: 0,
        ease: "power3.out",
    });
});

onBeforeUnmount(() => {
    removeScrollListener();
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
