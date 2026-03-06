<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

// 1. Backend (Controller) se aane wala data receive kar rahe hain
const props = defineProps({
    record: Object,      // Ye permanent profile hai
    appointments: Array  // Ye saari visit history hai
});

// 2. Form ko 'record' ke data se pre-fill kar rahe hain
const form = useForm({
    blood_group: props.record?.blood_group || '',
    allergies: props.record?.allergies || '',
    chronic_diseases: props.record?.chronic_diseases || '',
    current_medications: props.record?.current_medications || '',
});

// 3. Form submit function
const submitProfile = () => {
    form.post(route('patient.record.update'), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Medical Record" />

    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">My Appointments & Medical History</h2>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h3 class="text-lg font-bold mb-4 border-b pb-2">Appointment History</h3>

            <div v-if="!appointments || appointments.length === 0" class="text-center text-gray-500 py-6 bg-gray-50 rounded">
                <p>No appointment history found.</p>
                <span class="text-sm">When you book an appointment via AI, it will show up here.</span>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-sm">
                            <th class="p-3 border-b rounded-tl-lg">Date & Time</th>
                            <th class="p-3 border-b">Doctor</th>
                            <th class="p-3 border-b">Symptoms (Issue)</th>
                            <th class="p-3 border-b rounded-tr-lg">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="apt in appointments" :key="apt.id" class="hover:bg-gray-50 border-b last:border-0 text-sm">
                            <td class="p-3">
                                <span class="font-semibold block">{{ apt.date }}</span>
                                <span class="text-gray-500 text-xs">{{ apt.time }}</span>
                            </td>
                            <td class="p-3">Dr. {{ apt.doctor?.name || 'N/A' }}</td>
                            <td class="p-3 text-gray-600">{{ apt.problem_description || 'N/A' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs text-white rounded-full" 
                                      :class="apt.status === 'pending' ? 'bg-yellow-500' : 'bg-green-500'">
                                    {{ apt.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-bold mb-2 border-b pb-2">Medical Profile (General Info)</h3>
            <p class="text-sm text-gray-500 mb-6">You can update your permanent medical record here. This helps doctors know your history.</p>

            <div v-if="form.recentlySuccessful" class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                ✅ Medical Record Updated Successfully!
            </div>

            <form @submit.prevent="submitProfile">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Blood Group</label>
                        <select v-model="form.blood_group" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                        <input type="text" v-model="form.allergies" placeholder="e.g. Dust, Peanuts" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chronic Diseases</label>
                    <textarea v-model="form.chronic_diseases" rows="2" placeholder="e.g. Diabetes, Hypertension" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Medications</label>
                    <textarea v-model="form.current_medications" rows="2" placeholder="List medicines you are currently taking" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="form.processing" 
                            class="bg-gray-800 text-white px-6 py-2 rounded hover:bg-gray-700 disabled:opacity-50 transition">
                        {{ form.processing ? 'Saving...' : 'UPDATE MEDICAL PROFILE' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>