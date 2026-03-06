<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3'; // ✅ YAHAN 'Link' ADD KIYA HAI

defineProps({ invoices: Array });
</script>

<template>
    <Head title="My Invoices" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Billing & Invoices
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="$page.props.flash.message" class="mb-4 bg-green-100 text-green-700 p-4 rounded border border-green-200 shadow-sm">
                    {{ $page.props.flash.message }}
                </div>

                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Invoice #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Doctor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="inv in invoices" :key="inv.id">
                                <td class="px-6 py-4 font-mono text-xs">
                                    {{ inv.invoice_number }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ new Date(inv.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4">
                                    Dr. {{ inv.appointment.doctor.name }}
                                </td>
                                <td class="px-6 py-4 font-bold">
                                    Rs. {{ inv.amount }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="inv.payment_status === 'paid' 
                                            ? 'bg-green-100 text-green-800' 
                                            : 'bg-red-100 text-red-800'"
                                        class="rounded px-2 py-1 text-xs font-bold uppercase"
                                    >
                                        {{ inv.payment_status }}
                                    </span>
                                </td>
                                <td class="flex items-center gap-2 px-6 py-4">
                                    <Link
                                        :href="route('invoices.show', inv.id)"
                                        class="text-sm font-bold text-gray-600 underline hover:text-blue-600"
                                    >
                                        View Bill
                                    </Link>

                                    <Link
                                        v-if="inv.payment_status === 'unpaid'"
                                        :href="route('invoices.pay', inv.id)"
                                        method="post"
                                        as="button"
                                        class="rounded bg-blue-600 px-3 py-1 text-xs text-white shadow transition hover:bg-blue-700"
                                    >
                                        Pay Now
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="invoices.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No invoices found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>