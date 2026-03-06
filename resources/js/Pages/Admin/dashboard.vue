<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Controller se data receive kar rahe hain
defineProps({ stats: Object, recent_appointments: Array, doctors: Array });
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="$page.props.flash.message" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
                    {{ $page.props.flash.message }}
                </div>

                <div class="flex justify-end mb-6">
                    <Link :href="route('admin.create-doctor')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center gap-2 transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Doctor
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Doctors</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total_doctors }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center border-l-4 border-green-500">
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Patients</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total_patients }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center border-l-4 border-yellow-500">
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Appointments</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total_appointments }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center border-l-4 border-purple-500">
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Revenue</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">Rs. {{ stats.total_revenue }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Available Doctors</h3>
                            <Link :href="route('admin.create-doctor')" class="text-sm text-blue-600 hover:underline">
                                + Add Another
                            </Link>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Specialization</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="doc in doctors" :key="doc.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            Dr. {{ doc.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ doc.doctor_profile ? doc.doctor_profile.specialization : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ doc.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                            Rs. {{ doc.doctor_profile ? doc.doctor_profile.consultation_fee : '0' }}
                                        </td>
                                    </tr>
                                    <tr v-if="doctors.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No doctors added yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Appointments</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medical Issue</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="apt in recent_appointments" :key="apt.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ apt.date }} <span class="text-gray-500 text-xs">({{ apt.time }})</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Dr. {{ apt.doctor.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ apt.patient.name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <span class="bg-gray-100 px-2 py-1 rounded text-xs">
                                                {{ apt.problem || 'Not specified' }}
                                            </span>
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
                                    </tr>
                                    <tr v-if="recent_appointments.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No appointments found yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>