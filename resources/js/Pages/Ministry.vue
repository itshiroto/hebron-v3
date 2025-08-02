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
</script>

<template>
    <DefaultLayout>
        <section class="bg-blue-800 text-white pt-20 h-[35vh] mb-12 relative">
            <div class="max-w-screen-xl mx-auto h-full flex flex-col items-center justify-center p-8 relative z-10">
                <h1 class="text-5xl">Serve at our ministries</h1>
            </div>
        </section>
        <section class="max-w-screen-xl mx-auto mb-12">
            <h2 class="text-3xl mb-8">Lorem Ipsum Dolor Sit Amet</h2>
            <p class="font-sans tracking-tight leading-relaxed">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate perferendis laborum, quidem
                corporis, soluta adipisci, vitae est distinctio incidunt tempora et alias labore non voluptatum
                voluptatibus. Impedit fugit esse, officia quas voluptate temporibus consequatur beatae accusantium
                facilis architecto dignissimos nesciunt velit enim voluptatem veritatis pariatur amet repellendus
                laborum iure reprehenderit facere ab id recusandae! Alias quasi temporibus iusto voluptates inventore,
                at dolorem magni. Debitis cum enim dolorem, rerum iste cumque impedit laboriosam at laborum! Culpa,
                minima ad sit expedita molestias natus quis dolore velit nam veniam atque praesentium odio voluptatem,
                error eius libero tenetur saepe harum consequatur, voluptatum consequuntur ex. Facilis at aperiam optio
                nisi enim obcaecati rem, minus perferendis officiis, neque consequatur facere aliquam dolor! Consectetur
                quia sed doloribus perferendis reprehenderit, quis dolore, labore vel natus libero aperiam voluptates
                ullam temporibus rerum assumenda vero praesentium molestiae, expedita a. Accusamus aut, corporis
                incidunt beatae voluptas inventore quo repudiandae quam atque?
            </p>
        </section>
        <section class="max-w-screen-xl mx-auto mb-16">
            <h3 class="text-3xl mb-8">Where can I serve?</h3>
            <div class="grid grid-cols-2 gap-8">
                <Card v-for="n in 6" class="shadow-none">
                    <CardHeader>
                        <img src="https://placehold.co/600x400" class="aspect-video object-cover rounded-lg mb-4"
                            alt="">
                        <CardTitle class="text-2xl">Halo!</CardTitle>
                    </CardHeader>
                    <CardContent class="font-sans">
                        <p class="text-sm leading-relaxed">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                            Possimus
                            laboriosam
                            nesciunt
                            adipisci dolorum ab voluptatibus delectus consequatur! Odio tenetur obcaecati dolore, iure
                            impedit dignissimos dicta accusamus, voluptas perspiciatis libero ut qui quod? Esse
                            quibusdam, minima nisi excepturi ipsam corporis? Nobis quo asperiores necessitatibus
                            dolorem, impedit eaque animi ratione iusto aut consequatur</p>
                    </CardContent>
                    <CardFooter class="font-sans">
                        <Button variant="outline">Volunteer now!</Button>
                    </CardFooter>
                </Card>
            </div>
        </section>

        <section id="faq" class="max-w-screen-xl mx-auto mb-12">
            <h3 class="text-3xl mb-8">Frequently Asked Question</h3>
            <Accordion type="single" collapsible>
                <AccordionItem value="item-1">
                    <AccordionTrigger class="font-sans text-xl">Is it accessible?</AccordionTrigger>
                    <AccordionContent class="font-sans text-lg">
                        Yes. It adheres to the WAI-ARIA design pattern.
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </section>
    </DefaultLayout>
</template>
