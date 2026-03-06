<script setup>
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import axios from 'axios'; // AI ko message bhejne ke liye

const showingNavigationDropdown = ref(false);

// ==========================================
// AI CHATBOT LOGIC
// ==========================================
const chatOpen = ref(false);
const newMessage = ref('');
const isLoading = ref(false);
const messages = ref([
    { sender: 'bot', text: 'Assalam o Alaikum! Main Smart Clinic ka AI Assistant hoon. Mai aapki kya madad kar sakta hoon?' }
]);

const scrollToBottom = async () => {
    await nextTick();
    const box = document.getElementById('chatbox');
    if (box) box.scrollTop = box.scrollHeight;
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;

    // User ka message add karo
    messages.value.push({ sender: 'user', text: newMessage.value });
    const userMessage = newMessage.value;
    newMessage.value = '';
    isLoading.value = true;
    scrollToBottom();

    try {
        // Laravel controller ko message bhejo
        const response = await axios.post(route('chatbot.send'), { message: userMessage });
        
        // Jawab add karo
        messages.value.push({ sender: 'bot', text: response.data.reply });
    } catch (error) {
        console.error(error);
        messages.value.push({ sender: 'bot', text: 'Sorry, mera connection thoda slow hai. Dobara try karein.' });
    } finally {
        isLoading.value = false;
        scrollToBottom();
    }
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                {{ $page.props.auth.user.name }}
                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</ResponsiveNavLink>
                    </div>

                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>

        <div class="fixed bottom-6 right-6 z-50">
            <button @click="chatOpen = !chatOpen" class="flex h-14 w-14 transform items-center justify-center rounded-full bg-blue-600 text-white shadow-lg transition hover:scale-105 hover:bg-blue-700">
                <svg v-if="!chatOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div v-if="chatOpen" class="absolute bottom-16 right-0 flex h-96 w-80 flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-2xl">
                <div class="flex items-center justify-between bg-blue-600 p-3 font-bold text-white">
                    <span>🤖 Smart Clinic AI</span>
                </div>

                <div class="flex flex-1 flex-col gap-2 overflow-y-auto bg-gray-50 p-3" id="chatbox">
                    <div v-for="(msg, index) in messages" :key="index" :class="msg.sender === 'user' ? 'text-right' : 'text-left'">
                        <span :class="msg.sender === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'" class="inline-block max-w-[85%] rounded-lg px-3 py-2 text-sm shadow-sm">
                            {{ msg.text }}
                        </span>
                    </div>
                    <div v-if="isLoading" class="text-left">
                        <span class="inline-block rounded-lg bg-gray-200 px-3 py-2 text-xs italic text-gray-500">Typing...</span>
                    </div>
                </div>

                <div class="flex gap-2 border-t bg-white p-3">
                    <input v-model="newMessage" @keyup.enter="sendMessage" type="text" placeholder="Ask about health or clinic..." class="flex-1 rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" :disabled="isLoading" />
                    <button @click="sendMessage" :disabled="isLoading || !newMessage.trim()" class="rounded bg-blue-600 px-3 py-2 text-white hover:bg-blue-700 disabled:opacity-50">
                        ➤
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>