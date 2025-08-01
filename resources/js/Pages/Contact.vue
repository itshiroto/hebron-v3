<script setup lang="ts">
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
</script>

<<template>
    <DefaultLayout>
        <div class="bg-blue-800 min-h-screen px-8 pt-20">
            <div class="text-white text-center my-16">
                <h1 class="text-5xl mb-4">Got Question? We’ve got answers.</h1>
                <h3 class="text-2xl font-sans tracking-tight">Contact us for support or inquiries.</h3>
            </div>
            <div class="max-w-screen-xl mx-auto p-6 bg-white rounded-lg w-full shadow-xl grid grid-cols-3 gap-8">
                <div class="">
                    <h4 class="font-bold text-2xl">Our Contact</h4>
                    <hr class="mt-4 mb-8 border-gray-600 ">
                    <div v-for="n in 3"
                        class="border-b last:border-none mb-4 pb-4 grid grid-cols-3 font-sans items-center">
                        <p class="text-sm font-bold text-gray-600 uppercase leading-none">Phone</p>
                        <p class="col-span-2 leading-none text-right">081296867118</p>
                    </div>
                </div>
                <iframe class="col-span-2 w-full h-full min-h-[480px]"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9409.156729619113!2d106.62013527573178!3d-6.250669861203338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fc76ad63a58b%3A0xb11a5c84cfc0f8db!2sGPdI%20Hebron%20Gading%20Serpong!5e1!3m2!1sen!2sid!4v1754075220198!5m2!1sen!2sid"
                    style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </DefaultLayout>
</template>
