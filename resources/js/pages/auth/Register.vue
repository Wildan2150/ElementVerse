<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';

defineOptions({
    layout: {
        title: 'Register - ElementVerse',
        description: 'Enter your details below to create your account',
    },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// State untuk toggle mata password
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmation = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register - ElementVerse" />

    <div
        class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-[#070814] py-12 font-sans text-slate-100 sm:px-6 lg:px-8"
    >
        <!-- Decorative Glow Orbs -->
        <div class="pointer-events-none absolute inset-0 z-0">
            <div
                class="absolute -top-20 -left-20 h-80 w-80 rounded-full bg-violet-600/10 blur-[120px]"
            ></div>
            <div
                class="absolute right-10 bottom-10 h-80 w-80 rounded-full bg-[#00ffff]/5 blur-[120px]"
            ></div>
            <div
                class="absolute top-[30%] left-[-10%] h-[1px] w-[600px] rotate-[-35deg] bg-gradient-to-r from-transparent via-[#00ffff]/20 to-transparent blur-[1px]"
            ></div>
        </div>

        <div class="relative z-10 sm:mx-auto sm:w-full sm:max-w-[420px]">
            <div
                class="border border-white/5 bg-white/[0.02] px-6 py-8 shadow-2xl backdrop-blur-md sm:rounded-xl sm:px-10"
            >
                <div class="mb-7 text-center">
                    <h1
                        class="mb-1 text-[28px] font-bold tracking-tight text-white"
                    >
                        Create Account
                    </h1>
                    <div
                        class="mb-3 bg-gradient-to-r from-[#d2ff00] to-[#00ffff] bg-clip-text text-[11.5px] font-black tracking-[0.25em] text-transparent uppercase select-none"
                    >
                        ElementVerse
                    </div>
                    <p class="text-[14px] font-medium text-slate-400">
                        Enter your details below to get started
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Nama Field -->
                    <div>
                        <label
                            for="name"
                            class="mb-1.5 block text-[14px] font-semibold text-slate-300"
                            >Full Name</label
                        >
                        <div class="mt-1">
                            <input
                                id="name"
                                type="text"
                                autocomplete="name"
                                required
                                autofocus
                                v-model="form.name"
                                class="block w-full appearance-none rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-slate-500 shadow-sm transition-colors focus:border-[var(--theme-primary)] focus:ring-2 focus:ring-[var(--theme-primary)]/20 focus:outline-none sm:text-[14px]"
                                placeholder="E.g. Budi Santoso"
                            />
                        </div>
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label
                            for="email"
                            class="mb-1.5 block text-[14px] font-semibold text-slate-300"
                            >Email address</label
                        >
                        <div class="mt-1">
                            <input
                                id="email"
                                type="email"
                                autocomplete="email"
                                required
                                v-model="form.email"
                                class="block w-full appearance-none rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-slate-500 shadow-sm transition-colors focus:border-[var(--theme-primary)] focus:ring-2 focus:ring-[var(--theme-primary)]/20 focus:outline-none sm:text-[14px]"
                                placeholder="E.g. budi@siswa.com"
                            />
                        </div>
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label
                            for="password"
                            class="mb-1.5 block text-[14px] font-semibold text-slate-300"
                            >Password</label
                        >
                        <div class="relative mt-1">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                v-model="form.password"
                                class="block w-full appearance-none rounded-lg border border-white/10 bg-white/5 py-2.5 pr-10 pl-4 text-white placeholder-slate-500 shadow-sm transition-colors focus:border-[var(--theme-primary)] focus:ring-2 focus:ring-[var(--theme-primary)]/20 focus:outline-none sm:text-[14px]"
                                placeholder="Create a password"
                            />

                            <button
                                type="button"
                                @click.prevent="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-slate-400 transition-colors hover:text-white focus:outline-none"
                            >
                                <svg
                                    v-show="!showPassword"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    />
                                </svg>
                                <svg
                                    v-show="showPassword"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                    />
                                </svg>
                            </button>
                        </div>
                        <InputError
                            :message="form.errors.password"
                            class="mt-1"
                        />
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label
                            for="password_confirmation"
                            class="mb-1.5 block text-[14px] font-semibold text-slate-300"
                            >Confirm Password</label
                        >
                        <div class="relative mt-1">
                            <input
                                id="password_confirmation"
                                :type="
                                    showPasswordConfirmation
                                        ? 'text'
                                        : 'password'
                                "
                                autocomplete="new-password"
                                required
                                v-model="form.password_confirmation"
                                class="block w-full appearance-none rounded-lg border border-white/10 bg-white/5 py-2.5 pr-10 pl-4 text-white placeholder-slate-500 shadow-sm transition-colors focus:border-[var(--theme-primary)] focus:ring-2 focus:ring-[var(--theme-primary)]/20 focus:outline-none sm:text-[14px]"
                                placeholder="Confirm your password"
                            />

                            <button
                                type="button"
                                @click.prevent="togglePasswordConfirmation"
                                class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-slate-400 transition-colors hover:text-white focus:outline-none"
                            >
                                <svg
                                    v-show="!showPasswordConfirmation"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    />
                                </svg>
                                <svg
                                    v-show="showPasswordConfirmation"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                    />
                                </svg>
                            </button>
                        </div>
                        <InputError
                            :message="form.errors.password_confirmation"
                            class="mt-1"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex w-full justify-center rounded-lg border border-transparent bg-gradient-to-r from-[#d2ff00] to-[#00ffff] px-4 py-2.5 text-[15px] font-bold text-[#070814] shadow-[0_0_15px_rgba(210,255,0,0.3)] transition-all hover:brightness-110 focus:ring-2 focus:ring-[#d2ff00] focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Spinner
                                v-if="form.processing"
                                class="mr-2 h-5 w-5 text-[#070814]"
                            />
                            Register
                        </button>
                    </div>
                </form>

                <!-- Redirect to Login -->
                <div class="mt-8 text-center text-[14px]">
                    <span class="font-medium text-slate-400"
                        >Already have an account?
                    </span>
                    <Link
                        href="/login"
                        class="font-semibold text-[#00ffff] transition-colors hover:text-[#d2ff00]"
                    >
                        Log in ↗
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
