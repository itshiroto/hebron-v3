<template>
    <div
        :class="[
            'flex flex-col',
            isReverse ? 'md:flex-row-reverse' : 'md:flex-row',
        ]"
    >
        <img
            :src="image"
            :alt="title"
            class="object-cover object-center w-full md:w-1/2"
        />
        <div class="flex flex-col justify-between p-8 gap-5 md:gap-0 md:p-16">
            <p class="text-3xl md:text-5xl font-playfair">{{ title }}</p>
            <p class="text-xl md:text-3xl font-playfair">
                {{ description }}
            </p>
            <PrimaryButton class="justify-center">Read More</PrimaryButton>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    image: {
        type: String,
        default: "/storage/images/pemandangan.jpg",
    },
    index: {
        type: Number,
        required: true,
    },
});

// Otomatis reverse untuk index ganjil (1, 3, 5, ...)
const isReverse = computed(() => props.index % 2 === 1);
</script>
