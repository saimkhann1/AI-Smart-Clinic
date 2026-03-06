<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Props mein 'medical_record' add kiya hai taake purana data pre-fill ho sake
const props = defineProps({ 
    doctors: Array,
    medical_record: Object 
});

const form = useForm({
    doctor_id: '',
    date: '',
    time: '',
    // Note: Database column ka naam 'problem' hai, isliye yahan bhi 'problem' use kiya
    problem: '', 
    
    // Medical Record Fields (Agar purana data hai to wo show hoga)
    blood_group: props.medical_record?.blood_group || '',
    allergies: props.medical_record?.allergies || '',
    chronic_diseases: props.medical_record?.chronic_diseases || '',
    current_medications: props.medical_record?.current_medications || '',
});

const submit = () => {
    form.post(route('appointments.store'));
};
</script>

<template>
    <Head title="Book Appointment" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book an Appointment</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <form @submit.prevent="submit">
                        
                        <div class="mb-6 border-b pb-4">
                            <h3 class="text-lg font-bold text-gray-700 mb-4">Appointment Details</h3>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Choose Doctor</label>
                                <select v-model="form.doctor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="" disabled>Select a Doctor</option>
                                    <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
                                        Dr. {{ doc.name }} ({{ doc.doctor_profile?.specialization }})
                                    </option>
                                </select>
                                <div v-if="form.errors.doctor_id" class="text-red-500 text-xs mt-1">{{ form.errors.doctor_id }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" v-model="form.date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                                    <div v-if="form.errors.date" class="text-red-500 text-xs mt-1">{{ form.errors.date }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Preferred Time</label>
                                    <input type="time" v-model="form.time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                                    <div v-if="form.errors.time" class="text-red-500 text-xs mt-1">{{ form.errors.time }}</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Reason for Visit (Problem)</label>
                                <textarea v-model="form.problem" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Briefly describe your issue (e.g. High Fever)..." required></textarea>
                                <div v-if="form.errors.problem" class="text-red-500 text-xs mt-1">{{ form.errors.problem }}</div>
                            </div>
                        </div>

                        <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700 mb-2">Medical History (Optional)</h3>
                            <p class="text-xs text-gray-500 mb-4">Update your medical details so the doctor knows your history.</p>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Blood Group</label>
                                    <select v-model="form.blood_group" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
                                    <label class="block text-sm font-medium text-gray-700">Allergies</label>
                                    <input type="text" v-model="form.allergies" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g. Peanuts, Dust" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Chronic Diseases</label>
                                <input type="text" v-model="form.chronic_diseases" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g. Diabetes, Asthma" />
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Current Medications</label>
                                <input type="text" v-model="form.current_medications" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="List any medicines you are taking" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-bold">
                                {{ form.processing ? 'Booking...' : 'Confirm Appointment' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>