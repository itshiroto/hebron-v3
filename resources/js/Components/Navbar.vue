<template>
    <nav
        ref="navRef"
        :class="[
            'fixed top-0 left-0 right-0 z-50 transition-all duration-300 backdrop-blur-md',
            isScrolled ? 'bg-black/90 shadow-lg' : 'bg-black/20',
        ]"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-20">
                <!-- Logo -->
                <div
                    ref="logoRef"
                    class="flex items-center space-x-3 cursor-pointer group"
                    @click="goToHome"
                >
                    <img
                        src="/storage/images/hebron-small.png"
                        alt="GPdi Hebron Logo"
                        class="w-10 h-10 lg:w-12 lg:h-12 rounded-full object-contain shadow-lg group-hover:shadow-blue-500/50 transition"
                    />
                    <div class="text-white">
                        <h1
                            class="font-bold text-lg lg:text-xl group-hover:text-blue-300 transition"
                        >
                            GPdi Hebron
                        </h1>
                        <p
                            class="text-xs lg:text-sm text-gray-200 group-hover:text-blue-200 transition"
                        >
                            Gading Serpong
                        </p>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a
                        v-for="(item, index) in menuItems"
                        :key="item.name"
                        :href="item.href"
                        class="text-white hover:text-blue-300 text-sm font-medium relative group transition py-2 px-1"
                        @click="handleMenuClick(item, $event)"
                    >
                        {{ item.name }}
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"
                        ></span>
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button
                    @click="toggleMenu"
                    class="md:hidden p-2 rounded-md text-white hover:text-blue-300 hover:bg-white/20 transition bg-black/30"
                >
                    <svg
                        v-if="isOpen"
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <transition name="fade">
            <div
                v-if="isOpen"
                class="md:hidden fixed inset-0 z-40 bg-black/90 backdrop-blur-sm flex items-start pt-20 px-6"
                @click.self="closeMenu"
            >
                <div class="w-full flex flex-col space-y-4">
                    <a
                        v-for="(item, index) in menuItems"
                        :key="item.name"
                        :href="item.href"
                        @click="handleMobileMenuClick(item, $event)"
                        class="text-white text-lg font-medium hover:text-blue-300 transition py-2 border-b border-white/10"
                    >
                        {{ item.name }}
                    </a>
                </div>
            </div>
        </transition>
    </nav>
</template>

<script>
import { gsap } from "gsap";
import { ScrollToPlugin } from "gsap/ScrollToPlugin";

gsap.registerPlugin(ScrollToPlugin);

export default {
    name: "ResponsiveNavbar",
    props: {
        brand: {
            type: Object,
            default: () => ({
                title: "GPdi Hebron",
                subtitle: "Gading Serpong",
            }),
        },
        navigation: {
            type: Array,
            default: () => [
                { name: "ABOUT US", href: "#about" },
                { name: "DIVISIONS", href: "#divisions" },
                { name: "MINISTRIES", href: "#ministries" },
                { name: "DEVOTIONS", href: "#devotions" },
                { name: "AGENDA", href: "#agenda" },
                { name: "CONTACT US", href: "#contact" },
            ],
        },
        scrollOffset: {
            type: Number,
            default: 80,
        },
    },
    data() {
        return {
            isOpen: false,
            isScrolled: false,
        };
    },
    computed: {
        menuItems() {
            return this.navigation;
        },
    },
    mounted() {
        this.initScrollListener();
        gsap.from(this.$refs.logoRef, {
            duration: 1,
            x: -50,
            opacity: 0,
            ease: "power3.out",
        });
    },
    beforeUnmount() {
        this.removeScrollListener();
    },
    methods: {
        initScrollListener() {
            this.handleScroll = () => {
                this.isScrolled = window.scrollY > 50;
            };
            window.addEventListener("scroll", this.handleScroll);
        },
        removeScrollListener() {
            window.removeEventListener("scroll", this.handleScroll);
        },
        toggleMenu() {
            this.isOpen = !this.isOpen;
        },
        closeMenu() {
            this.isOpen = false;
        },
        handleMenuClick(item, event) {
            event.preventDefault();
            this.scrollToSection(item.href);
        },
        handleMobileMenuClick(item, event) {
            event.preventDefault();
            this.closeMenu();
            setTimeout(() => this.scrollToSection(item.href), 300);
        },
        scrollToSection(href) {
            const target = document.querySelector(href);
            if (target) {
                gsap.to(window, {
                    duration: 1,
                    scrollTo: { y: target, offsetY: this.scrollOffset },
                    ease: "power2.inOut",
                });
            }
        },
        goToHome() {
            window.location.href = "/";
        },
    },
};
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
