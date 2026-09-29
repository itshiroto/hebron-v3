<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import SectionHeading from '@/Components/site/SectionHeading.vue';
import DivisionCard from '@/Components/site/DivisionCard.vue';
import PostCard from '@/Components/site/PostCard.vue';
import { useSiteSettings } from '@/composables/useSiteSettings';
import { MOTTO, storageUrl } from '@/lib/site';
import type { HomePageProps } from '@/types/home-page';
import { vReveal } from '@/lib/reveal';

const props = defineProps<HomePageProps>();
const { address, mapsUrl, livestreamUrl, services, socials } = useSiteSettings();

const gallery = computed(() => (props.carousel ?? []).slice(0, 4));
const instagram = computed(() => socials.value.find((s) => s.platform.toLowerCase().includes('instagram'))?.url ?? null);
const steps = computed(() => [
    { n: '01', title: 'Pelayanan', desc: 'Layani Tuhan dengan talenta dan karunia yang Ia berikan.', href: '/ministry', image: props.carousel?.[4] ?? null },
    { n: '02', title: 'Renungan', desc: 'Renungan Firman untuk berjalan lebih dekat dengan Kristus setiap hari.', href: '/devotion', image: props.carousel?.[5] ?? null },
    { n: '03', title: 'Hubungi Kami', desc: 'Pertanyaan, permohonan doa, atau ingin berbincang dengan gembala kami.', href: '/contact', image: props.carousel?.[6] ?? null },
]);

const track = ref<HTMLElement | null>(null);
const progress = ref(0);
function updateProgress() {
    const el = track.value;
    if (!el) return;
    const max = el.scrollWidth - el.clientWidth;
    progress.value = max > 0 ? el.scrollLeft / max : 1;
}
function scrollDivisions(direction: 1 | -1) {
    const el = track.value;
    if (!el) return;
    el.scrollBy({ left: direction * el.clientWidth * 0.8, behavior: 'smooth' });
}
onMounted(updateProgress);
</script>

<template>
    <Head :title="(!Array.isArray($page.props.meta) && $page.props.meta?.title) || 'Beranda'" />
    <DefaultLayout>
        <PageHero :image="hero?.image" :video="hero?.video" size="full" eyebrow="GPdI Hebron · Gading Serpong" :title="hero?.title || MOTTO">
            <BaseButton href="/contact" variant="light">Kunjungi Ibadah Kami</BaseButton>
            <BaseButton v-if="livestreamUrl" :href="livestreamUrl" external variant="link-light">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M10 8.5l5 3.5-5 3.5z" /></svg>
                Tonton Online
            </BaseButton>
        </PageHero>

        <!-- Ibadah Raya band -->
        <section v-if="services.length" class="bg-brand-deep text-white">
            <div class="container-site grid gap-6 py-12 lg:grid-cols-4 lg:items-center lg:gap-12 lg:py-14">
                <h2 v-reveal class="font-serif text-[32px] leading-tight lg:text-4xl">Ibadah Raya</h2>
                <div
                    v-for="(service, index) in services"
                    :key="service.title"
                    v-reveal="(index + 1) * 120"
                    class="flex items-center justify-between border-t border-white/20 pt-4 lg:flex-col lg:items-start lg:gap-1.5 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0"
                >
                    <div class="flex flex-col gap-1.5">
                        <p class="text-[13px] font-semibold uppercase tracking-[0.14em] text-brand-orange lg:text-xs lg:font-normal lg:tracking-[0.18em]">{{ service.title }}</p>
                        <p v-if="service.livestream" class="flex items-center gap-2 text-xs font-semibold lg:order-last">
                            <span aria-hidden="true" class="h-2 w-2 rounded-full bg-decor"></span>Live Streaming
                        </p>
                    </div>
                    <p class="font-serif text-[32px] lg:text-[40px]">{{ service.time }} <span class="font-sans text-xs text-white/70 lg:text-sm">WIB</span></p>
                </div>
            </div>
        </section>

        <!-- Divisi carousel -->
        <section v-if="divisions.length" class="section-y">
            <div class="container-site">
                <SectionHeading v-reveal :eyebrow="`${divisions.length} Divisi`" title="Ibadah untuk setiap generasi" actions-class="hidden lg:flex">
                    <template #actions>
                        <BaseButton href="/division" variant="link">Semua divisi →</BaseButton>
                        <div class="flex gap-2">
                            <button type="button" aria-label="Divisi sebelumnya" class="flex h-12 w-12 items-center justify-center rounded-full border border-line-strong text-heading focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-teal" @click="scrollDivisions(-1)">←</button>
                            <button type="button" aria-label="Divisi berikutnya" class="flex h-12 w-12 items-center justify-center rounded-full bg-heading text-white focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-teal" @click="scrollDivisions(1)">→</button>
                        </div>
                    </template>
                </SectionHeading>
            </div>
            <div
                ref="track"
                v-reveal="150"
                class="mt-8 flex snap-x snap-mandatory scroll-pl-5 gap-3.5 overflow-x-auto pl-5 pr-5 scrollbar-none lg:mt-14 lg:scroll-pl-[120px] lg:gap-6 lg:pl-[120px]"
                @scroll.passive="updateProgress"
            >
                <DivisionCard v-for="division in divisions" :key="division.id" :division="division" />
            </div>
            <div class="container-site mt-8 flex items-center gap-4">
                <div class="relative h-0.5 grow bg-line">
                    <div class="absolute inset-y-0 left-0 bg-heading transition-[width]" :style="{ width: `${Math.max(progress, 1 / divisions.length) * 100}%` }"></div>
                </div>
                <BaseButton href="/division" variant="link" class="shrink-0 lg:hidden">Semua divisi →</BaseButton>
            </div>
        </section>

        <!-- Visi & Misi -->
        <section v-if="visionMission?.visi" class="bg-mist lg:bg-white">
            <div class="container-site section-y grid gap-10 lg:grid-cols-[5fr_7fr] lg:gap-28">
                <div v-reveal class="flex flex-col gap-6">
                    <SectionHeading eyebrow="Tentang Hebron" :title="visionMission.title || 'Visi & Misi'" />
                    <BaseButton href="/about" variant="link" class="self-start">{{ visionMission.about_button_text || 'Selengkapnya tentang Hebron' }} →</BaseButton>
                </div>
                <div class="flex flex-col">
                    <div v-reveal="150" class="flex flex-col gap-3 border-b border-line pb-10 lg:grid lg:grid-cols-[160px_1fr] lg:gap-8 lg:pb-12">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-ink-muted lg:pt-3 lg:text-xs">{{ visionMission.visi.title }}</p>
                        <div class="rich-text font-serif text-[26px] leading-tight text-heading lg:text-[34px] lg:leading-snug" v-html="visionMission.visi.desc"></div>
                    </div>
                    <div v-reveal="300" class="flex flex-col gap-3 pt-10 lg:grid lg:grid-cols-[160px_1fr] lg:gap-8 lg:pt-12">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-ink-muted lg:pt-1.5 lg:text-xs">{{ visionMission.misi.title }}</p>
                        <div class="rich-text mission-list text-base leading-relaxed lg:text-lg" v-html="visionMission.misi.desc"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sambutan gembala -->
        <section v-if="pastorGreeting?.pastors_name" class="bg-white lg:bg-mist">
            <div class="container-site section-y grid gap-8 lg:grid-cols-[5fr_7fr] lg:items-center lg:gap-28">
                <div v-reveal class="relative h-[400px] bg-brand-mint/40 lg:h-[620px]">
                    <img v-if="pastorGreeting.pastor_img" :src="storageUrl(pastorGreeting.pastor_img)!" :alt="pastorGreeting.pastors_name" loading="lazy" class="h-full w-full object-cover" />
                    <img v-if="pastorGreeting.pastor_signature" :src="storageUrl(pastorGreeting.pastor_signature)!" alt="" class="absolute bottom-4 right-4 h-24 lg:h-32" />
                </div>
                <div v-reveal="200" class="flex flex-col gap-6 lg:gap-8">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">{{ pastorGreeting.subtitle || 'Sambutan dari gembala kami' }}</p>
                    <div class="rich-text font-serif text-[28px] italic leading-tight text-heading lg:text-[40px] lg:leading-snug" v-html="pastorGreeting.desc"></div>
                    <div class="flex flex-col gap-1">
                        <p class="font-serif text-2xl text-heading lg:text-[28px]">{{ pastorGreeting.pastors_name }}</p>
                        <p class="text-[13px] text-ink-muted lg:text-sm">Gembala Sidang</p>
                    </div>
                    <BaseButton href="/about#pastoral" variant="outline" class="self-stretch lg:self-start">{{ pastorGreeting.pastoral_button_text || 'Lihat tim pastoral Hebron' }} →</BaseButton>
                </div>
            </div>
        </section>

        <!-- Galeri -->
        <section v-if="gallery.length" class="pt-16 lg:pt-32">
            <div class="container-site">
                <SectionHeading v-reveal eyebrow="Kehidupan di Hebron" title="Momen sebagai satu keluarga">
                    <template v-if="instagram" #actions>
                        <BaseButton :href="instagram" external variant="link">Ikuti kami di Instagram</BaseButton>
                    </template>
                </SectionHeading>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-1.5 px-1.5 lg:mt-14 lg:h-[420px] lg:grid-cols-4 lg:gap-3 lg:px-3">
                <img
                    v-for="(image, index) in gallery"
                    :key="image"
                    v-reveal="index * 100"
                    :src="storageUrl(image)!"
                    alt=""
                    loading="lazy"
                    class="h-[200px] w-full object-cover lg:h-full"
                    :class="index % 2 ? 'lg:mt-14 lg:h-[calc(100%-56px)]' : ''"
                />
            </div>
        </section>

        <!-- Langkah selanjutnya -->
        <section class="bg-mist lg:bg-white">
            <div class="container-site section-y flex flex-col gap-10 lg:gap-[72px]">
                <SectionHeading v-reveal eyebrow="Bertumbuh bersama kami" title="Temukan tempatmu di keluarga Hebron" align="center" />
                <div class="grid gap-10 lg:grid-cols-3">
                    <Link v-for="(step, index) in steps" :key="step.n" v-reveal="index * 150" :href="step.href" class="group flex flex-col gap-3 text-ink focus-visible:outline-solid focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-teal">
                        <div class="h-[200px] bg-brand-mint/40 lg:h-[320px]">
                            <img v-if="step.image" :src="storageUrl(step.image)!" alt="" loading="lazy" class="h-full w-full object-cover" />
                        </div>
                        <span class="mt-2 text-xs font-semibold tracking-[0.2em] text-brand-teal">{{ step.n }}</span>
                        <span class="font-serif text-[30px] leading-none text-heading group-hover:underline lg:text-[38px]">{{ step.title }}</span>
                        <span class="text-[15px] leading-relaxed text-ink-muted lg:text-base">{{ step.desc }}</span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Renungan terbaru -->
        <section v-if="latestDevotions.length" class="bg-white lg:bg-mist">
            <div class="container-site section-y flex flex-col gap-8 lg:gap-14">
                <SectionHeading v-reveal eyebrow="Renungan & Berita" title="Terbaru dari Firman">
                    <template #actions><BaseButton href="/devotion" variant="link" class="self-start">Lihat semua renungan →</BaseButton></template>
                </SectionHeading>
                <div class="border-t border-line md:grid md:grid-cols-3 md:gap-10 md:border-0">
                    <PostCard v-for="(post, index) in latestDevotions" :key="post.id" v-reveal="index * 150" :post="post" />
                </div>
            </div>
        </section>

        <!-- Mari beribadah -->
        <section class="bg-brand-teal text-white">
            <div class="container-site grid gap-6 py-16 lg:grid-cols-[7fr_5fr] lg:items-center lg:gap-24 lg:py-[120px]">
                <div v-reveal class="flex flex-col gap-6 lg:gap-7">
                    <span aria-hidden="true" class="h-[3px] w-12 bg-decor lg:w-14"></span>
                    <h2 class="font-serif text-[44px] leading-none lg:text-[80px]">Mari beribadah bersama kami</h2>
                    <p class="max-w-[520px] text-base leading-relaxed text-white/85 lg:text-lg">Baik pertama kali datang maupun kembali pulang, selalu ada tempat untukmu di keluarga Hebron.</p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
                        <BaseButton :href="mapsUrl" external variant="light">Petunjuk arah</BaseButton>
                        <BaseButton href="/contact" variant="outline-light">Hubungi kami</BaseButton>
                    </div>
                </div>
                <div v-reveal="200" class="flex flex-col gap-8 lg:border-l lg:border-white/25 lg:pl-12">
                    <div class="flex flex-col gap-2.5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-cream">Alamat</p>
                        <div class="rich-text text-sm leading-relaxed text-white/85 lg:text-lg" v-html="address"></div>
                    </div>
                    <div v-if="services.length" class="flex flex-col gap-2.5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-cream">Setiap Minggu</p>
                        <p class="text-sm lg:text-lg">{{ services.map((s) => s.time).join(' · ') }} WIB</p>
                        <p v-if="services.some((s) => s.livestream)" class="text-sm text-white/75">
                            {{ services.filter((s) => s.livestream).map((s) => `${s.title} (${s.time} WIB)`).join(', ') }} juga disiarkan secara live streaming.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
