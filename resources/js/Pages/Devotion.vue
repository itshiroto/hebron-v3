<script setup lang="ts">
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { computed } from 'vue';
import { format } from 'date-fns';
import { getReadingTime } from '@/utils/readingTime';

// TODO: type declaration
const props = defineProps(['data'])

const formattedDate = computed(() => {
    if (props.data && props.data.updated_at) {
        return format(new Date(props.data.updated_at), 'dd MMMM yyyy');
    }
    return '';
});

const readTime = getReadingTime(props.data.content)

const sectionStyle = computed(() => {
    if (props.data.image) {
        return {
            backgroundImage: `url(/storage/${props.data.image})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
        };
    }
    return {};
});
</script>

<template>
    <DefaultLayout>
        <section class="bg-black text-white pt-20 h-[50vh] mb-12 relative" :style="sectionStyle">
            <div class="absolute inset-0 bg-black opacity-50"></div>
            <div class="max-w-screen-xl mx-auto h-full flex flex-col items-center justify-between p-8 relative z-10">
                <h3 class="text-blue-500 font-sans font-bold text-xl uppercase mb-4">{{ props.data.category }}</h3>
                <h1 class="text-5xl">{{ props.data.title }}</h1>
                <h4 class="font-sans text-sm text-gray-400 tracking-tight">
                    {{
                        readTime === 0 ? "less than a minute" :
                            readTime === 1 ? "1 minute read" :
                                `${readTime} minutes read`
                    }} • {{ formattedDate }}</h4>
            </div>
        </section>
        <article id="content" class="max-w-screen-lg mx-auto px-8 prose-lg font-sans tracking-tight mb-12"
            v-html="props.data.content" />
    </DefaultLayout>
</template>
