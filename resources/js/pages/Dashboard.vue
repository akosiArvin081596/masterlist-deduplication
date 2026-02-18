<script setup lang="ts">
import { useDarkMode } from '@/composables/useDarkMode';
import type { Auth } from '@/types/auth';
import { Link, Head } from '@inertiajs/vue3';
import { destroy } from '@/actions/App/Http/Controllers/Auth/LoginController';

type Stats = {
    totalRecords: number;
    totalPairs: number;
    processedRecords: number;
    cleanRecords: number;
    confirmedPairs: number;
    pendingPairs: number;
    resolvedPairs: number;
};

type ActivityItem = {
    action: string;
    detail: string;
    time: string;
    status: 'success' | 'warning' | 'error';
};

type ProcessingStatus = {
    processedPercent: number;
    resolvedPercent: number;
    pendingPercent: number;
    lastRun: string;
    isActive: boolean;
};

const props = defineProps<{
    auth: Auth;
    stats: Stats;
    recentActivity: ActivityItem[];
    processingStatus: ProcessingStatus;
}>();

const { isDark, toggle } = useDarkMode();

const statCards = [
    {
        label: 'Total Records',
        value: () => props.stats.totalRecords.toLocaleString(),
        sub: () => `Across ${props.stats.totalRecords > 0 ? 'all uploaded masterlists' : 'no masterlists yet'}`,
    },
    {
        label: 'Duplicate Pairs Found',
        value: () => props.stats.totalPairs.toLocaleString(),
        sub: () => props.stats.totalRecords > 0
            ? `${((props.stats.confirmedPairs / Math.max(props.stats.totalPairs, 1)) * 100).toFixed(1)}% confirmed`
            : 'No deduplication run yet',
    },
    {
        label: 'Records Processed',
        value: () => props.stats.processedRecords.toLocaleString(),
        sub: () => props.stats.totalRecords > 0
            ? `${((props.stats.processedRecords / props.stats.totalRecords) * 100).toFixed(1)}% of total`
            : 'No completed masterlists',
    },
    {
        label: 'Clean Records',
        value: () => props.stats.cleanRecords.toLocaleString(),
        sub: () => props.stats.processedRecords > 0
            ? `${((props.stats.cleanRecords / props.stats.processedRecords) * 100).toFixed(1)}% clean rate`
            : '—',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <!-- Navbar -->
        <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-900 dark:bg-blue-800 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-sm font-bold">DS</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-none">Masterlist Deduplication</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">DSWD</p>
                        </div>
                    </div>

                    <nav class="hidden sm:flex items-center gap-1">
                        <Link
                            href="/"
                            class="px-3 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-md"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href="/masterlists"
                            class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors"
                        >
                            Masterlists
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="toggle"
                        class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 pl-3 border-l border-gray-200 dark:border-gray-700">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-none">{{ auth.user.name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ auth.user.email }}</p>
                        </div>
                        <Link
                            :href="destroy().url"
                            method="delete"
                            as="button"
                            class="ml-2 text-xs text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800"
                        >
                            Sign out
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page heading -->
            <div class="mb-8">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview of deduplication operations</p>
            </div>

            <!-- Stat cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div
                    v-for="card in statCards"
                    :key="card.label"
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 px-5 py-5 shadow-sm"
                >
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ card.label }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ card.value() }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ card.sub() }}</p>
                </div>
            </div>

            <!-- Bottom grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <!-- Recent activity -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent Activity</h2>
                        <Link href="/masterlists" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View all</Link>
                    </div>
                    <ul v-if="recentActivity.length > 0" class="divide-y divide-gray-100 dark:divide-gray-800">
                        <li v-for="(item, i) in recentActivity" :key="i" class="flex items-start gap-3 px-5 py-4">
                            <span
                                :class="{
                                    'bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400': item.status === 'success',
                                    'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-600 dark:text-yellow-400': item.status === 'warning',
                                    'bg-red-100 dark:bg-red-900/40 text-red-500 dark:text-red-400': item.status === 'error',
                                }"
                                class="mt-1.5 w-2 h-2 rounded-full shrink-0"
                            ></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ item.action }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ item.detail }}</p>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0">{{ item.time }}</span>
                        </li>
                    </ul>
                    <div v-else class="px-5 py-10 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500">No activity yet. <Link href="/masterlists" class="text-blue-600 dark:text-blue-400 hover:underline">Upload a masterlist</Link> to get started.</p>
                    </div>
                </div>

                <!-- Processing status -->
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Processing Status</h2>
                    </div>
                    <div class="px-5 py-5 space-y-4">
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1.5">
                                <span>Records Processed</span>
                                <span>{{ processingStatus.processedPercent }}%</span>
                            </div>
                            <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 dark:bg-blue-500 rounded-full transition-all" :style="`width: ${processingStatus.processedPercent}%`"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1.5">
                                <span>Duplicates Resolved</span>
                                <span>{{ processingStatus.resolvedPercent }}%</span>
                            </div>
                            <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full transition-all" :style="`width: ${processingStatus.resolvedPercent}%`"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1.5">
                                <span>Pending Review</span>
                                <span>{{ processingStatus.pendingPercent }}%</span>
                            </div>
                            <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-400 rounded-full transition-all" :style="`width: ${processingStatus.pendingPercent}%`"></div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800 space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Last dedup run</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ processingStatus.lastRun }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Pending pairs</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ stats.pendingPairs.toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Status</span>
                                <span
                                    :class="processingStatus.isActive ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400'"
                                    class="font-medium"
                                >
                                    {{ processingStatus.isActive ? 'Active' : 'Idle' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
