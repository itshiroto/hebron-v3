<template>
    <div class="grid md:grid-cols-2">
        <img :src="`/storage/${props.image}`" :alt="title" class="object-cover w-full h-full"
            :class="{ 'md:order-2': index % 2 !== 0 }" />
        <div class="flex flex-col justify-center px-12 py-8" :class="{ 'md:order-1': index % 2 !== 0 }">
            <p class="mb-4 text-3xl md:text-4xl font-bold">{{ title }}</p>
            <p class="mb-8 text-xl font-sans">{{ time }}</p>
            <p class="mb-8 md:text-lg font-sans tracking-tight leading-relaxed">
                {{ description }}
            </p>
            <a v-if="link" :href="link">
                <Button
                    class="font-sans w-full font-semibold uppercase bg-blue-600 hover:bg-blue-500 text-white tracking-tight rounded-full shadow-none"
                    size="lg">
                    Learn More
                </Button>
            </a>
        </div>
    </div>
</template>

<script setup>
import { Button } from "@/components/ui/button"

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    time: {
        type: String,
    },
    image: {
        type: String,
    },
    link: {
        type: String,
    },
    index: {
        type: Number,
        required: true,
    },
});
</script>
