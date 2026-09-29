<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { NAV_ITEMS } from '@/lib/site';
import { useSiteSettings } from '@/composables/useSiteSettings';

const props = defineProps<{ open: boolean }>();
const emit = defineEmits<{ close: [] }>();
const { churchName, logo, services } = useSiteSettings();
const panel = ref<HTMLElement | null>(null);

function focusables(): HTMLElement[] {
    return Array.from(panel.value?.querySelectorAll<HTMLElement>('a[href], button:not([disabled])') ?? []);
}

function onKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
        event.preventDefault();
        emit('close');
        return;
    }
    if (event.key !== 'Tab') return;
    const items = focusables();
    if (!items.length) return;
    const first = items[0];
    const last = items[items.length - 1];
    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
}

watch(
    () => props.open,
    async (open) => {
        document.body.style.overflow = open ? 'hidden' : '';
        if (open) {
            await nextTick();
            focusables()[0]?.focus();
        }
    },
);

onBeforeUnmount(() => {
    if (typeof document !== 'undefined') document.body.style.overflow = '';
});
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-active-class="transition-opacity duration-200"
        leave-to-class="opacity-0"
    >
        <div
            v-if="open"
            id="mobile-menu"
            ref="panel"
            role="dialog"
            aria-modal="true"
            aria-label="Menu"
            class="fixed inset-0 z-[60] flex flex-col bg-brand-deep text-white lg:hidden"
            @keydown="onKeydown"
        >
            <div class="flex h-16 items-center justify-between pl-5 pr-3">
                <Link href="/" class="flex items-center gap-2.5" @click="emit('close')">
                    <img :src="logo" alt="" class="h-7 w-auto" />
                    <span class="font-serif text-[19px]">{{ churchName }}</span>
                </Link>
                <button type="button" aria-label="Tutup menu" class="flex h-11 w-11 items-center justify-center" @click="emit('close')">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>
            <nav aria-label="Utama" class="flex flex-col px-5 pt-10">
                <Link
                    v-for="item in NAV_ITEMS"
                    :key="item.href"
                    :href="item.href"
                    class="border-b border-white/15 py-4 font-serif text-[40px] leading-none"
                    @click="emit('close')"
                    >{{ item.name }}</Link
                >
            </nav>
            <div class="mt-auto flex flex-col gap-5 px-5 pb-10">
                <div v-if="services.length" class="flex flex-col gap-1.5 text-sm text-white/80">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-orange">Setiap Minggu</p>
                    <p>{{ services.map((s) => s.time).join(' · ') }} WIB</p>
                </div>
                <Link
                    href="/contact"
                    class="flex h-[52px] items-center justify-center rounded-button bg-white text-[15px] font-semibold text-heading"
                    @click="emit('close')"
                    >Kunjungi Ibadah Kami</Link
                >
            </div>
        </div>
    </Transition>
</template>
