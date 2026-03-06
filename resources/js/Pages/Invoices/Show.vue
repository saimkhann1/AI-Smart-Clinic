<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import html2pdf from 'html2pdf.js/dist/html2pdf.min.js';

const props = defineProps({ invoice: Object });

// Invoice ke section ko target karne ke liye ref
const invoiceRef = ref(null);

// PDF Download Function
const downloadPDF = () => {
    const element = invoiceRef.value;
    
    const opt = {
        margin:       0, // Side margins
        filename:     `Invoice-${props.invoice.invoice_number}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true }, // Scale 2 se text HD aayega
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
};
</script>

<template>
    <Head :title="'Invoice #' + invoice.invoice_number" />

    <div class="min-h-screen bg-gray-100 p-8 flex flex-col items-center">
        
        <div class="w-[210mm] flex justify-between items-center mb-6 print:hidden">
            <Link :href="route('invoices.index')" class="text-gray-600 font-bold hover:text-gray-900 flex items-center gap-2">
                &larr; Back to List
            </Link>
            
            <button 
                @click="downloadPDF" 
                class="bg-gray-900 text-white px-5 py-2 rounded shadow hover:bg-gray-800 transition font-semibold flex items-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
            </button>
        </div>

        <div ref="invoiceRef" class="bg-white w-[210mm] min-h-[297mm] p-12 shadow-xl relative text-gray-800">
            
            <div class="flex justify-between items-start border-b border-gray-300 pb-8 mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-blue-800 tracking-tight uppercase">Smart Clinic</h1>
                    <p class="text-sm text-gray-500 mt-1">Excellence in Healthcare</p>
                    <div class="text-xs text-gray-400 mt-2 space-y-1">
                        <p>123 Medical Road, Multan, Pakistan</p>
                        <p>Ph: +92 300 1234567 | info@smartclinic.com</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-semibold text-gray-700 uppercase tracking-widest">Invoice</h2>
                    <p class="text-gray-500 font-mono text-lg mt-1">#{{ invoice.invoice_number }}</p>
                    <p class="text-sm text-gray-500 mt-1">Date: {{ new Date(invoice.created_at).toLocaleDateString() }}</p>
                </div>
            </div>

            <div class="flex justify-between mb-10 bg-gray-50 p-6 rounded border border-gray-100">
                <div>
                    <h3 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Billed To (Patient)</h3>
                    <p class="font-bold text-lg text-gray-900">{{ invoice.appointment.patient?.name }}</p>
                    <p class="text-gray-600 text-sm">{{ invoice.appointment.patient?.email }}</p>
                </div>
                <div class="text-right">
                    <h3 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Consultant (Doctor)</h3>
                    <p class="font-bold text-lg text-gray-900">Dr. {{ invoice.appointment.doctor?.name }}</p>
                    <p class="text-blue-600 font-medium text-sm">
                        {{ invoice.appointment.doctor?.doctor_profile?.specialization || 'Specialist' }}
                    </p>
                </div>
            </div>

            <table class="w-full mb-8 border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left p-4 font-bold text-gray-600 border-b border-gray-200 uppercase text-xs tracking-wider">Description</th>
                        <th class="text-right p-4 font-bold text-gray-600 border-b border-gray-200 uppercase text-xs tracking-wider">Amount (PKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-4 border-b border-gray-100">
                            <strong class="text-gray-800">Medical Consultation Fee</strong>
                            <div class="text-xs text-gray-500 mt-1">
                                Appointment on {{ invoice.appointment.date }} at {{ invoice.appointment.time }}
                            </div>
                        </td>
                        <td class="text-right p-4 border-b border-gray-100 font-medium text-gray-800">
                            {{ invoice.amount }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-end mt-4">
                <div class="w-1/2">
                    <div class="flex justify-between py-2 text-gray-500">
                        <span>Subtotal</span>
                        <span>{{ invoice.amount }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-t-2 border-gray-800 mt-2">
                        <span class="font-bold text-2xl text-gray-900">Total</span>
                        <span class="font-bold text-2xl text-blue-800">Rs. {{ invoice.amount }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <span v-if="invoice.payment_status === 'paid'" class="text-green-600 font-bold border-2 border-green-600 px-6 py-2 rounded uppercase text-lg inline-block transform -rotate-3 opacity-90">
                    PAID
                </span>
                <span v-else class="text-red-600 font-bold border-2 border-red-600 px-6 py-2 rounded uppercase text-lg inline-block transform -rotate-3 opacity-90">
                    UNPAID
                </span>
            </div>

            <div class="absolute bottom-12 left-12 right-12 text-center border-t pt-6">
                <p class="text-blue-800 font-bold text-sm">Thank you for choosing Smart Clinic!</p>
                <p class="text-gray-400 text-xs mt-1">This is a system generated invoice.</p>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Google Font Import for clean look in PDF */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');

div {
    font-family: 'Inter', sans-serif;
}
</style>