<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    specialization: '',
    consultation_fee: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('admin.store-doctor'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Add New Doctor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Register New Doctor</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <div class="mb-4">
                        <Link :href="route('admin.dashboard')" class="text-blue-600 hover:text-blue-800 text-sm">
                            &larr; Back to Dashboard
                        </Link>
                    </div>

                    <form @submit.prevent="submit">
                        
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Doctor Name</label>
                            <input v-model="form.name" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Email Address</label>
                            <input v-model="form.email" type="email" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required />
                            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Phone Number</label>
                            <input v-model="form.phone" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" />
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Specialization (e.g. Cardiologist)</label>
                            <input v-model="form.specialization" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required />
                            <div v-if="form.errors.specialization" class="text-red-500 text-xs mt-1">{{ form.errors.specialization }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Consultation Fee (Rs)</label>
                            <input v-model="form.consultation_fee" type="number" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required />
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Password</label>
                            <input v-model="form.password" type="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required />
                            <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                        </div>

                        <div class="mb-6">
                            <label class="block font-medium text-sm text-gray-700">Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required />
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 font-semibold text-xs uppercase tracking-widest">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>