<script setup>
import { nextTick, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Konsa input dikhana hai (OTP ya Recovery Code)
const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

// OTP aur Recovery Mode ke darmiyan switch karne ka function
const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
        form.clearErrors();
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
        form.clearErrors();
    }
};

const submit = () => {
    // Apne route ka naam check kar lein, aam tor par ye 'two-factor.login' hota hai
    form.post(route('two-factor.login'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Two-factor Confirmation" />

        <div class="mb-4 text-sm text-gray-600">
            <template v-if="! recovery">
                Please confirm access to your account by entering the authentication code provided by your authenticator application.
            </template>

            <template v-else>
                Please confirm access to your account by entering one of your emergency recovery codes.
            </template>
        </div>

        <form @submit.prevent="submit">
            <div v-if="! recovery">
                <InputLabel for="code" value="Authentication Code" />
                <TextInput
                    id="code"
                    ref="codeInput"
                    type="text"
                    inputmode="numeric"
                    class="mt-1 block w-full tracking-widest text-center text-xl"
                    v-model="form.code"
                    autofocus
                    autocomplete="one-time-code"
                    placeholder="123456"
                />
                <InputError class="mt-2" :message="form.errors.code" />
            </div>

            <div v-else>
                <InputLabel for="recovery_code" value="Recovery Code" />
                <TextInput
                    id="recovery_code"
                    ref="recoveryCodeInput"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.recovery_code"
                    autocomplete="one-time-code"
                    placeholder="Enter your emergency code"
                />
                <InputError class="mt-2" :message="form.errors.recovery_code" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <button 
                    type="button" 
                    class="text-sm text-indigo-600 hover:text-indigo-900 underline cursor-pointer" 
                    @click.prevent="toggleRecovery"
                >
                    <template v-if="! recovery">
                        Use a recovery code
                    </template>

                    <template v-else>
                        Use an authentication code
                    </template>
                </button>

                <PrimaryButton 
                    class="ms-4 bg-gray-800 text-white" 
                    :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing"
                >
                    Continue
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>