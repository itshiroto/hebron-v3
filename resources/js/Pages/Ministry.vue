<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import SectionHeading from '@/Components/site/SectionHeading.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import { storageUrl } from '@/lib/site';
import type { MinistryPageSections } from '@/types/ministry-page';

const props = defineProps<{ sections: MinistryPageSections }>();
const hero = computed(() => props.sections.hero);
const intro = computed(() => props.sections.intro);
const ministries = computed(() => props.sections.serve?.ministryList ?? []);
const faqs = computed(() => props.sections.faq?.questionsList ?? []);
</script>

<template>
    <Head title="Pelayanan" />
    <DefaultLayout>
        <PageHero :image="hero?.image" eyebrow="Pelayanan" :title="hero?.title || 'Pelayanan di Hebron Family Church'" />

        <section v-if="intro?.title || intro?.desc" class="container-site section-y grid gap-5 lg:grid-cols-[5fr_7fr] lg:gap-28">
            <h2 class="font-serif text-[34px] leading-[1.08] text-heading lg:text-[56px] lg:leading-[1.05]">{{ intro?.title }}</h2>
            <div class="rich-text text-base leading-relaxed text-ink lg:text-[19px]" v-html="intro?.desc"></div>
        </section>

        <section v-if="ministries.length" class="container-site flex flex-col gap-10 pb-16 lg:gap-14 lg:pb-36">
            <div class="border-t border-line pt-10 lg:pt-14">
                <SectionHeading eyebrow="Di mana saya bisa melayani?" :title="sections.serve?.title || 'Wadah pelayanan kami'" />
            </div>
            <div class="grid gap-11 lg:grid-cols-3 lg:gap-x-10 lg:gap-y-[72px]">
                <article v-for="(ministry, index) in ministries" :key="ministry.title" class="flex flex-col gap-2.5 lg:gap-4">
                    <div class="mb-1.5 h-[220px] overflow-hidden bg-brand-mint/40 lg:mb-2 lg:h-[280px]">
                        <img v-if="ministry.image" :src="storageUrl(ministry.image)!" :alt="ministry.title" loading="lazy" class="h-full w-full object-cover" />
                    </div>
                    <p class="text-xs font-semibold tracking-[0.16em] text-brand-teal lg:text-[13px]">{{ String(index + 1).padStart(2, '0') }}</p>
                    <h3 class="font-serif text-[28px] leading-[1.05] text-heading lg:text-[34px]">{{ ministry.title }}</h3>
                    <div class="rich-text text-[15px] leading-relaxed text-ink-muted" v-html="ministry.desc"></div>
                    <BaseButton v-if="ministry.link" :href="ministry.link" external variant="link" class="mt-auto !justify-start self-stretch border-t border-line pt-3.5">Daftar melayani →</BaseButton>
                </article>
            </div>
        </section>

        <section v-if="faqs.length" class="bg-mist">
            <div class="container-site section-y grid gap-6 lg:grid-cols-[5fr_7fr] lg:gap-28">
                <div class="flex flex-col gap-5">
                    <SectionHeading eyebrow="Pertanyaan" :title="sections.faq?.title || 'Pertanyaan yang sering diajukan'" />
                    <BaseButton href="/contact" variant="link" class="self-start">Masih ragu? Hubungi kami</BaseButton>
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
