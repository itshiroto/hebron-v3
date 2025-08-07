<script setup lang="ts">
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps<{
    hero: {
        title: string;
        subtitle: string;
        image?: string; // Added image property
    };
    contactList: Array<{
        title: string;
        desc: string;
        link?: string;
    }>;
}>();


const page = usePage(); // Initialize usePage
const generalSettings = computed(() => page.props.generalSettings || {});
const churchAddress = computed(() => generalSettings.value.church_address || 'Kompleks Pasar Modern Paramount, <br>Jl. Boulevard Raya Gading Serpong Blok L39,');
</script>

<template>
    <DefaultLayout>
        <div class="min-h-screen px-8 py-20 bg-cover bg-center"
            :style="hero.image ? { backgroundImage: `url(${hero.image})` } : { backgroundColor: '#2b6cb0' }">
            <div class="text-white text-center my-16">
                <h1 class="text-5xl mb-4">{{ hero.title }}</h1>
                <h3 class="text-2xl font-sans tracking-tight">{{ hero.subtitle }}</h3>
            </div>
            <div class="max-w-screen-xl mx-auto p-6 bg-white rounded-lg w-full shadow-xl grid lg:grid-cols-3 gap-8">
                <div class="">
                    <h4 class="font-bold text-2xl">Our Contact</h4>
                    <hr class="mt-4 mb-8 border-gray-600 ">
                    <div v-for="(item, index) in contactList" :key="index"
                        class="border-b last:border-none mb-4 pb-4 grid grid-cols-3 font-sans items-center">
                        <p class="text-sm font-bold text-gray-600 uppercase leading-none">{{ item.title }}</p>
                        <div class="col-span-2 leading-none text-right">
                            <a v-if="item.link" :href="item.link" target="_blank"
                                class="text-blue-600 hover:underline text-sm" v-html="item.desc">
                            </a>
                            <div v-else v-html="item.desc"></div>
                        </div>
                    </div>
                    <h4 class="mt-8 font-bold text-2xl">Our Address</h4>
                    <hr class="mt-4 mb-4 border-gray-600 ">
                    <div class="leading-relaxed" v-html="churchAddress" />
                </div>
                <iframe class="lg:col-span-2 w-full h-full min-h-[480px]"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9409.156729619113!2d106.62013527573178!3d-6.250669861203338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fc76ad63a58b%3A0xb11a5c84cfc0f8db!2sGPdI%20Hebron%20Gading%20Serpong!5e1!3m2!1sen!2sid!4v1754075220198!5m2!1sen!2sid"
                    style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </DefaultLayout>
</template>
