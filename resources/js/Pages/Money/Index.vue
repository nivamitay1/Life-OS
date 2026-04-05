<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import TransactionModal from './Components/TransactionModal.vue';
import GoalsList from './Components/GoalsList.vue';
import RecentTransactionsList from './Components/RecentTransactionsList.vue';
import SubscriptionsList from './Components/SubscriptionsList.vue';

const props = defineProps({
  entries: Array,
  subscriptions: Array,
  goals: Array,
  stats: Object,
  levelData: Object,
  isFreshInstall: Boolean,
});

const showEntryModal = ref(false);
const duplicateEntryData = ref(null);

const openNewTransaction = () => {
    duplicateEntryData.value = null;
    showEntryModal.value = true;
};

const handleDuplicate = (tx) => {
    duplicateEntryData.value = tx;
    showEntryModal.value = true;
};

const loadBlueprint = () => {
    useForm({}).post(route('money.seed-blueprint'), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Money | Life OS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center px-2 gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Money Workspace</h2>
                </div>
                
                <div class="flex-shrink-0">
                    <button @click="openNewTransaction" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        New Transaction
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6 sm:space-y-8 px-2 mt-4 sm:mt-6">
            
            <div v-if="isFreshInstall" class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border-2 border-emerald-500 rounded-2xl p-8 text-center shadow-sm mb-8">
                <div class="mx-auto w-16 h-16 bg-emerald-100 dark:bg-emerald-800/50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Welcome to your Money Workspace</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 max-w-xl mx-auto text-sm">You haven't set up any goals or subscriptions yet. Would you like to automatically load a standard **50/30/20 Budgeting Blueprint** to get started instantly?</p>
                <button @click="loadBlueprint" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition-all active:scale-95">Load Budget Blueprint</button>
            </div>

            <!-- Key Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 bg-emerald-500/5 group-hover:bg-emerald-500/10 w-20 h-20 rounded-full transition-colors duration-500"></div>
                    <dt class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Balance</dt>
                    <dd class="mt-2 text-2xl font-black text-gray-900 dark:text-white">{{ stats.totalBalance }}</dd>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 bg-emerald-500/5 group-hover:bg-emerald-500/10 w-20 h-20 rounded-full transition-colors duration-500"></div>
                    <dt class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monthly Income</dt>
                    <dd class="mt-2 text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ stats.monthlyIncome }}</dd>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 bg-red-500/5 group-hover:bg-red-500/10 w-20 h-20 rounded-full transition-colors duration-500"></div>
                    <dt class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monthly Expenses</dt>
                    <dd class="mt-2 text-2xl font-black text-red-600 dark:text-red-400">{{ stats.monthlyExpenses }}</dd>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 bg-indigo-500/5 group-hover:bg-indigo-500/10 w-20 h-20 rounded-full transition-colors duration-500"></div>
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Mo. Fixed Burn</dt>
                    <dd class="mt-2 text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ stats.monthlyFixedExpenses }}</dd>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="lg:col-span-2">
                    <GoalsList :goals="goals" />
                    <SubscriptionsList :subscriptions="subscriptions" />
                    <div class="mt-6 sm:mt-8">
                        <RecentTransactionsList :entries="entries" @duplicate="handleDuplicate" />
                    </div>
                </div>
                <div class="space-y-6 sm:space-y-8">
                    <!-- Right column widget -->
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-sm p-6 text-white text-center">
                        <h3 class="text-emerald-100 text-sm font-bold uppercase tracking-wider mb-2">Module Level</h3>
                        <div v-if="levelData">
                            <div class="text-4xl font-black mb-1">{{ levelData.level }}</div>
                            <div class="text-sm text-emerald-100 mb-4">{{ Number(levelData.xp).toLocaleString() }} / {{ Number(levelData.next_level_base).toLocaleString() }} XP</div>
                            <div class="w-full bg-black/20 rounded-full h-1.5 overflow-hidden mb-2">
                                <div class="bg-white h-1.5 rounded-full" :style="{ width: levelData.progress_percent + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <TransactionModal :show="showEntryModal" :duplicateEntry="duplicateEntryData" @close="showEntryModal = false" />

    </AuthenticatedLayout>
</template>
