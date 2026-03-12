<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Backend se aane wala data (Props)
defineProps({
    appointments: {
        type: Array,
        default: () => []
    },
    user: Object,
    auth: Object
});

// Doctor Action Form (Status Update karne ke liye)
const form = useForm({
    status: '',
    doctor_notes: ''
});

const updateStatus = (id, status) => {
    if(confirm('Are you sure you want to ' + status + ' this appointment?')) {
        form.status = status;
        form.put(route('appointments.update', id), {
            preserveScroll: true, // Page ko wahin rok kar rakhega
            onSuccess: () => form.reset()
        });
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ user.role === 'doctor' ? 'Doctor Dashboard' : 'Patient Dashboard' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div v-if="$page.props.flash?.message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.message }}
                </div>

                <div v-if="user.role === 'patient'" class="flex flex-wrap gap-4">
                    <Link :href="route('appointments.create')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">
                        + Book New Appointment
                    </Link>
                 <Link :href="route('patient.record.show')" class="bg-purple-600 text-white px-4 py-2 rounded shadow hover:bg-purple-700">
                        My Medical History
                    </Link>
                    <Link :href="route('invoices.index')" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700">
                        My Invoices
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">
                        {{ user.role === 'doctor' ? 'Upcoming Appointments' : 'My Appointments' }}
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ user.role === 'doctor' ? 'Patient Name' : 'Doctor Name' }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Problem</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th v-if="user.role === 'doctor'" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="apt in appointments" :key="apt.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ apt.date }}</div>
                                        <div class="text-sm text-gray-500">{{ apt.time }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ user.role === 'doctor' ? (apt.patient?.name || 'Unknown Patient') : ('Dr. ' + (apt.doctor?.name || 'Unknown')) }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ apt.problem_description || 'N/A' }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="{
                                            'bg-yellow-100 text-yellow-800': apt.status === 'pending',
                                            'bg-green-100 text-green-800': apt.status === 'confirmed',
                                            'bg-blue-100 text-blue-800': apt.status === 'completed',
                                            'bg-red-100 text-red-800': apt.status === 'cancelled',
                                        }" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ apt.status.charAt(0).toUpperCase() + apt.status.slice(1) }}
                                        </span>
                                    </td>
                                    
                                    <td v-if="user.role === 'doctor'" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div v-if="apt.status === 'pending'">
                                            <button @click="updateStatus(apt.id, 'confirmed')" class="text-green-600 hover:text-green-900 mr-4 font-bold transition">Confirm</button>
                                            <button @click="updateStatus(apt.id, 'cancelled')" class="text-red-600 hover:text-red-900 font-bold transition">Cancel</button>
                                        </div>
                                        <div v-else-if="apt.status === 'confirmed'">
                                            <button @click="updateStatus(apt.id, 'completed')" class="text-blue-600 hover:text-blue-900 font-bold bg-blue-50 px-3 py-1 rounded transition">Mark Complete</button>
                                        </div>
                                        <div v-else class="text-gray-400 italic">Locked</div>
                                    </td>
                                </tr>
                                
                                <tr v-if="appointments.length === 0">
                                    <td :colspan="user.role === 'doctor' ? 5 : 4" class="px-6 py-8 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium">No appointments found.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Need Help or Have a Question?</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Agar aapko clinic administration se koi baat karni hai ya appointment ki detail chahiye, to humse raabta karein.
                        </p>
                    </div>
                    
                    <div class="mt-4 sm:mt-0">
                        <Link :href="route('contact')">
                            <PrimaryButton class="bg-indigo-600 hover:bg-indigo-700 transition">
                                Go to Contact Page
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>