<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { DivisionItem } from '@/types/site';
import { storageUrl } from '@/lib/site';

defineProps<{ division: DivisionItem }>();
</script>

<template>
    <Link :href="`/division#${division.slug}`" class="group flex w-[260px] shrink-0 snap-start flex-col gap-2.5 text-ink lg:w-[290px] lg:gap-3.5">
        <div class="mb-1.5 h-[300px] overflow-hidden bg-brand-mint/40 lg:h-[360px]">
            <img
                v-if="division.image"
                :src="storageUrl(division.image)!"
                :alt="division.title"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
        </div>
        <p v-if="division.audience" class="text-[11px] font-semibold uppercase tracking-[0.2em] text-ink-muted lg:text-xs">{{ division.audience }}</p>
        <p class="font-serif text-[28px] leading-[1.05] text-heading lg:text-[32px]">{{ division.title }}</p>
        <p v-if="division.time" class="text-sm font-semibold text-brand-teal lg:text-[15px]">{{ division.time }}</p>
        <p v-else class="text-sm font-medium text-ink-muted lg:text-[15px]">Jadwal akan diumumkan</p>
        <span class="mt-1 hidden border-t border-line pt-3.5 text-sm font-semibold text-brand-teal lg:block">Selengkapnya →</span>
    </Link>
</template>
