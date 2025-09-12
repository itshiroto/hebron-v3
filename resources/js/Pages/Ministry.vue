<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head } from '@inertiajs/vue3';

import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'

import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { computed } from 'vue';
import { MinistryPageSections, HeroSectionData, IntroSectionData, ServeSectionData, FAQSectionData } from '@/types/ministry-page'; // Import all necessary types

const props = defineProps<{
    sections: MinistryPageSections; // Use the imported type
}>();

const heroSection = computed<HeroSectionData>(() => props.sections.hero || { title: '', image: '' });
const introSection = computed<IntroSectionData>(() => props.sections.intro || { title: '', desc: '' });
const serveSection = computed<ServeSectionData>(() => props.sections.serve || { title: '', ministryList: [] });
const faqSection = computed<FAQSectionData>(() => props.sections.faq || { title: '', questionsList: [] });

</script>

<template>

    <Head title="Ministry" />
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="bg-blue-800 text-white pt-20 h-[35vh] mb-12 relative"
            :style="{ backgroundImage: heroSection.image ? `url(/storage/${heroSection.image})` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <div class="max-w-screen-xl mx-auto h-full flex flex-col items-center justify-center p-4 sm:p-8 relative z-10">
                <h1 class="text-3xl sm:text-5xl text-center">{{ heroSection.title || 'Serve at our ministries' }}</h1>
            </div>
        </section>

        <!-- Intro Section -->
        <section class="max-w-screen-xl mx-auto px-8 mb-12">
            <h2 class="text-4xl mb-6 sm:mb-8 ">{{ introSection.title || 'Lorem Ipsum Dolor Sit Amet' }}</h2>
            <div class="text-lg font-sans tracking-tight leading-relaxed"
                v-html="introSection.desc || `Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate perferendis laborum, quidem corporis, soluta adipisci, vitae est distinctio incidunt tempora et alias labore non voluptatum voluptatibus. Impedit fugit esse, officia quas voluptate temporibus voluptatibus. Impedit fugit esse, officia quas voluptate temporibus consequatur beatae accusantium facilis architecto dignissimos nesciunt velit enim voluptatem veritatis pariatur amet repellendus laborum iure reprehenderit facere ab id recusandae! Alias quasi temporibus iusto voluptates inventore, at dolorem magni. Debitis cum enim dolorem, rerum iste cumque impedit laboriosam at laborum! Culpa, minima ad sit expedita molestias natus quis dolore velit nam veniam atque praesentium odio voluptatem, error eius libero tenetur saepe harum consequatur, voluptatum consequuntur ex. Facilis at aperiam optio nisi enim obcaecati rem, minus perferendis officiis, neque consequatur facere aliquam dolor! Consectetur quia sed doloribus perferendis reprehenderit, quis dolore, labore vel natus libero aperiam voluptates ullam temporibus rerum assumenda vero praesentius molestiae, expedita a. Accusamus aut, corporis incidunt beatae voluptas inventore quo repudiandae quam atque?`">
            </div>
        </section>

        <!-- Serve Section -->
        <section class="max-w-screen-xl mx-auto px-8 mb-16">
            <h3 class="text-3xl mb-6 sm:mb-8">{{ serveSection.title || 'Where can I serve?' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                <Card v-for="(ministry, index) in serveSection.ministryList" :key="index" class="shadow-none flex flex-col transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <CardHeader class="p-4 sm:p-6">
                        <img :src="ministry.image ? `/storage/${ministry.image}` : 'https://placehold.co/600x400'"
                            class="aspect-video object-cover rounded-lg mb-3 sm:mb-4" :alt="ministry.title">
                        <CardTitle class="text-2xl sm:text-3xl">{{ ministry.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="font-sans p-4 sm:p-6 !pt-0 flex-grow">
                        <div class="leading-relaxed" v-html="ministry.desc"></div>
                    </CardContent>
                    <CardFooter class="font-sans p-4 sm:p-6 pt-0">
                        <a :href="ministry.link" target="_blank">
                            <!-- TODO: sementara pake hover class dulu soalnya shadcnnya rusak -->
                            <Button variant="outline" class="hover:bg-gray-100 text-sm sm:text-base">Volunteer now!</Button>
                        </a>
                    </CardFooter>
                </Card>
                <Card v-if="!serveSection.ministryList || serveSection.ministryList.length === 0" class="shadow-none flex flex-col transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <CardHeader class="p-4 sm:p-6">
                        <img src="https://placehold.co/600x400" class="aspect-video object-cover rounded-lg mb-3 sm:mb-4"
                            alt="">
                        <CardTitle class="text-2xl sm:text-2xl">No Ministries Available</CardTitle>
                    </CardHeader>
                    <CardContent class="font-sans p-4 sm:p-6 flex-grow">
                        <p class="leading-relaxed">Please add ministries from the admin panel.</p>
                    </CardContent>
                </Card>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="max-w-screen-xl mx-auto px-4 sm:px-0 mb-12" v-if="faqSection.questionsList && faqSection.questionsList.length > 0">
            <h3 class="text-2xl sm:text-3xl mb-6 sm:mb-8 text-center sm:text-left">{{ faqSection.title || 'Frequently Asked Question' }}</h3>
            <Accordion type="single" collapsible>
                <AccordionItem v-for="(qa, index) in faqSection.questionsList" :key="index" :value="`item-${index}`">
                    <AccordionTrigger class="font-sans text-lg sm:text-xl">{{ qa.question }}</AccordionTrigger>
                    <AccordionContent class="font-sans text-base sm:text-lg" v-html="qa.answer"></AccordionContent>
                </AccordionItem>
            </Accordion>
        </section>
    </DefaultLayout>
</template>
