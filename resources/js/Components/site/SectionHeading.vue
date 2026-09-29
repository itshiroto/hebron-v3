<script setup lang="ts">
withDefaults(
    defineProps<{
        eyebrow: string;
        title: string;
        align?: 'left' | 'center';
        tone?: 'light' | 'dark';
        as?: 'h1' | 'h2';
    }>(),
    { align: 'left', tone: 'light', as: 'h2' },
);
</script>

<template>
    <div
        class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
        :class="align === 'center' ? 'md:justify-center' : ''"
    >
        <div class="flex flex-col gap-4 md:gap-5" :class="align === 'center' ? 'items-center text-center' : ''">
            <p
                class="flex items-center gap-4 text-[11px] font-semibold uppercase tracking-[0.2em] md:text-xs"
                :class="tone === 'dark' ? 'text-brand-orange' : 'text-brand-teal'"
            >
                <span aria-hidden="true" class="h-0.5 w-7 bg-decor md:w-10"></span>{{ eyebrow }}
            </p>
            <component
                :is="as"
                class="font-serif text-[40px] leading-[1.05] md:text-[64px] md:leading-[1.02]"
                :class="tone === 'dark' ? 'text-white' : 'text-heading'"
                >{{ title }}</component
            >
        </div>
        <div v-if="$slots.actions" class="flex items-center gap-6"><slot name="actions" /></div>
    </div>
</template>
