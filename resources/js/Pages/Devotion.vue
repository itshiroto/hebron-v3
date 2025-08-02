<script setup lang="ts">
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { computed } from 'vue';
import { format } from 'date-fns';
import { getReadingTime } from '@/utils/readingTime';
import { Head } from '@inertiajs/vue3';

// TODO: type declaration
const props = defineProps(['data'])

const formattedDate = computed(() => {
    if (props.data && props.data.updated_at) {
        return format(new Date(props.data.updated_at), 'dd MMMM yyyy');
    }
    return '';
});

const readTime = getReadingTime(props.data.content)
</script>

<template>

    <Head :title="props.data.title" />
    <DefaultLayout>
        <section class="bg-black text-white pt-20 h-[60vh] mb-12 relative">
            <div class="absolute inset-0 opacity-30" :style="{
                backgroundImage: `url(/storage/${props.data.image})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
            }"></div>
            <div class="max-w-screen-xl mx-auto h-full flex flex-col items-center justify-between p-8 relative z-10">
                <h3 class="text-blue-500 font-sans font-bold text-xl uppercase mb-4">{{ props.data.category }}</h3>
                <h1 class="text-5xl">{{ props.data.title }}</h1>
                <h4 class="font-sans text-sm text-gray-200 tracking-tight">
                    {{
                        readTime === 0 ? "less than a minute" :
                            readTime === 1 ? "1 minute read" :
                                `${readTime} minutes read`
                    }} • {{ formattedDate }}</h4>
            </div>
        </section>
        <article id="content" class="min-h-screen max-w-screen-lg mx-auto px-8 prose-lg font-sans tracking-tight mb-12"
            v-html="props.data.content" />
    </DefaultLayout>
</template>
