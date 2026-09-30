<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import { useSiteSettings } from '@/composables/useSiteSettings';
import { vReveal } from '@/lib/reveal';

// Empty CMS sections arrive as [] from PHP, so every field is read optionally.
const props = defineProps<{
    hero: { title?: string; subtitle?: string | null; image?: string | null };
    contactList: { title: string; desc: string; link?: string }[];
    officeHours: { hours?: string | null };
    requests: { title?: string; whatsapp?: string | null; items?: { title: string; desc?: string | null; message?: string | null }[] };
}>();

const { address, mapsUrl } = useSiteSettings();
const requestItems = computed(() => props.requests.items ?? []);
const whatsapp = computed(() => props.requests.whatsapp?.replace(/\D/g, '') || null);
const whatsappLink = (message?: string | null) =>
    whatsapp.value ? `https://wa.me/${whatsapp.value}${message ? `?text=${encodeURIComponent(message)}` : ''}` : null;
</script>

<template>
    <Head :title="(!Array.isArray($page.props.meta) && $page.props.meta?.title) || 'Kontak'" />
    <DefaultLayout>
        <PageHero :image="hero.image" eyebrow="Kontak" :title="hero.title || 'Hubungi kami'" :subtitle="hero.subtitle" />

        <section class="container-site section-y grid gap-14 lg:grid-cols-[5fr_7fr] lg:gap-24">
            <div class="flex flex-col gap-11 lg:gap-14">
                <div v-if="contactList.length || officeHours.hours" v-reveal class="flex flex-col gap-3.5 lg:gap-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Hubungi Kami</p>
                    <div class="flex flex-col border-t border-line">
                        <div v-for="item in contactList" :key="item.title + item.desc" class="flex min-h-[56px] flex-col justify-center gap-1 border-b border-line py-3 lg:grid lg:grid-cols-[140px_1fr] lg:items-baseline lg:gap-6 lg:py-5">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-muted lg:text-[13px]">{{ item.title }}</p>
                            <a v-if="item.link" :href="item.link" target="_blank" rel="noopener noreferrer" class="text-base font-medium text-heading hover:text-brand-teal lg:text-[17px]" v-html="item.desc"></a>
                            <div v-else class="text-base font-medium text-heading lg:text-[17px]" v-html="item.desc"></div>
                        </div>
                        <div v-if="officeHours.hours" class="flex min-h-[56px] flex-col justify-center gap-1 border-b border-line py-3 lg:grid lg:grid-cols-[140px_1fr] lg:items-baseline lg:gap-6 lg:py-5">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-muted lg:text-[13px]">Jam Kantor</p>
                            <p class="text-base font-medium text-heading lg:text-[17px]">{{ officeHours.hours }}</p>
                        </div>
                    </div>
                </div>
                <div v-reveal="150" class="flex flex-col gap-3.5 lg:gap-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Alamat</p>
                    <div class="rich-text font-serif text-2xl leading-tight text-heading lg:text-[30px]" v-html="address"></div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:gap-8">
                        <BaseButton :href="mapsUrl" external variant="link" class="self-start">Buka di Google Maps ↗</BaseButton>
                        <BaseButton href="/visit" variant="link" class="self-start">Mau datang ibadah? Lihat panduan kunjungan →</BaseButton>
                    </div>
                </div>
            </div>

            <div v-if="requestItems.length" class="flex flex-col gap-3.5 lg:gap-5">
                <p v-reveal class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">{{ requests.title || 'Kami bisa bantu' }}</p>
                <div class="grid gap-px border border-line bg-line sm:grid-cols-2">
                    <component
                        :is="whatsappLink(item.message) ? 'a' : 'div'"
                        v-for="(item, index) in requestItems"
                        :key="item.title"
                        v-reveal="index * 80"
                        :href="whatsappLink(item.message) ?? undefined"
                        :target="whatsappLink(item.message) ? '_blank' : undefined"
                        :rel="whatsappLink(item.message) ? 'noopener noreferrer' : undefined"
                        class="group flex flex-col gap-2 bg-white p-6 transition-colors hover:bg-mist focus-visible:outline-solid focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand-teal lg:p-8"
                    >
                        <span class="font-serif text-[26px] leading-tight text-heading lg:text-[30px]">{{ item.title }}</span>
                        <span v-if="item.desc" class="text-[15px] leading-relaxed text-ink-muted">{{ item.desc }}</span>
                        <span v-if="whatsappLink(item.message)" class="mt-2 text-sm font-semibold text-brand-teal group-hover:text-heading">Chat via WhatsApp →</span>
                    </component>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
