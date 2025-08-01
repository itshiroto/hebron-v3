<script setup lang="ts">
import Navbar from '@/Components/Navbar.vue';
</script>

<template>
    <div>
        <div class="min-h-screen">
            <Navbar />
            <main class="font-serif">
                <slot />
            </main>
        </div>
    </div>
</template>
