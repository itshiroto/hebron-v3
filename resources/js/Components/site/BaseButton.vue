<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = withDefaults(
    defineProps<{
        href?: string | null;
        variant?: 'primary' | 'outline' | 'outline-light' | 'light' | 'accent' | 'link' | 'link-light';
        external?: boolean;
        type?: 'button' | 'submit';
    }>(),
    { href: null, variant: 'primary', external: false, type: 'button' },
);

const VARIANTS = {
    primary: 'bg-heading text-white hover:bg-brand-teal focus-visible:outline-brand-teal',
    outline: 'border border-heading text-heading hover:bg-heading hover:text-white focus-visible:outline-brand-teal',
    'outline-light': 'border border-white/70 text-white hover:bg-white hover:text-heading focus-visible:outline-brand-orange',
    light: 'bg-white text-heading hover:bg-brand-cream focus-visible:outline-brand-orange',
    accent: 'bg-brand-orange text-heading hover:bg-brand-orange/90 focus-visible:outline-white',
    link: '!min-h-0 !px-0 text-brand-teal hover:text-heading focus-visible:outline-brand-teal',
    'link-light': '!min-h-0 !px-0 text-white hover:text-brand-mint focus-visible:outline-brand-orange',
} as const;

const classes = computed(
    () =>
        `inline-flex min-h-[44px] items-center justify-center gap-2.5 rounded-button px-6 text-sm font-semibold transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 ${VARIANTS[props.variant]}`,
);
const internal = computed(() => !!props.href && !props.external && props.href.startsWith('/'));
</script>

<template>
    <Link v-if="internal" :href="href!" :class="classes"><slot /></Link>
    <a
        v-else-if="href"
        :href="href"
        :class="classes"
        :target="external ? '_blank' : undefined"
        :rel="external ? 'noopener noreferrer' : undefined"
        ><slot
    /></a>
    <button v-else :type="type" :class="classes"><slot /></button>
</template>
