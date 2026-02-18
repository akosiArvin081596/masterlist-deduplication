<script setup lang="ts">
import { useDarkMode } from '@/composables/useDarkMode';
import type { Auth } from '@/types/auth';
import type { DuplicatePair, Masterlist, MasterlistRecord, PaginatedDuplicatePairs } from '@/types/masterlist';
import { update } from '@/actions/App/Http/Controllers/DuplicatePairController';
import { index as reviewIndex } from '@/actions/App/Http/Controllers/DuplicatePairController';
import MasterlistExportController from '@/actions/App/Http/Controllers/MasterlistExportController';
import { destroy } from '@/actions/App/Http/Controllers/Auth/LoginController';
import AppFooter from '@/components/AppFooter.vue';
import { Form, Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    auth: Auth;
    masterlist: Masterlist;
    pairs: PaginatedDuplicatePairs;
    filter: string;
}>();

const { isDark, toggle } = useDarkMode();

const tabs = [
    { key: 'all', label: 'All' },
    { key: 'pending', label: 'Pending' },
    { key: 'confirmed', label: 'Confirmed' },
    { key: 'dismissed', label: 'Dismissed' },
];

function setFilter(key: string): void {
    router.get(reviewIndex(props.masterlist).url, key !== 'all' ? { filter: key } : {}, { preserveScroll: true });
}

const matchTypeBadge: Record<DuplicatePair['match_type'], string> = {
    exact: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
    fuzzy: 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400',
    typo: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400',
};

const matchTypeLabel: Record<DuplicatePair['match_type'], string> = {
    exact: 'Exact',
    fuzzy: 'Fuzzy',
    typo: 'Typo',
};

const fields: { key: keyof MasterlistRecord; label: string }[] = [
    { key: 'last_name', label: 'Last Name' },
    { key: 'first_name', label: 'First Name' },
    { key: 'middle_name', label: 'Middle Name' },
    { key: 'ext_name', label: 'Ext. Name' },
    { key: 'birthday', label: 'Birthday' },
    { key: 'region_name', label: 'Region' },
    { key: 'province_name', label: 'Province' },
    { key: 'city_name', label: 'City/Municipality' },
    { key: 'barangay_name', label: 'Barangay' },
    { key: 'purok_sitio', label: 'Purok/Sitio' },
];

function isDifferent(pair: DuplicatePair, key: keyof MasterlistRecord): boolean {
    const v1 = String(pair.record1[key] ?? '').toLowerCase();
    const v2 = String(pair.record2[key] ?? '').toLowerCase();
    return v1 !== v2;
}

function displayValue(record: MasterlistRecord, key: keyof MasterlistRecord): string {
    const val = record[key];
    return val ? String(val) : '—';
}

</script>

<template>
    <Head :title="`Review — ${masterlist.incident_name}`" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 flex flex-col">
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
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm mb-6">
                <Link href="/masterlists" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    Masterlists
                </Link>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ masterlist.incident_name }}</span>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <span class="text-gray-500 dark:text-gray-400">Review</span>
            </nav>

            <!-- Header row -->
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Review Duplicate Pairs</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ masterlist.incident_name }}</p>
                </div>
                <a
                    :href="MasterlistExportController.url(masterlist)"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-700 hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-700 rounded-lg transition-colors shrink-0"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Clean List
                </a>
            </div>

            <!-- Stats bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Pairs</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ pairs.total.toLocaleString() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Records</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ masterlist.record_count?.toLocaleString() ?? '—' }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400 capitalize">{{ masterlist.status }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Current Filter</p>
                    <p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-400 capitalize">{{ filter }}</p>
                </div>
            </div>

            <!-- Filter tabs -->
            <div class="flex gap-1 mb-6 border-b border-gray-200 dark:border-gray-800">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    @click="setFilter(tab.key)"
                    :class="[
                        'px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors',
                        filter === tab.key
                            ? 'border-blue-600 text-blue-700 dark:text-blue-400'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200',
                    ]"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Pair cards -->
            <div v-if="pairs.data.length === 0" class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm px-6 py-12 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">No duplicate pairs found for this filter.</p>
            </div>

            <div class="space-y-4">
                <div
                    v-for="pair in pairs.data"
                    :key="pair.id"
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden"
                >
                    <!-- Card header -->
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span
                                :class="matchTypeBadge[pair.match_type]"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wide"
                            >
                                {{ matchTypeLabel[pair.match_type] }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ pair.confidence }}% confidence</span>
                            <span
                                v-if="pair.record1.masterlist?.incident_name !== pair.record2.masterlist?.incident_name"
                                class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 px-2 py-0.5 rounded font-medium"
                            >
                                Cross-list
                            </span>
                        </div>
                        <span
                            v-if="pair.status !== 'pending'"
                            :class="{
                                'text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30': pair.status === 'confirmed',
                                'text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800': pair.status === 'dismissed',
                            }"
                            class="text-xs font-medium px-2 py-0.5 rounded capitalize"
                        >
                            {{ pair.status }}
                        </span>
                    </div>

                    <!-- Side-by-side comparison -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 w-32">Field</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        Record 1
                                        <span v-if="pair.record1.masterlist" class="font-normal text-gray-400 dark:text-gray-500"> — {{ pair.record1.masterlist.incident_name }}</span>
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        Record 2
                                        <span v-if="pair.record2.masterlist" class="font-normal text-gray-400 dark:text-gray-500"> — {{ pair.record2.masterlist.incident_name }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr v-for="field in fields" :key="field.key">
                                    <td class="px-4 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ field.label }}</td>
                                    <td
                                        :class="isDifferent(pair, field.key) ? 'bg-yellow-50 dark:bg-yellow-900/10 text-yellow-900 dark:text-yellow-200' : 'text-gray-800 dark:text-gray-200'"
                                        class="px-4 py-2 text-xs"
                                    >
                                        {{ displayValue(pair.record1, field.key) }}
                                    </td>
                                    <td
                                        :class="isDifferent(pair, field.key) ? 'bg-yellow-50 dark:bg-yellow-900/10 text-yellow-900 dark:text-yellow-200' : 'text-gray-800 dark:text-gray-200'"
                                        class="px-4 py-2 text-xs"
                                    >
                                        {{ displayValue(pair.record2, field.key) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Action buttons -->
                    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-3">
                        <Form v-bind="update.form(pair)" #default="{ processing }">
                            <input type="hidden" name="status" value="confirmed" />
                            <button
                                type="submit"
                                :disabled="processing || pair.status !== 'pending'"
                                class="px-3 py-1.5 text-xs font-medium text-white bg-green-700 hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors"
                            >
                                {{ pair.status === 'confirmed' ? 'Confirmed' : 'Confirm Duplicate' }}
                            </button>
                        </Form>
                        <Form v-bind="update.form(pair)" #default="{ processing }">
                            <input type="hidden" name="status" value="dismissed" />
                            <button
                                type="submit"
                                :disabled="processing || pair.status !== 'pending'"
                                class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors"
                            >
                                {{ pair.status === 'dismissed' ? 'Dismissed' : 'Not a Duplicate' }}
                            </button>
                        </Form>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pairs.last_page > 1" class="mt-6 flex items-center justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Showing {{ pairs.from }}–{{ pairs.to }} of {{ pairs.total }}
                </p>
                <div class="flex gap-2">
                    <Link
                        v-if="pairs.prev_page_url"
                        :href="pairs.prev_page_url"
                        class="px-3 py-1.5 text-xs rounded border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="pairs.next_page_url"
                        :href="pairs.next_page_url"
                        class="px-3 py-1.5 text-xs rounded border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </main>

        <AppFooter />
    </div>
</template>
