<script setup lang="ts">
import { nextTick, onMounted } from 'vue';
import SiteHeader from '@/Components/site/SiteHeader.vue';
import SiteFooter from '@/Components/site/SiteFooter.vue';

// Pages render client-side, so the browser's own jump to #anchor happens before the target exists.
onMounted(async () => {
    const id = decodeURIComponent(window.location.hash.slice(1));
    if (!id) return;
    await nextTick();
    document.getElementById(id)?.scrollIntoView();
});
</script>

<template>
    <a
        href="#konten"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:bg-white focus:px-4 focus:py-2 focus:text-heading"
        >Lewati ke konten</a
    >
    <SiteHeader />
    <main id="konten" class="min-h-screen bg-white font-sans text-ink">
        <slot />
    </main>
    <SiteFooter />
</template>
