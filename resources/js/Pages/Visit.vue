<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import SectionHeading from '@/Components/site/SectionHeading.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import DivisionCard from '@/Components/site/DivisionCard.vue';
import { useSiteSettings } from '@/composables/useSiteSettings';
import { storageUrl } from '@/lib/site';
import { vReveal } from '@/lib/reveal';
import type { DivisionItem } from '@/types/site';

// Empty CMS sections arrive as [] from PHP, so every field is read optionally.
const props = defineProps<{
    hero: { title?: string; subtitle?: string | null; image?: string | null };
    expect: { title?: string; items?: { title: string; desc: string }[] };
    arrival: { title?: string; parking?: string | null; steps?: { title: string; desc?: string | null; image?: string | null }[] };
    kids: { title?: string; desc?: string | null };
    kidsDivisions: DivisionItem[];
    otherDivisions: DivisionItem[];
    welcome: { title?: string; desc?: string | null; button_text?: string | null; button_link?: string | null };
    faq: { title?: string; questionsList?: { question: string; answer: string }[] };
}>();

const { address, mapsUrl, livestreamUrl, services } = useSiteSettings();
const steps = computed(() => props.arrival.steps ?? []);
const faqs = computed(() => props.faq.questionsList ?? []);
const welcomeText = computed(() => props.welcome.button_text || 'Kabari kami');
const MAP_EMBED =
    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9409.156729619113!2d106.62013527573178!3d-6.250669861203338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fc76ad63a58b%3A0xb11a5c84cfc0f8db!2sGPdI%20Hebron%20Gading%20Serpong!5e1!3m2!1sen!2sid!4v1754075220198!5m2!1sen!2sid';
</script>

<template>
    <Head :title="(!Array.isArray($page.props.meta) && $page.props.meta?.title) || 'Kunjungi Kami'" />
    <DefaultLayout>
        <PageHero :image="hero.image" eyebrow="Kunjungi Kami" :title="hero.title || 'Minggu ini, ada tempat untukmu'" :subtitle="hero.subtitle">
            <BaseButton :href="mapsUrl" external variant="light">Petunjuk arah</BaseButton>
            <BaseButton v-if="welcome.button_link" :href="welcome.button_link" external variant="link-light">{{ welcomeText }} →</BaseButton>
        </PageHero>

        <!-- Pilih jam ibadah -->
        <section v-if="services.length" class="bg-brand-deep text-white">
            <div class="container-site grid gap-6 py-12 lg:grid-cols-4 lg:gap-12 lg:py-14">
                <h2 v-reveal class="font-serif text-[32px] leading-tight lg:text-4xl">Pilih jam ibadah</h2>
                <div
                    v-for="(service, index) in services"
                    :key="service.title"
                    v-reveal="(index + 1) * 120"
                    class="flex flex-col gap-2 border-t border-white/20 pt-4 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0"
                >
                    <p class="text-[13px] font-semibold uppercase tracking-[0.14em] text-brand-orange lg:text-xs lg:font-normal lg:tracking-[0.18em]">{{ service.title }}</p>
                    <p class="font-serif text-[32px] leading-none lg:text-[40px]">{{ service.time }} <span class="font-sans text-xs text-white/70 lg:text-sm">WIB</span></p>
                    <p v-if="service.livestream" class="flex items-center gap-2 text-xs font-semibold">
                        <span aria-hidden="true" class="h-2 w-2 rounded-full bg-decor"></span>Live Streaming
                    </p>
                    <p v-if="service.note" class="text-sm leading-relaxed text-white/75">{{ service.note }}</p>
                </div>
            </div>
        </section>

        <!-- Ibadah divisi: kids & youth cards, then every other division's schedule.
             The "Apa yang akan kamu alami" section (expect) is hidden for now; its CMS content is kept. -->
        <section v-if="kidsDivisions.length || otherDivisions.length" class="container-site section-y flex flex-col gap-12 lg:gap-16">
            <div class="grid gap-10 lg:grid-cols-[5fr_7fr] lg:items-center lg:gap-24">
                <div v-reveal class="flex flex-col gap-6">
                    <SectionHeading eyebrow="Ibadah Divisi" :title="kids.title || 'Bawa anak & remaja'" />
                    <p v-if="kids.desc" class="text-base leading-relaxed text-ink-muted lg:text-lg">{{ kids.desc }}</p>
                    <BaseButton href="/division" variant="link" class="self-start">Semua divisi →</BaseButton>
                </div>
                <div v-if="kidsDivisions.length" v-reveal="150" class="-mx-5 flex snap-x snap-mandatory scroll-pl-5 gap-3.5 overflow-x-auto px-5 scrollbar-none lg:mx-0 lg:gap-6 lg:px-0">
                    <DivisionCard v-for="division in kidsDivisions" :key="division.id" :division="division" />
                </div>
            </div>
            <div v-if="otherDivisions.length" class="flex flex-col gap-4 lg:gap-5">
                <p v-reveal class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Divisi lainnya</p>
                <div class="grid border-t border-line md:grid-cols-2 md:gap-x-12 lg:gap-x-20">
                    <Link
                        v-for="(division, index) in otherDivisions"
                        :key="division.id"
                        v-reveal="(index % 2) * 100"
                        :href="`/division#${division.slug}`"
                        class="group flex items-center justify-between gap-6 border-b border-line py-5 focus-visible:outline-solid focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand-teal lg:py-6"
                    >
                        <span class="flex flex-col gap-1">
                            <span v-if="division.audience" class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink-muted">{{ division.audience }}</span>
                            <span class="font-serif text-2xl leading-[1.05] text-heading group-hover:underline lg:text-[28px]">{{ division.title }}</span>
                        </span>
                        <span class="max-w-[45%] text-right text-sm font-semibold" :class="division.time ? 'text-brand-teal' : 'text-ink-muted'">{{ division.time || 'Jadwal akan diumumkan' }}</span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Cara sampai -->
        <section class="bg-mist">
            <div class="container-site section-y flex flex-col gap-10 lg:gap-14">
                <div class="grid gap-10 lg:grid-cols-[5fr_7fr] lg:gap-24">
                    <div v-reveal class="flex flex-col gap-6">
                        <SectionHeading eyebrow="Lokasi" :title="arrival.title || 'Cara sampai ke Hebron'" />
                        <div class="rich-text font-serif text-2xl leading-tight text-heading lg:text-[28px]" v-html="address"></div>
                        <div v-if="arrival.parking" class="flex flex-col gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Parkir</p>
                            <p class="text-[15px] leading-relaxed text-ink lg:text-base">{{ arrival.parking }}</p>
                        </div>
                        <BaseButton :href="mapsUrl" external class="self-stretch sm:self-start">Buka di Google Maps ↗</BaseButton>
                    </div>
                    <ol v-if="steps.length" class="flex flex-col border-t border-line-strong">
                        <li
                            v-for="(step, index) in steps"
                            :key="step.title"
                            v-reveal="index * 120"
                            class="grid grid-cols-[40px_1fr] gap-x-4 gap-y-4 border-b border-line-strong py-6 lg:grid-cols-[56px_1fr_200px] lg:gap-x-6 lg:py-8"
                        >
                            <span aria-hidden="true" class="font-serif text-[32px] leading-none text-brand-teal lg:text-[40px]">{{ index + 1 }}</span>
                            <div class="flex flex-col gap-1.5">
                                <p class="text-base font-semibold text-heading lg:text-[19px]">{{ step.title }}</p>
                                <p v-if="step.desc" class="text-[15px] leading-relaxed text-ink-muted">{{ step.desc }}</p>
                            </div>
                            <img
                                v-if="step.image"
                                :src="storageUrl(step.image)!"
                                :alt="step.title"
                                loading="lazy"
                                class="col-span-2 h-[180px] w-full object-cover lg:col-span-1 lg:h-[130px]"
                            />
                        </li>
                    </ol>
                </div>
                <iframe :src="MAP_EMBED" title="Peta lokasi GPdI Hebron Gading Serpong" class="h-[260px] w-full border-0 lg:h-[420px]" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>

        <!-- Kabari kami -->
        <section v-if="welcome.title" class="bg-brand-teal text-white">
            <div class="container-site grid gap-6 py-16 lg:grid-cols-[7fr_5fr] lg:items-end lg:gap-24 lg:py-24">
                <div v-reveal class="flex flex-col gap-6 lg:gap-7">
                    <span aria-hidden="true" class="h-[3px] w-12 bg-decor lg:w-14"></span>
                    <h2 class="font-serif text-[40px] leading-none lg:text-[64px]">{{ welcome.title }}</h2>
                </div>
                <div v-reveal="150" class="flex flex-col gap-6">
                    <p v-if="welcome.desc" class="text-base leading-relaxed text-white/85 lg:text-lg">{{ welcome.desc }}</p>
                    <BaseButton v-if="welcome.button_link" :href="welcome.button_link" external variant="light" class="self-stretch sm:self-start">{{ welcomeText }}</BaseButton>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section v-if="faqs.length" class="bg-mist">
            <div class="container-site section-y grid gap-6 lg:grid-cols-[5fr_7fr] lg:gap-28">
                <div class="flex flex-col gap-5">
                    <SectionHeading eyebrow="Pertanyaan" :title="faq.title || 'Pertanyaan yang sering diajukan'" />
                    <BaseButton v-if="livestreamUrl" :href="livestreamUrl" external variant="link" class="self-start">Belum bisa datang? Tonton live</BaseButton>
                    <BaseButton href="/contact" variant="link" class="self-start">Masih ada pertanyaan? Hubungi kami</BaseButton>
                </div>
                <div class="flex flex-col border-t border-line-strong">
                    <details v-for="(qa, index) in faqs" :key="qa.question" :open="index === 0" class="group border-b border-line-strong py-5 lg:py-7">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-6 text-base font-semibold text-heading marker:hidden lg:text-[19px] [&::-webkit-details-marker]:hidden">
                            {{ qa.question }}
                            <span aria-hidden="true" class="font-serif text-[26px] leading-none text-brand-teal group-open:hidden lg:text-[32px]">+</span>
                            <span aria-hidden="true" class="hidden font-serif text-[26px] leading-none text-brand-teal group-open:inline lg:text-[32px]">−</span>
                        </summary>
                        <div class="rich-text mt-3 max-w-[620px] text-[15px] leading-relaxed text-ink-muted lg:mt-4 lg:text-base" v-html="qa.answer"></div>
                    </details>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
