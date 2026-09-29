<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PostCard from '@/Components/site/PostCard.vue';
import type { DevotionCategory, DevotionSummary, Paginated } from '@/types/site';

const props = defineProps<{ posts: Paginated<DevotionSummary>; category: DevotionCategory | null }>();

const tabs: { label: string; value: DevotionCategory | null }[] = [
    { label: 'Semua', value: null },
    { label: 'Renungan', value: 'devotion' },
    { label: 'Berita', value: 'news' },
    { label: 'Pengumuman', value: 'announcement' },
];
const tabHref = (value: DevotionCategory | null) => (value ? `/devotion?category=${value}` : '/devotion');
const featured = computed(() => (props.posts.current_page === 1 ? props.posts.data[0] : null));
const rest = computed(() => (featured.value ? props.posts.data.slice(1) : props.posts.data));
const pageLinks = computed(() => props.posts.links.slice(1, -1));
</script>

<template>
    <Head title="Renungan & Berita" />
    <DefaultLayout>
        <section class="bg-brand-deep pt-16 text-white lg:pt-[88px]">
            <div class="container-site flex flex-col gap-3.5 pt-12 lg:gap-5 lg:pt-24">
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-orange lg:text-[11px]">Renungan &amp; Berita</p>
                <h1 class="font-serif text-5xl leading-none lg:text-[80px]">Terbaru dari Firman</h1>
            </div>
            <nav aria-label="Filter kategori" class="container-site mt-9 flex gap-6 overflow-x-auto border-b border-white/15 lg:mt-16 lg:gap-10">
                <Link
                    v-for="tab in tabs"
                    :key="tab.label"
                    :href="tabHref(tab.value)"
                    :aria-current="category === tab.value ? 'page' : undefined"
                    preserve-scroll
                    class="shrink-0 border-b-2 pb-4 text-[13px] font-semibold tracking-[0.08em] lg:pb-5 lg:text-sm"
                    :class="category === tab.value ? 'border-brand-orange text-white' : 'border-transparent text-white/70 hover:text-white'"
                    >{{ tab.label }}</Link
                >
            </nav>
        </section>

        <p v-if="!posts.data.length" class="container-site section-y text-ink-muted">Belum ada tulisan di kategori ini.</p>

        <section v-if="featured" class="container-site border-b border-line py-10 lg:py-24">
            <PostCard :post="featured" variant="featured" />
        </section>

        <section v-if="rest.length || posts.last_page > 1" class="container-site flex flex-col gap-8 pb-14 pt-4 lg:gap-20 lg:pb-32 lg:pt-24">
            <div class="md:grid md:grid-cols-3 md:gap-x-10 md:gap-y-[72px]">
                <PostCard v-for="post in rest" :key="post.id" :post="post" />
            </div>

            <nav v-if="posts.last_page > 1" aria-label="Halaman" class="hidden items-center justify-center gap-2 text-sm font-semibold md:flex">
                <template v-for="link in pageLinks" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        :aria-current="link.active ? 'page' : undefined"
                        class="flex h-11 w-11 items-center justify-center"
                        :class="link.active ? 'bg-heading text-white' : 'border border-line-strong text-heading hover:border-heading'"
                        >{{ link.label }}</Link
                    >
                    <span v-else class="flex h-11 w-11 items-center justify-center text-ink-muted">{{ link.label }}</span>
                </template>
                <Link v-if="posts.next_page_url" :href="posts.next_page_url" class="flex h-11 items-center gap-2 border border-line-strong px-4 text-heading hover:border-heading">Berikutnya →</Link>
            </nav>
            <Link
                v-if="posts.next_page_url"
                :href="posts.next_page_url"
                class="flex h-[50px] items-center justify-center rounded-button border border-heading text-sm font-semibold text-heading md:hidden"
                >Muat lebih banyak</Link
            >
        </section>
    </DefaultLayout>
</template>
