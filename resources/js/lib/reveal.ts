import type { Directive } from 'vue';

// Fades and rises an element in the first time it scrolls into view.
// Usage: v-reveal or v-reveal="150" (delay in ms, for staggering siblings).
// Server render and reduced-motion users get the element as-is.
let observer: IntersectionObserver | null = null;

function getObserver() {
    return (observer ??= new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue;
                entry.target.classList.add('is-revealed');
                observer!.unobserve(entry.target);
            }
        },
        { rootMargin: '0px 0px -10% 0px' },
    ));
}

export const vReveal: Directive<HTMLElement, number | undefined> = {
    mounted(el, { value }) {
        if (typeof IntersectionObserver === 'undefined' || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        el.classList.add('reveal');
        if (value) el.style.setProperty('--reveal-delay', `${value}ms`);
        getObserver().observe(el);
    },
    unmounted(el) {
        observer?.unobserve(el);
    },
};
