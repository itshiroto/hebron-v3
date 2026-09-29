<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import SectionHeading from '@/Components/site/SectionHeading.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import { storageUrl } from '@/lib/site';
import type { AboutPageProps, BeliefsSectionData } from '@/types/about-page';
import { vReveal } from '@/lib/reveal';

const props = defineProps<AboutPageProps>();
const beliefs = computed<BeliefsSectionData>(() => (Array.isArray(props.beliefs) ? {} : props.beliefs ?? {}));
const beliefItems = computed(() => beliefs.value.items ?? []);
</script>

<template>
    <Head :title="(!Array.isArray($page.props.meta) && $page.props.meta?.title) || 'Tentang Kami'" />
    <DefaultLayout>
        <PageHero :image="hero?.image" eyebrow="Tentang Kami" :title="hero?.title || 'Tentang Hebron'" :subtitle="hero?.subtitle" />

        <!-- Visi & Misi -->
        <section v-if="visionMission?.visi" class="container-site section-y grid gap-8 lg:grid-cols-[5fr_7fr] lg:items-center lg:gap-28">
            <div v-reveal class="h-[380px] overflow-hidden rounded-t-[170px] rounded-b bg-brand-mint/40 lg:h-[600px] lg:rounded-t-[240px]">
                <img v-if="visionMission.image" :src="storageUrl(visionMission.image)!" alt="" loading="lazy" class="h-full w-full object-cover" />
            </div>
            <div v-reveal="200" class="flex flex-col gap-8 lg:gap-12">
                <div class="flex flex-col gap-3 border-b border-line pb-8 lg:gap-5 lg:border-0 lg:pb-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">{{ visionMission.visi.title }}</p>
                    <div class="rich-text font-serif text-[28px] leading-tight text-heading lg:text-[40px]" v-html="visionMission.visi.desc"></div>
                </div>
                <div class="hidden h-px bg-line lg:block"></div>
                <div class="flex flex-col gap-3 lg:gap-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">{{ visionMission.misi.title }}</p>
                    <div class="rich-text mission-list text-base leading-relaxed lg:text-lg" v-html="visionMission.misi.desc"></div>
                </div>
            </div>
        </section>

        <!-- Nilai-nilai -->
        <section v-if="values?.length" class="bg-brand-deep text-white">
            <div class="container-site section-y flex flex-col gap-8 lg:gap-16">
                <SectionHeading v-reveal eyebrow="Yang kami pegang" title="Nilai-nilai kami" tone="dark" />
                <div class="grid border-t border-white/20 lg:grid-cols-4">
                    <div
                        v-for="(value, index) in values"
                        :key="value.title"
                        v-reveal="index * 120"
                        class="flex flex-col gap-3 border-b border-white/20 py-6 lg:gap-5 lg:border-b-0 lg:py-0 lg:pr-8 lg:pt-10"
                        :class="index > 0 ? 'lg:border-l lg:pl-8' : ''"
                    >
                        <p class="text-xs font-semibold tracking-[0.16em] text-brand-orange lg:text-[13px]">{{ String(index + 1).padStart(2, '0') }}</p>
                        <Icon v-if="value.icon" :icon="value.icon" class="h-9 w-9 text-brand-mint" aria-hidden="true" />
                        <p class="font-serif text-[28px] leading-tight lg:text-[34px]">{{ value.title }}</p>
                        <div class="rich-text text-sm leading-loose text-white/75 lg:text-[15px] [&_a]:text-brand-mint [&>p:first-child]:text-[13px] [&>p:first-child]:font-semibold [&>p:first-child]:tracking-[0.12em] [&>p:first-child]:text-brand-mint" v-html="value.desc"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pengakuan Iman -->
        <section v-if="beliefItems.length" class="container-site section-y grid gap-8 lg:grid-cols-[5fr_7fr] lg:gap-28">
            <div v-reveal class="flex flex-col gap-5 lg:gap-6">
                <SectionHeading eyebrow="Pengakuan Iman" :title="beliefs.title || 'Apa yang kami percaya'" />
                <div v-if="beliefs.intro" class="rich-text text-[15px] leading-relaxed text-ink-muted lg:text-[17px]" v-html="beliefs.intro"></div>
                <BaseButton v-if="beliefs.link" :href="beliefs.link" external variant="link" class="self-start">Baca Pengakuan Iman GPdI lengkap →</BaseButton>
            </div>
            <ol class="grid border-t border-line lg:grid-cols-2 lg:gap-x-12">
                <li v-for="(item, index) in beliefItems" :key="item.title" v-reveal="(index % 2) * 120" class="flex gap-4 border-b border-line py-5 lg:flex-col lg:gap-2.5 lg:py-7">
                    <span class="w-8 shrink-0 font-serif text-2xl leading-tight text-brand-teal lg:text-[28px]">{{ String(index + 1).padStart(2, '0') }}</span>
                    <div class="flex flex-col gap-1.5 lg:gap-2.5">
                        <p class="font-serif text-2xl leading-tight text-heading lg:text-[28px]">{{ item.title }}</p>
                        <p class="text-sm leading-relaxed text-ink-muted lg:text-[15px]">{{ item.desc }}</p>
                        <p v-if="item.verse" class="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-teal lg:text-xs">{{ item.verse }}</p>
                    </div>
                </li>
            </ol>
        </section>

        <!-- Tim Pastoral -->
        <section v-if="pastors?.length" id="pastoral" class="scroll-mt-16 bg-mist lg:scroll-mt-[88px]">
            <div class="container-site section-y flex flex-col gap-8 lg:gap-16">
                <SectionHeading v-reveal eyebrow="Kepemimpinan" title="Tim Pastoral" />
                <div class="grid grid-cols-2 gap-x-3.5 gap-y-7 lg:grid-cols-4 lg:gap-8">
                    <div v-for="(pastor, index) in pastors" :key="pastor.name" v-reveal="(index % 4) * 120" class="flex flex-col gap-2 lg:gap-3">
                        <div class="mb-1.5 h-[210px] overflow-hidden bg-brand-mint/40 lg:mb-2 lg:h-[360px]">
                            <img v-if="pastor.image" :src="storageUrl(pastor.image)!" :alt="pastor.name" loading="lazy" class="h-full w-full object-cover" />
                        </div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-brand-teal lg:text-xs">{{ pastor.position }}</p>
                        <p class="font-serif text-[22px] leading-tight text-heading lg:text-[32px]">{{ pastor.name }}</p>
                    </div>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
