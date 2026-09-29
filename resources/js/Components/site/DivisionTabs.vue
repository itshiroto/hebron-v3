<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import type { DivisionItem } from '@/types/site';
import { storageUrl } from '@/lib/site';
import BaseButton from './BaseButton.vue';

const props = defineProps<{ divisions: DivisionItem[] }>();
const selected = ref(0);
const openIndex = ref(0);
const tabs = ref<HTMLButtonElement[]>([]);
const current = computed(() => props.divisions[selected.value]);
const total = computed(() => String(props.divisions.length).padStart(2, '0'));
const num = (i: number) => String(i + 1).padStart(2, '0');

function indexFromHash(): number {
    const slug = decodeURIComponent(window.location.hash.replace(/^#/, ''));
    const found = props.divisions.findIndex((d) => d.slug === slug);
    return found >= 0 ? found : 0;
}

function select(i: number) {
    selected.value = i;
    openIndex.value = i;
    history.replaceState(history.state, '', `#${props.divisions[i].slug}`);
}

function toggle(i: number) {
    openIndex.value = openIndex.value === i ? -1 : i;
    if (openIndex.value === i) select(i);
}

function onKeydown(event: KeyboardEvent, i: number) {
    const n = props.divisions.length;
    const next = { ArrowDown: (i + 1) % n, ArrowUp: (i - 1 + n) % n, Home: 0, End: n - 1 }[event.key];
    if (next === undefined) return;
    event.preventDefault();
    select(next);
    tabs.value[next]?.focus();
}

onMounted(() => {
    if (!props.divisions.length) return;
    const i = indexFromHash();
    selected.value = i;
    openIndex.value = i;
});
</script>

<template>
    <p v-if="!divisions.length" class="text-ink-muted">Belum ada divisi.</p>

    <!-- Desktop: list + detail -->
    <div v-if="divisions.length" class="hidden gap-[72px] lg:grid lg:grid-cols-[5fr_7fr] lg:items-start">
        <div role="tablist" aria-label="Divisi" aria-orientation="vertical" class="flex flex-col border-t border-line">
            <button
                v-for="(division, i) in divisions"
                :id="`tab-${division.slug}`"
                :key="division.id"
                ref="tabs"
                type="button"
                role="tab"
                :aria-selected="i === selected"
                :aria-controls="`panel-${division.slug}`"
                :tabindex="i === selected ? 0 : -1"
                class="flex min-h-[88px] w-full items-center justify-between gap-4 border-b border-line px-5 py-4 text-left transition-colors focus-visible:outline-solid focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand-teal"
                :class="i === selected ? 'bg-mist shadow-[inset_3px_0_0_var(--color-decor)]' : 'hover:bg-mist/60'"
                @click="select(i)"
                @keydown="onKeydown($event, i)"
            >
                <span class="flex flex-col gap-1">
                    <span class="font-serif text-[28px] leading-[1.05] text-heading">{{ division.title }}</span>
                    <span v-if="division.audience" class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink-muted">{{ division.audience }}</span>
                </span>
                <span v-if="division.short_time" class="text-right text-[13px] font-semibold" :class="division.time ? 'text-brand-teal' : 'text-ink-muted'">{{ division.short_time }}</span>
            </button>
        </div>
        <div v-if="current" :id="`panel-${current.slug}`" role="tabpanel" :aria-labelledby="`tab-${current.slug}`" tabindex="0" class="flex flex-col gap-5 focus-visible:outline-hidden">
            <div class="h-[460px] overflow-hidden bg-brand-mint/40">
                <img v-if="current.image" :src="storageUrl(current.image)!" :alt="current.title" class="h-full w-full object-cover" />
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.2em] text-ink-muted">{{ num(selected) }} / {{ total }}<template v-if="current.audience"> · {{ current.audience }}</template></p>
            <h2 class="font-serif text-[56px] leading-[1.02] text-heading">{{ current.title }}</h2>
            <p class="text-base font-semibold" :class="current.time ? 'text-brand-teal' : 'text-ink-muted'">{{ current.time || 'Jadwal akan diumumkan' }}</p>
            <p class="max-w-[560px] text-[17px] leading-relaxed text-ink-muted">{{ current.description }}</p>
            <BaseButton v-if="current.link" :href="current.link" :external="!current.link.startsWith('/')" class="self-start">Selengkapnya →</BaseButton>
        </div>
    </div>

    <!-- Mobile: accordion -->
    <div v-if="divisions.length" class="flex flex-col border-t border-line lg:hidden">
        <div
            v-for="(division, i) in divisions"
            :key="division.id"
            class="border-b border-line"
            :class="openIndex === i ? 'bg-mist shadow-[inset_3px_0_0_var(--color-decor)]' : ''"
        >
            <h2>
                <button
                    type="button"
                    :aria-expanded="openIndex === i"
                    :aria-controls="`acc-${division.slug}`"
                    class="flex min-h-[76px] w-full items-center justify-between gap-3 px-4 py-3.5 text-left"
                    @click="toggle(i)"
                >
                    <span class="flex flex-col gap-1">
                        <span class="font-serif text-2xl leading-[1.05] text-heading">{{ division.title }}</span>
                        <span v-if="division.audience" class="text-[10px] font-semibold uppercase tracking-[0.18em] text-ink-muted">{{ division.audience }}</span>
                    </span>
                    <span class="flex items-center gap-3">
                        <span v-if="division.short_time" class="text-xs font-semibold" :class="division.time ? 'text-brand-teal' : 'text-ink-muted'">{{ division.short_time }}</span>
                        <span aria-hidden="true" class="font-serif text-2xl leading-none text-brand-teal">{{ openIndex === i ? '−' : '+' }}</span>
                    </span>
                </button>
            </h2>
            <div v-show="openIndex === i" :id="`acc-${division.slug}`" class="flex flex-col gap-3 px-4 pb-5">
                <div class="h-[220px] overflow-hidden bg-brand-mint/40">
                    <img v-if="division.image" :src="storageUrl(division.image)!" :alt="division.title" loading="lazy" class="h-full w-full object-cover" />
                </div>
                <p class="mt-1 text-sm font-semibold" :class="division.time ? 'text-brand-teal' : 'text-ink-muted'">{{ division.time || 'Jadwal akan diumumkan' }}</p>
                <p class="text-[15px] leading-relaxed text-ink-muted">{{ division.description }}</p>
                <BaseButton v-if="division.link" :href="division.link" :external="!division.link.startsWith('/')">Selengkapnya</BaseButton>
            </div>
        </div>
    </div>
</template>
