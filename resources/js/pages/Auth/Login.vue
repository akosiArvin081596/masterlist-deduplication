<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Auth/LoginController';
import { useDarkMode } from '@/composables/useDarkMode';
import { cn } from '@/lib/utils';
import { Head, useForm } from '@inertiajs/vue3';

const { isDark, toggle } = useDarkMode();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit(): void {
    form.submit(store(), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Sign In" />

    <div class="relative min-h-screen bg-gray-50 dark:bg-gray-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Dark mode toggle -->
        <button
            type="button"
            @click="toggle"
            class="absolute top-4 right-4 p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors"
            :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
        >
            <!-- Sun icon -->
            <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
            </svg>
            <!-- Moon icon -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" />
            </svg>
        </button>
        <!-- Branding -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <div class="mx-auto w-16 h-16 bg-blue-900 dark:bg-blue-800 rounded-full flex items-center justify-center">
                <span class="text-white text-2xl font-bold">DS</span>
            </div>
            <h1 class="mt-4 text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                Masterlist Deduplication System
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Department of Social Welfare and Development</p>
        </div>

        <!-- Card -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-900 py-8 px-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-lg">
                <h2 class="mb-6 text-center text-base font-semibold text-gray-800 dark:text-gray-200">Sign in to your account</h2>

                <form class="space-y-5" @submit.prevent="submit">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email address</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            :class="cn(
                                'mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm',
                                'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100',
                                'placeholder:text-gray-400 dark:placeholder:text-gray-500',
                                'focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600',
                                form.errors.email ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600'
                            )"
                            placeholder="you@example.com"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            :class="cn(
                                'mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm',
                                'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100',
                                'placeholder:text-gray-400 dark:placeholder:text-gray-500',
                                'focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600',
                                form.errors.password ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600'
                            )"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-600 dark:bg-gray-800"
                        />
                        <label for="remember" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Remember me</label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        :class="cn(
                            'w-full flex justify-center rounded-md px-3 py-2 text-sm font-semibold text-white',
                            'bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors',
                            'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700',
                            form.processing && 'opacity-60 cursor-not-allowed'
                        )"
                    >
                        {{ form.processing ? 'Signing in…' : 'Sign in' }}
                    </button>
                </form>
            </div>

            <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                For access concerns, contact your system administrator.
            </p>
        </div>
    </div>
</template>
