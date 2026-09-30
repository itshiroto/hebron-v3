<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MOTTO, NAV_ITEMS } from '@/lib/site';
import { useSiteSettings } from '@/composables/useSiteSettings';

const { churchName, logo, address, socials, services } = useSiteSettings();
const year = new Date().getFullYear();
</script>

<template>
    <footer class="bg-brand-deeper text-white [view-transition-name:site-footer]">
        <div class="container-site flex flex-col gap-10 pb-8 pt-16 lg:gap-[72px] lg:pb-12 lg:pt-24">
            <div class="grid gap-10 lg:grid-cols-[2fr_1fr_1fr_1.4fr] lg:gap-16">
                <div class="flex flex-col gap-5">
                    <div class="flex items-center gap-3">
                        <img :src="logo" alt="" class="h-9 w-auto lg:h-11" />
                        <span class="font-serif text-2xl lg:text-[28px]">{{ churchName }}</span>
                    </div>
                    <p class="max-w-[360px] font-serif text-2xl italic leading-tight text-white/90 lg:text-[28px]">{{ MOTTO }}.</p>
                </div>
                <div class="grid grid-cols-2 gap-6 lg:contents">
                    <div class="flex flex-col gap-3 text-[15px]">
                        <p class="mb-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-orange lg:text-xs">Jelajahi</p>
                        <Link v-for="item in NAV_ITEMS" :key="item.href" :href="item.href" class="text-white/80 hover:text-white">{{ item.name }}</Link>
                    </div>
                    <div class="flex flex-col gap-3 text-[15px]">
                        <p class="mb-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-orange lg:text-xs">Terhubung</p>
                        <a
                            v-for="social in socials"
                            :key="social.url"
                            :href="social.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-white/80 hover:text-white"
                            >{{ social.platform }}</a
                        >
                    </div>
                </div>
                <div class="flex flex-col gap-3 text-sm leading-relaxed text-white/80 lg:text-[15px]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-orange lg:text-xs">Kunjungi</p>
                    <div class="rich-text" v-html="address"></div>
                    <p v-if="services.length">Setiap Minggu · {{ services.map((s) => s.time).join(' · ') }} WIB</p>
                    <Link href="/visit" class="font-semibold text-white hover:text-brand-mint">Panduan kunjungan →</Link>
                </div>
            </div>
            <div class="flex flex-col gap-1.5 border-t border-white/15 pt-6 text-xs text-white/60 lg:flex-row lg:justify-between lg:pt-8 lg:text-[13px]">
                <span>© {{ year }} GPdI Hebron Gading Serpong. Hak cipta dilindungi.</span>
                <span>GPdI Hebron Production Team w/ 34 Creative Labs</span>
            </div>
        </div>
    </footer>
</template>
