<script setup lang="ts">
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { getReadingTime } from '@/utils/readingTime';
import { format } from 'date-fns';

const props = defineProps(['data'])
</script>

<template>
    <DefaultLayout>
        <section class="bg-blue-800 text-white pt-20 h-[35vh] mb-16 relative">
            <div class="max-w-screen-xl mx-auto h-full flex flex-col items-center justify-center p-8 relative z-10">
                <h1 class="text-5xl">Devotions</h1>
            </div>
        </section>
        <section id="list" class="grid grid-cols-1 max-w-screen-xl mx-auto px-8 gap-4">
            <a v-for="posts in props.data" :key="posts.id" :href="`/devotion/${posts.slug}`"
                class="transition-all group pb-8 last:border-none border-b">
                <div class="flex justify-between items-start gap-16 mb-8">
                    <div class="flex flex-col gap-2">
                        <h2 class="font-medium text-3xl leading-normal group-hover:underline">{{ posts.title }}</h2>
                        <p class="font-sans tracking-tight leading-relaxed text-gray-800 text-justify">
                            {{ posts.snippet }}
                        </p>
                    </div>
                    <img v-if="posts.image" class="h-48 aspect-video" :src="`/storage/${posts.image}`" />
                </div>
                <div class="flex justify-between uppercase font-sans text-xs tracking-tight text-gray-600">
                    <h3 class="">{{ format(new Date(posts.updated_at), 'dd MMMM yyyy') }}</h3>
                    <p class="">{{ getReadingTime(posts.content) }} minutes reading</p>
                </div>
            </a>
        </section>
    </DefaultLayout>
</template>
