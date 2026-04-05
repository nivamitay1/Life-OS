<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    priorities: Array,
    suggestedAction: Object,
    badges: Array,
    streaks: Object,
    stats: Object,
});

const showAllPriorities = ref(false);
const visiblePriorities = computed(() => {
    return showAllPriorities.value ? props.priorities : props.priorities.slice(0, 4);
});
</script>

<template>
    <Head title="Command Center | Life OS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Command Center</h2>
                    <div class="mt-1 flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Level {{ stats.overallLevel }}</span>
                        <div class="w-1.5 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                        <span class="text-sm text-gray-400 dark:text-gray-500">{{ Number(stats.overallXp).toLocaleString() }} XP</span>
                    </div>
                </div>
                
                <div v-if="suggestedAction" class="flex-shrink-0">
                    <Link :href="suggestedAction.url" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Start Next Action
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6 sm:space-y-8 max-w-4xl mx-auto mt-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                
                <div class="md:col-span-2 space-y-6 sm:space-y-8">
                    <!-- Action Feed -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Today's Feed
                            </h3>
                        </div>
                        <ul v-if="visiblePriorities.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <li v-for="priority in visiblePriorities" :key="priority.id" class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div :class="['w-2 h-2 rounded-full', priority.isOverdue ? 'bg-red-500' : 'bg-indigo-500']"></div>
                                    <div>
                                        <p :class="[priority.isOverdue ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-900 dark:text-white font-medium', 'text-sm']">
                                            {{ priority.text }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ priority.isOverdue ? 'Overdue' : 'Due Today' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        {{ priority.module }}
                                    </span>
                                    <Link :href="priority.url" class="opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">{{ priority.actionText }} &rarr;</Link>
                                </div>
                            </li>
                        </ul>
                        <div v-else class="p-8 text-center">
                            <div class="mx-auto w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-gray-900 dark:text-white font-bold mb-1">You're all caught up!</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Enjoy your day or jump into a module to start something new.</p>
                        </div>
                        <div v-if="priorities.length > 4" class="border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 p-3 text-center">
                            <button @click="showAllPriorities = !showAllPriorities" class="text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                {{ showAllPriorities ? 'Show Less' : 'View All Priorities (' + priorities.length + ' total)' }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 sm:space-y-8">
                    <!-- Streaks Widget -->
                    <div class="bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl shadow-sm p-6 text-white flex flex-col justify-center relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 bg-white/10 w-24 h-24 rounded-full group-hover:scale-150 transition-transform duration-700 ease-out"></div>
                        <div class="flex justify-between items-center z-10">
                            <div>
                                <h3 class="text-orange-100 text-xs font-bold uppercase tracking-widest">Active Streak</h3>
                                <div class="text-3xl font-black tracking-tight mt-1 flex items-baseline gap-1">
                                    {{ streaks.current }} <span class="text-sm font-bold text-orange-200">days</span>
                                </div>
                            </div>
                            <div class="text-4xl filter drop-shadow-md">
                                🔥
                            </div>
                        </div>
                    </div>

                    <!-- Recent Wins (Badges) -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="font-bold text-gray-900 dark:text-white">Recent Wins</h3>
                        </div>
                        <div class="space-y-3">
                            <div v-for="badge in badges" :key="badge.name" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div :class="[badge.color, 'h-10 w-10 border border-gray-100 dark:border-gray-700 rounded-xl flex items-center justify-center text-lg shadow-sm']">
                                    {{ badge.icon }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-white">{{ badge.name }}</h4>
                                    <div class="text-[10px] uppercase font-bold text-gray-400 mt-0.5">{{ badge.module }} &bull; {{ badge.date }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
