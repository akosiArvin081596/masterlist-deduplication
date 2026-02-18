<script setup lang="ts">
import { useDarkMode } from '@/composables/useDarkMode';
import type { Auth } from '@/types/auth';
import type { Masterlist, PaginatedMasterlists } from '@/types/masterlist';
import { run } from '@/actions/App/Http/Controllers/DeduplicationController';
import { index as reviewIndex } from '@/actions/App/Http/Controllers/DuplicatePairController';
import { store } from '@/actions/App/Http/Controllers/MasterlistController';
import MasterlistExportController from '@/actions/App/Http/Controllers/MasterlistExportController';
import { destroy } from '@/actions/App/Http/Controllers/Auth/LoginController';
import { Form, Head, Link, usePoll } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    auth: Auth;
    masterlists: PaginatedMasterlists;
}>();

const { isDark, toggle } = useDarkMode();

const showUploadForm = ref(false);

const hasActiveJobs = computed(() =>
    props.masterlists.data.some((m) => m.status === 'processing' || m.status === 'deduplicating'),
);

usePoll(4000, { only: ['masterlists'] }, { autoStart: hasActiveJobs.value });

const statusLabel: Record<Masterlist['status'], string> = {
    pending: 'Pending',
    processing: 'Processing',
    ready: 'Ready',
    deduplicating: 'Deduplicating',
    completed: 'Completed',
};

const statusClass: Record<Masterlist['status'], string> = {
    pending: 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
    processing: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
    ready: 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400',
    deduplicating: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400',
    completed: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
};

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Masterlists" />

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
                            class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors"
                        >
                            Dashboard
                        </Link>
                        <Link
                            :href="reviewIndex.definition.url.replace('/{masterlist}', '')"
                            class="px-3 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-md"
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
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Masterlists</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload and manage beneficiary masterlists for deduplication.</p>
                </div>
                <button
                    type="button"
                    @click="showUploadForm = !showUploadForm"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 rounded-lg transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Masterlist
                </button>
            </div>

            <!-- Upload form (slide-down) -->
            <div v-if="showUploadForm" class="mb-6 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Upload New Masterlist</h2>
                <Form
                    v-bind="store.form()"
                    enctype="multipart/form-data"
                    reset-on-success
                    #default="{ errors, processing }"
                    @success="showUploadForm = false"
                >
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Incident Name</label>
                            <input
                                type="text"
                                name="incident_name"
                                placeholder="e.g. Typhoon Odette — Surigao del Norte"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="errors.incident_name" class="mt-1 text-xs text-red-600 dark:text-red-400">{{ errors.incident_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">CSV File</label>
                            <input
                                type="file"
                                name="file"
                                accept=".csv,.txt"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 file:mr-3 file:text-xs file:font-medium file:border-0 file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-400 file:rounded file:px-2 file:py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="errors.file" class="mt-1 text-xs text-red-600 dark:text-red-400">{{ errors.file }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 disabled:opacity-60 rounded-lg transition-colors"
                        >
                            {{ processing ? 'Uploading...' : 'Upload' }}
                        </button>
                        <button
                            type="button"
                            @click="showUploadForm = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </Form>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div v-if="masterlists.data.length === 0" class="px-6 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No masterlists uploaded yet.</p>
                </div>

                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Incident Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden sm:table-cell">Uploaded</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden md:table-cell">Records</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide hidden md:table-cell">Pairs</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr v-for="masterlist in masterlists.data" :key="masterlist.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ masterlist.incident_name }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                {{ formatDate(masterlist.created_at) }}
                            </td>
                            <td class="px-5 py-4 text-right text-gray-700 dark:text-gray-300 hidden md:table-cell">
                                {{ masterlist.record_count?.toLocaleString() ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-right text-gray-700 dark:text-gray-300 hidden md:table-cell">
                                {{ masterlist.duplicate_pair_count?.toLocaleString() ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    :class="statusClass[masterlist.status]"
                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium"
                                >
                                    <span
                                        v-if="masterlist.status === 'processing' || masterlist.status === 'deduplicating'"
                                        class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"
                                    ></span>
                                    {{ statusLabel[masterlist.status] }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        v-if="masterlist.status === 'ready'"
                                        :href="run(masterlist).url"
                                        method="post"
                                        as="button"
                                        class="text-xs font-medium text-blue-700 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                    >
                                        Run Dedup
                                    </Link>
                                    <Link
                                        v-if="masterlist.status === 'completed'"
                                        :href="reviewIndex(masterlist).url"
                                        class="text-xs font-medium text-indigo-700 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 px-2 py-1 rounded hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors"
                                    >
                                        Review
                                    </Link>
                                    <a
                                        v-if="masterlist.status === 'completed'"
                                        :href="MasterlistExportController.url(masterlist)"
                                        class="text-xs font-medium text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 px-2 py-1 rounded hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors"
                                    >
                                        Export
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="masterlists.last_page > 1" class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Showing {{ masterlists.from }}–{{ masterlists.to }} of {{ masterlists.total }}
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-if="masterlists.prev_page_url"
                            :href="masterlists.prev_page_url"
                            class="px-3 py-1 text-xs rounded border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                        >
                            Previous
                        </Link>
                        <Link
                            v-if="masterlists.next_page_url"
                            :href="masterlists.next_page_url"
                            class="px-3 py-1 text-xs rounded border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                        >
                            Next
                        </Link>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
