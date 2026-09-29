<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import DivisionTabs from '@/Components/site/DivisionTabs.vue';
import type { DivisionItem } from '@/types/site';

defineProps<{ data: DivisionItem[]; hero: { title?: string; image?: string } | [] }>();
</script>

<template>
    <Head :title="(!Array.isArray($page.props.meta) && $page.props.meta?.title) || 'Divisi'" />
    <DefaultLayout>
        <PageHero
            :image="Array.isArray(hero) ? null : hero?.image"
            eyebrow="Divisi"
            :title="(!Array.isArray(hero) && hero?.title) || 'Ibadah untuk setiap generasi'"
        />
        <section class="container-site pb-[72px] pt-10 lg:pb-36 lg:pt-28">
            <DivisionTabs :divisions="data" />
        </section>
    </DefaultLayout>
</template>
