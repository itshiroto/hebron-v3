<script setup lang="ts">
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
</script>

<template>
    <div>
        <div class="min-h-screen">
            <Navbar />
            <main class="font-serif min-h-screen">
                <slot />
            </main>
            <Footer />
        </div>
    </div>
</template>
