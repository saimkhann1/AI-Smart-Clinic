<script setup >
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Backend se aane wala data (Props)
// 'const props =' hata diya gaya hai taake unused variable ka error na aaye
defineProps({
    appointments: Array,
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="$page.props.flash?.message" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.message }}
                </div>

                <div v-if="user.role === 'patient'" class="flex gap-4 mb-6">
                    <Link :href="route('appointments.create')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
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
                    <h3 class="text-lg font-bold mb-4">
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
                                <tr v-for="apt in appointments" :key="apt.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ apt.date }}</div>
                                        <div class="text-sm text-gray-500">{{ apt.time }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ user.role === 'doctor' ? apt.patient.name : 'Dr. ' + apt.doctor.name }}
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
                                        }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ apt.status }}
                                        </span>
                                    </td>
                                    <td v-if="user.role === 'doctor'" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div v-if="apt.status === 'pending'">
                                            <button @click="updateStatus(apt.id, 'confirmed')" class="text-green-600 hover:text-green-900 mr-3">Confirm</button>
                                            <button @click="updateStatus(apt.id, 'cancelled')" class="text-red-600 hover:text-red-900">Cancel</button>
                                        </div>
                                        <div v-else-if="apt.status === 'confirmed'">
                                            <button @click="updateStatus(apt.id, 'completed')" class="text-blue-600 hover:text-blue-900 font-bold">Mark Complete</button>
                                        </div>
                                        <div v-else class="text-gray-400">Locked</div>
                                    </td>
                                </tr>
                                <tr v-if="appointments.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No appointments found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>