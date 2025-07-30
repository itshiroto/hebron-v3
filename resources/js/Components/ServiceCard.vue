<template>
    <div class="relative rounded-xl overflow-hidden shadow-lg group">
        <!-- Gambar latar -->
        <img
            :src="image"
            alt="service"
            class="w-full h-96 object-cover transition-transform duration-500 group-hover:scale-105"
        />

        <!-- Overlay gelap -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Konten -->
        <div class="absolute inset-0 p-10 flex flex-col justify-end text-white">
            <!-- Logo atau Judul -->
            <div class="absolute top-10 left-10 mb-4">
                <div v-if="isLogo" class="h-12 mb-2">
                    <img :src="title" alt="logo" class="h-full w-auto" />
                </div>
                <h3
                    v-else
                    class="text-xl md:text-3xl font-bold drop-shadow-lg leading-tight"
                    v-html="title"
                ></h3>
            </div>

            <!-- Jadwal -->
            <div class="mb-2">
                <p class="text-lg font-medium opacity-90">Setiap Minggu</p>
                <p class="text-lg font-medium opacity-90">{{ waktu }}</p>
            </div>

            <!-- Deskripsi -->
            <p v-if="desc" class="text-lg opacity-80 leading-snug mt-2">
                {{ desc }}
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String, // Bisa berupa teks atau path logo
    isLogo: {
        type: Boolean,
        default: false,
    },
    desc: {
        type: String,
        default: "",
    },
    image: String,
    waktu: {
        type: String,
        default: "07.30 - 09.00 • 17.30 WIB",
    },
});
</script>
