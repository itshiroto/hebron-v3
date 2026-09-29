<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import { useSiteSettings } from '@/composables/useSiteSettings';

defineProps<{
    hero: { title?: string; subtitle?: string | null; image?: string | null };
    contactList: { title: string; desc: string; link?: string }[];
}>();

const { address, mapsUrl, services } = useSiteSettings();
const MAP_EMBED =
    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9409.156729619113!2d106.62013527573178!3d-6.250669861203338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fc76ad63a58b%3A0xb11a5c84cfc0f8db!2sGPdI%20Hebron%20Gading%20Serpong!5e1!3m2!1sen!2sid!4v1754075220198!5m2!1sen!2sid';
</script>

<template>
    <Head title="Kontak" />
    <DefaultLayout>
        <PageHero :image="hero?.image" eyebrow="Kontak & Lokasi" :title="hero?.title || 'Kami senang bertemu denganmu'" :subtitle="hero?.subtitle" />

        <section class="container-site section-y grid gap-11 lg:grid-cols-[5fr_7fr] lg:gap-24">
            <div class="flex flex-col gap-11 lg:gap-14">
                <div v-if="contactList.length" class="flex flex-col gap-3.5 lg:gap-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Hubungi Kami</p>
                    <div class="flex flex-col border-t border-line">
                        <div v-for="item in contactList" :key="item.title + item.desc" class="flex min-h-[56px] flex-col justify-center gap-1 border-b border-line py-3 lg:grid lg:grid-cols-[140px_1fr] lg:items-baseline lg:gap-6 lg:py-5">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-muted lg:text-[13px]">{{ item.title }}</p>
                            <a v-if="item.link" :href="item.link" target="_blank" rel="noopener noreferrer" class="text-base font-medium text-heading hover:text-brand-teal lg:text-[17px]" v-html="item.desc"></a>
                            <div v-else class="text-base font-medium text-heading lg:text-[17px]" v-html="item.desc"></div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-3.5 lg:gap-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Alamat</p>
                    <div class="rich-text font-serif text-2xl leading-tight text-heading lg:text-[30px]" v-html="address"></div>
                    <BaseButton :href="mapsUrl" external variant="link" class="hidden self-start lg:inline-flex">Buka di Google Maps ↗</BaseButton>
                </div>
                <div v-if="services.length" class="flex flex-col gap-3.5 lg:gap-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Ibadah Raya</p>
                    <div v-for="service in services" :key="service.title" class="flex justify-between gap-4 text-[15px] lg:text-base">
                        <span>{{ service.title }}<template v-if="service.livestream"> · Live Streaming</template></span>
                        <span class="font-semibold">{{ service.time }} WIB</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-3.5">
                <iframe :src="MAP_EMBED" title="Peta lokasi GPdI Hebron Gading Serpong" class="h-[260px] w-full border-0 lg:h-full lg:min-h-[720px]" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <BaseButton :href="mapsUrl" external class="lg:hidden">Buka di Google Maps</BaseButton>
            </div>
        </section>
    </DefaultLayout>
</template>
