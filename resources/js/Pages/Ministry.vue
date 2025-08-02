<script setup lang="ts">
import { Button } from '@/components/ui/button';

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
    <DefaultLayout>
        <!-- Hero Section -->
        <section class="bg-blue-800 text-white pt-20 h-[35vh] mb-12 relative"
            :style="{ backgroundImage: heroSection.image ? `url(/storage/${heroSection.image})` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }">
            <div class="max-w-screen-xl mx-auto h-full flex flex-col items-center justify-center p-8 relative z-10">
                <h1 class="text-5xl">{{ heroSection.title || 'Serve at our ministries' }}</h1>
            </div>
        </section>

        <!-- Intro Section -->
        <section class="max-w-screen-xl mx-auto mb-12">
            <h2 class="text-3xl mb-8">{{ introSection.title || 'Lorem Ipsum Dolor Sit Amet' }}</h2>
            <div class="font-sans tracking-tight leading-relaxed"
                v-html="introSection.desc || `Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate perferendis laborum, quidem corporis, soluta adipisci, vitae est distinctio incidunt tempora et alias labore non voluptatum voluptatibus. Impedit fugit esse, officia quas voluptate temporibus voluptatibus. Impedit fugit esse, officia quas voluptate temporibus consequatur beatae accusantium facilis architecto dignissimos nesciunt velit enim voluptatem veritatis pariatur amet repellendus laborum iure reprehenderit facere ab id recusandae! Alias quasi temporibus iusto voluptates inventore, at dolorem magni. Debitis cum enim dolorem, rerum iste cumque impedit laboriosam at laborum! Culpa, minima ad sit expedita molestias natus quis dolore velit nam veniam atque praesentium odio voluptatem, error eius libero tenetur saepe harum consequatur, voluptatum consequuntur ex. Facilis at aperiam optio nisi enim obcaecati rem, minus perferendis officiis, neque consequatur facere aliquam dolor! Consectetur quia sed doloribus perferendis reprehenderit, quis dolore, labore vel natus libero aperiam voluptates ullam temporibus rerum assumenda vero praesentius molestiae, expedita a. Accusamus aut, corporis incidunt beatae voluptas inventore quo repudiandae quam atque?`">
            </div>
        </section>

        <!-- Serve Section -->
        <section class="max-w-screen-xl mx-auto mb-16">
            <h3 class="text-3xl mb-8">{{ serveSection.title || 'Where can I serve?' }}</h3>
            <div class="grid grid-cols-2 gap-8">
                <Card v-for="(ministry, index) in serveSection.ministryList" :key="index" class="shadow-none">
                    <CardHeader>
                        <img :src="ministry.image ? `/storage/${ministry.image}` : 'https://placehold.co/600x400'"
                            class="aspect-video object-cover rounded-lg mb-4" :alt="ministry.title">
                        <CardTitle class="text-2xl">{{ ministry.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="font-sans">
                        <div class="text-sm leading-relaxed" v-html="ministry.desc"></div>
                    </CardContent>
                    <CardFooter class="font-sans">
                        <a :href="ministry.link" target="_blank">
                            <!-- TODO: sementara pake hover class dulu soalnya shadcnnya rusak -->
                            <Button variant="outline" class="hover:bg-gray-100">Volunteer now!</Button>
                        </a>
                    </CardFooter>
                </Card>
                <Card v-if="!serveSection.ministryList || serveSection.ministryList.length === 0" class="shadow-none">
                    <CardHeader>
                        <img src="https://placehold.co/600x400" class="aspect-video object-cover rounded-lg mb-4"
                            alt="">
                        <CardTitle class="text-2xl">No Ministries Available</CardTitle>
                    </CardHeader>
                    <CardContent class="font-sans">
                        <p class="text-sm leading-relaxed">Please add ministries from the admin panel.</p>
                    </CardContent>
                </Card>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="max-w-screen-xl mx-auto mb-12">
            <h3 class="text-3xl mb-8">{{ faqSection.title || 'Frequently Asked Question' }}</h3>
            <Accordion type="single" collapsible v-if="faqSection.questionsList && faqSection.questionsList.length > 0">
                <AccordionItem v-for="(qa, index) in faqSection.questionsList" :key="index" :value="`item-${index}`">
                    <AccordionTrigger class="font-sans text-xl">{{ qa.question }}</AccordionTrigger>
                    <AccordionContent class="font-sans text-lg" v-html="qa.answer"></AccordionContent>
                </AccordionItem>
            </Accordion>
            <p v-else class="font-sans text-lg">No FAQs available. Please add some from the admin panel.</p>
        </section>
    </DefaultLayout>
</template>
