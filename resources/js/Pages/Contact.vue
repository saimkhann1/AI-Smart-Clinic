<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

// Form ka Data
const form = ref({
    name: '',
    email: '',
    message: ''
});

const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Web3Forms par data bhejne ka function
const submitForm = async () => {
    isSubmitting.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    try {
        const response = await fetch('https://api.web3forms.com/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                // 💥 APNI WEB3FORMS ACCESS KEY YAHAN DAALEIN 💥
                access_key: '09d258f2-d720-415b-a983-655d6b75466a', 
                name: form.value.name,
                email: form.value.email,
                message: form.value.message,
                subject: 'New Patient Inquiry - Smart Clinic' 
            })
        });

        const result = await response.json();

        if (response.status === 200) {
            successMessage.value = 'Aapka message hamesha ke liye mehfooz ho gaya hai. Hum jald raabta karenge!';
            form.value.name = '';
            form.value.email = '';
            form.value.message = '';
        } else {
            errorMessage.value = result.message || 'Kuch ghalat ho gaya, dobara try karein.';
        }
    } catch (error) {
        errorMessage.value = 'Server se connect nahi ho paya. Apna internet check karein.';
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Head title="Contact Us - Smart Clinic" />

    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        
        <div class="max-w-6xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
            
            <div class="md:w-5/12 bg-indigo-600 text-white p-10 sm:p-12 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-indigo-500 opacity-50 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-blue-500 opacity-50 blur-2xl"></div>

                <div class="relative z-10">
                    <h2 class="text-3xl font-bold tracking-tight mb-2">Smart Clinic</h2>
                    <p class="text-indigo-100 text-sm leading-relaxed mb-8">
                        Humari behtareen medical team aapki khidmat ke liye 24/7 hazir hai. Appointment ya kisi bhi maloomat ke liye humse raabta karein.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 bg-indigo-500/30 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-indigo-200 font-medium">Call Us Now</p>
                                <p class="text-base font-semibold">+92 308 7536155</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 bg-indigo-500/30 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-indigo-200 font-medium">Email Address</p>
                                <p class="text-base font-semibold">saimkhannoffical@gmail.com</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 bg-indigo-500/30 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-indigo-200 font-medium">Clinic Location</p>
                                <p class="text-base font-semibold">123 Health Avenue, Medical District, Multan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-10 md:mt-0">
                    <Link :href="route('login')" class="inline-flex items-center text-sm font-medium text-indigo-200 hover:text-white transition-colors">
                        &larr; Back to Dashboard
                    </Link>
                </div>
            </div>

            <div class="md:w-7/12 p-10 sm:p-14 bg-white">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>

                <div v-if="successMessage" class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
                </div>

                <div v-if="errorMessage" class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="name" value="Full Name" class="text-gray-600 font-medium" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all bg-gray-50/50"
                                v-model="form.name"
                                required
                                placeholder="E.g. Saim Khan "
                            />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email Address" class="text-gray-600 font-medium" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all bg-gray-50/50"
                                v-model="form.email"
                                required
                                placeholder="saimkhan@example.com"
                            />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="message" value="How can we help you?" class="text-gray-600 font-medium" />
                        <textarea
                            id="message"
                            v-model="form.message"
                            rows="5"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all bg-gray-50/50 resize-none p-3"
                            required
                            placeholder="Type your message here..."
                        ></textarea>
                    </div>

                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full sm:w-auto px-8 py-3.5 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center"
                            :disabled="isSubmitting"
                        >
                            <span v-if="isSubmitting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sending Request...
                            </span>
                            <span v-else>Send Message</span>
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</template>