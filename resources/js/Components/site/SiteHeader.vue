<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { NAV_ITEMS, isActive } from '@/lib/site';
import { useSiteSettings } from '@/composables/useSiteSettings';
import MobileMenu from './MobileMenu.vue';

const page = usePage();
const { churchName, logo } = useSiteSettings();
const scrolled = ref(false);
const menuOpen = ref(false);
const menuButton = ref<HTMLButtonElement | null>(null);
const left = NAV_ITEMS.slice(0, 3);
const right = NAV_ITEMS.slice(3);

function onScroll() {
    scrolled.value = window.scrollY > 40;
}
onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
});
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));

function closeMenu() {
    menuOpen.value = false;
    menuButton.value?.focus();
}

function linkClass(href: string) {
    return [
        'border-b py-1.5 transition-colors hover:text-brand-mint focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-orange',
        isActive(page.url, href) ? 'border-decor' : 'border-transparent',
    ];
}
</script>

<template>
    <header
        class="fixed inset-x-0 top-0 z-50 text-white transition-colors duration-300"
        :class="scrolled ? 'bg-brand-deep shadow-lg' : 'bg-transparent'"
    >
        <div class="flex h-16 items-center justify-between pl-5 pr-3 lg:grid lg:h-[88px] lg:grid-cols-[1fr_auto_1fr] lg:px-14">
            <nav aria-label="Utama" class="hidden gap-8 text-xs font-medium uppercase tracking-[0.16em] lg:flex">
                <Link
                    v-for="item in left"
                    :key="item.href"
                    :href="item.href"
                    :class="linkClass(item.href)"
                    :aria-current="isActive(page.url, item.href) ? 'page' : undefined"
                    >{{ item.name }}</Link
                >
            </nav>
            <Link href="/" class="flex items-center gap-2.5">
                <img :src="logo" alt="" class="h-7 w-auto lg:h-[30px]" />
                <span class="font-serif text-[19px] lg:text-xl">{{ churchName }}</span>
            </Link>
            <nav aria-label="Sekunder" class="hidden items-center justify-end gap-8 text-xs font-medium uppercase tracking-[0.16em] lg:flex">
                <Link
                    v-for="item in right"
                    :key="item.href"
                    :href="item.href"
                    :class="linkClass(item.href)"
                    :aria-current="isActive(page.url, item.href) ? 'page' : undefined"
                    >{{ item.name }}</Link
                >
                <Link
                    href="/contact"
                    class="rounded-button border border-white/70 px-4 py-2.5 transition-colors hover:bg-white hover:text-heading"
                    >Kunjungi Kami</Link
                >
            </nav>
            <button
                ref="menuButton"
                type="button"
                class="flex h-11 w-11 items-center justify-center lg:hidden"
                aria-label="Buka menu"
                aria-controls="mobile-menu"
                :aria-expanded="menuOpen"
                @click="menuOpen = true"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M4 8h16M4 16h16" /></svg>
            </button>
        </div>
    </header>
    <MobileMenu :open="menuOpen" @close="closeMenu" />
</template>
