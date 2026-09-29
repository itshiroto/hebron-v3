<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { DevotionSummary } from '@/types/site';
import { CATEGORY_LABELS, formatDate, readingLabel, storageUrl } from '@/lib/site';

withDefaults(defineProps<{ post: DevotionSummary; variant?: 'grid' | 'featured' }>(), { variant: 'grid' });
</script>

<template>
    <Link
        v-if="variant === 'featured'"
        :href="`/devotion/${post.slug}`"
        class="group grid gap-6 text-ink lg:grid-cols-[7fr_5fr] lg:items-center lg:gap-[72px]"
    >
        <div class="h-[240px] overflow-hidden bg-brand-teal lg:h-[480px]">
            <img v-if="post.image" :src="storageUrl(post.image)!" :alt="post.title" class="h-full w-full object-cover" />
        </div>
        <div class="flex flex-col gap-3 lg:gap-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-teal lg:text-xs">Terbaru · {{ CATEGORY_LABELS[post.category] }}</p>
            <p class="font-serif text-[34px] leading-[1.08] text-heading group-hover:underline lg:text-[52px] lg:leading-[1.05]">{{ post.title }}</p>
            <p v-if="post.snippet" class="text-[15px] leading-relaxed text-ink-muted lg:text-[17px]">{{ post.snippet }}</p>
            <p class="text-xs text-ink-muted lg:text-[13px]">{{ formatDate(post.created_at) }} · {{ readingLabel(post.reading_minutes) }}</p>
        </div>
    </Link>
    <Link v-else :href="`/devotion/${post.slug}`" class="group flex gap-4 border-b border-line py-5 text-ink md:flex-col md:gap-4 md:border-0 md:py-0">
        <div class="h-[104px] w-[104px] shrink-0 overflow-hidden bg-brand-mint/40 md:mb-1.5 md:h-[240px] md:w-full">
            <img v-if="post.image" :src="storageUrl(post.image)!" :alt="post.title" loading="lazy" class="h-full w-full object-cover" />
        </div>
        <div class="flex flex-col gap-1.5 md:gap-4">
            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-brand-teal md:text-xs">{{ CATEGORY_LABELS[post.category] }} · {{ formatDate(post.created_at) }}</p>
            <p class="font-serif text-[22px] leading-[1.15] text-heading group-hover:underline md:text-[30px] md:leading-[1.12]">{{ post.title }}</p>
            <p v-if="post.snippet" class="hidden text-[15px] leading-relaxed text-ink-muted md:line-clamp-2">{{ post.snippet }}</p>
            <p class="text-xs text-ink-muted md:text-[13px]">{{ readingLabel(post.reading_minutes) }}</p>
        </div>
    </Link>
</template>
