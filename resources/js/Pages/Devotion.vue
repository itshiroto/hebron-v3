<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PostCard from '@/Components/site/PostCard.vue';
import { CATEGORY_LABELS, formatDate, readingLabel, storageUrl } from '@/lib/site';
import type { DevotionFull, DevotionSummary } from '@/types/site';

const props = defineProps<{ data: DevotionFull; related: DevotionSummary[] }>();
const shareUrl = ref('');
const copied = ref(false);

onMounted(() => {
    shareUrl.value = window.location.href;
});

// Clipboard API can be unavailable (http, iframes, denied permission); fall back to a hidden textarea.
function legacyCopy(text: string): boolean {
    const area = document.createElement('textarea');
    area.value = text;
    area.setAttribute('readonly', '');
    area.style.position = 'fixed';
    area.style.opacity = '0';
    document.body.appendChild(area);
    area.select();
    const ok = document.execCommand('copy');
    area.remove();
    return ok;
}

async function copyLink() {
    let ok = false;
    try {
        await navigator.clipboard.writeText(shareUrl.value);
        ok = true;
    } catch {
        ok = legacyCopy(shareUrl.value);
    }
    copied.value = ok;
    if (ok) setTimeout(() => (copied.value = false), 2000);
}
</script>

<template>
    <Head :title="data.title" />
    <DefaultLayout>
        <section class="bg-brand-deep pb-[120px] pt-14 text-white lg:pb-60 lg:pt-16">
            <div class="container-site flex flex-col gap-4 pt-10 lg:items-center lg:gap-6 lg:pt-24 lg:text-center">
                <Link href="/devotion" class="text-[13px] font-semibold text-white/70 hover:text-white">← Semua renungan</Link>
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-orange lg:text-xs">{{ CATEGORY_LABELS[data.category] }}</p>
                <h1 class="max-w-[960px] font-serif text-[42px] leading-[1.04] lg:text-[76px] lg:leading-[1.02]">{{ data.title }}</h1>
                <p class="text-[13px] text-white/70 lg:text-sm">{{ formatDate(data.created_at) }} · {{ readingLabel(data.reading_minutes) }}</p>
            </div>
        </section>

        <div class="container-site mt-[-88px] lg:-mt-44">
            <div class="h-[240px] overflow-hidden bg-brand-teal lg:h-[600px]">
                <img v-if="data.image" :src="storageUrl(data.image)!" :alt="data.title" class="h-full w-full object-cover" />
            </div>
        </div>

        <article class="mx-auto max-w-[720px] px-5 pb-16 pt-10 lg:px-0 lg:pb-32 lg:pt-24">
            <div
                class="rich-text text-[17px] leading-[1.8] text-ink lg:text-lg [&>p:first-child]:font-serif [&>p:first-child]:text-2xl [&>p:first-child]:leading-snug [&>p:first-child]:text-heading lg:[&>p:first-child]:text-[30px]"
                v-html="data.content"
            ></div>
            <div class="mt-8 flex flex-col gap-3.5 border-t border-line pt-6 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-ink-muted lg:text-[13px]">Bagikan</p>
                <div class="grid grid-cols-3 gap-2 lg:flex">
                    <a :href="`https://wa.me/?text=${encodeURIComponent(`${data.title} ${shareUrl}`)}`" target="_blank" rel="noopener noreferrer" class="flex h-11 items-center justify-center border border-line-strong px-4 text-[13px] font-semibold text-heading">WhatsApp</a>
                    <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`" target="_blank" rel="noopener noreferrer" class="flex h-11 items-center justify-center border border-line-strong px-4 text-[13px] font-semibold text-heading">Facebook</a>
                    <button type="button" class="flex h-11 items-center justify-center border border-line-strong bg-white px-4 text-[13px] font-semibold text-heading" @click="copyLink">
                        {{ copied ? 'Tersalin' : 'Salin tautan' }}
                    </button>
                </div>
            </div>
        </article>

        <section v-if="related.length" class="bg-mist">
            <div class="container-site flex flex-col gap-2 py-14 lg:gap-14 lg:pb-32 lg:pt-28">
                <div class="flex items-end justify-between">
                    <h2 class="font-serif text-[34px] leading-[1.05] text-heading lg:text-[52px]">Baca juga</h2>
                    <Link href="/devotion" class="hidden text-[15px] font-semibold text-brand-teal lg:inline">Lihat semua renungan</Link>
                </div>
                <div class="md:grid md:grid-cols-3 md:gap-10">
                    <PostCard v-for="post in related" :key="post.id" :post="post" />
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
