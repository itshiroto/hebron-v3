<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { storageUrl } from '@/lib/site';

const props = withDefaults(
    defineProps<{
        image?: string | null;
        video?: string | null;
        eyebrow: string;
        title: string;
        subtitle?: string | null;
        size?: 'full' | 'page';
    }>(),
    { image: null, video: null, subtitle: null, size: 'page' },
);

const imageUrl = computed(() => storageUrl(props.image));
const videoUrl = computed(() => storageUrl(props.video));
const videoEl = ref<HTMLVideoElement | null>(null);
const playing = ref(false);

function play() {
    videoEl.value
        ?.play()
        .then(() => (playing.value = true))
        .catch(() => (playing.value = false));
}

onMounted(() => {
    if (videoEl.value && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) play();
});

function toggle() {
    if (!videoEl.value) return;
    if (videoEl.value.paused) {
        play();
    } else {
        videoEl.value.pause();
        playing.value = false;
    }
}
</script>

<template>
    <section
        class="relative isolate overflow-hidden bg-brand-deep text-white"
        :class="size === 'full' ? 'h-[100svh] min-h-[560px]' : 'h-[480px] md:h-[560px]'"
    >
        <img v-if="imageUrl" :src="imageUrl" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" />
        <video
            v-if="videoUrl"
            ref="videoEl"
            :src="videoUrl"
            :poster="imageUrl ?? undefined"
            muted
            playsinline
            loop
            preload="metadata"
            aria-hidden="true"
            class="absolute inset-0 -z-20 h-full w-full object-cover"
        ></video>
        <div class="absolute inset-0 -z-10 bg-brand-deeper/20"></div>
        <!-- Readability gradients: top for the overlay header, bottom-left for the hero text -->
        <div aria-hidden="true" class="absolute inset-x-0 top-0 -z-10 h-32 bg-gradient-to-b from-brand-deeper/60 to-transparent lg:h-40"></div>
        <div
            aria-hidden="true"
            class="absolute inset-x-0 bottom-0 -z-10 h-3/4 bg-gradient-to-t from-brand-deeper/85 via-brand-deeper/40 to-transparent md:bg-gradient-to-tr md:from-brand-deeper/80 md:via-brand-deeper/35"
        ></div>

        <div class="absolute inset-x-5 bottom-10 flex max-w-3xl flex-col gap-4 md:inset-x-14 md:bottom-16 md:gap-5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-orange md:text-[11px]">{{ eyebrow }}</p>
            <h1
                class="font-serif leading-[1.02]"
                :class="size === 'full' ? 'text-[44px] md:max-w-[620px] md:text-[60px]' : 'text-[46px] md:text-[72px] md:leading-none'"
            >
                {{ title }}
            </h1>
            <p v-if="subtitle" class="text-[15px] leading-relaxed text-white/85 md:text-[17px]">{{ subtitle }}</p>
            <div v-if="$slots.default" class="mt-1 flex flex-wrap items-center gap-3 md:gap-7"><slot /></div>
        </div>

        <button
            v-if="videoUrl"
            type="button"
            class="absolute right-5 top-20 flex h-11 w-11 items-center justify-center rounded-full border border-white/60 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-orange md:bottom-16 md:right-14 md:top-auto"
            :aria-label="playing ? 'Jeda video latar' : 'Putar video latar'"
            @click="toggle"
        >
            <svg v-if="playing" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 6v12M15 6v12" /></svg>
            <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" /></svg>
        </button>
    </section>
</template>
