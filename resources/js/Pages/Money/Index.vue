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
        <div class="space-y-12 mt-2">
            
            <div class="flex items-center justify-between">
                <h1 class="text-[10px] uppercase tracking-[0.2em] font-bold text-atlas-muted border border-atlas-border px-4 py-2 rounded-lg bg-atlas-panel shadow-sm">Money Ledger</h1>
                
                <button @click="openNewTransaction" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd px-5 py-2 text-xs font-bold tracking-wider text-atlas-surface shadow-ambient hover:scale-95 transition-transform">
                    + New Transaction
                </button>
            </div>
            
            <div v-if="isFreshInstall" class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] p-10 text-center shadow-ambient mb-12 flex flex-col items-center">
                <div class="w-16 h-16 bg-atlas-surface border border-atlas-border rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                    <svg class="w-8 h-8 text-atlas-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-2xl font-serif text-atlas-text mb-3">The Blank Canvas.</h3>
                <p class="text-atlas-muted mb-8 max-w-xl mx-auto text-base">You haven't set up any goals or subscriptions yet. Would you like to automatically load a standard **50/30/20 Budgeting Blueprint** to establish your foundation?</p>
                <button @click="loadBlueprint" class="px-8 py-3 bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd text-atlas-surface tracking-wide rounded-2xl shadow-ambient transition-all hover:scale-95">Load Blueprint</button>
            </div>

            <!-- Floating Data Row (Key Stats) -->
            <div class="flex flex-wrap gap-x-16 gap-y-8 px-4 items-center">
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Total Balance</dt>
                    <dd class="text-3xl font-serif text-atlas-text">{{ stats.totalBalance }}</dd>
                </div>
                <div class="w-px h-12 bg-atlas-border/50 hidden md:block"></div>
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Monthly Income</dt>
                    <dd class="text-3xl font-serif text-atlas-text">{{ stats.monthlyIncome }}</dd>
                </div>
                <div class="w-px h-12 bg-atlas-border/50 hidden md:block"></div>
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Monthly Expenses</dt>
                    <dd class="text-3xl font-serif text-atlas-text">{{ stats.monthlyExpenses }}</dd>
                </div>
                <div class="w-px h-12 bg-atlas-border/50 hidden lg:block"></div>
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Mo. Fixed Burn</dt>
                    <dd class="text-3xl font-serif text-atlas-text opacity-70">{{ stats.monthlyFixedExpenses }}</dd>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pt-6">
                <div class="lg:col-span-2 space-y-12">
                    <GoalsList :goals="goals" />
                    <SubscriptionsList :subscriptions="subscriptions" />
                    <div class="pt-4">
                        <RecentTransactionsList :entries="entries" @duplicate="handleDuplicate" />
                    </div>
                </div>
                <div class="space-y-12">
                    <!-- Right column widget -->
                    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient p-8 text-center flex flex-col items-center">
                        <h3 class="text-atlas-muted text-[10px] font-bold uppercase tracking-widest mb-3">Financial Mastery</h3>
                        <div v-if="levelData" class="w-full">
                            <div class="text-5xl font-serif text-atlas-text mb-2">{{ levelData.level }}</div>
                            <div class="text-xs tracking-wide text-atlas-muted mb-6">{{ Number(levelData.xp).toLocaleString() }} / {{ Number(levelData.next_level_base).toLocaleString() }} XP</div>
                            <div class="w-full bg-atlas-surface border border-atlas-border/50 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-atlas-text h-1.5 rounded-full" :style="{ width: levelData.progress_percent + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <TransactionModal :show="showEntryModal" :duplicateEntry="duplicateEntryData" @close="showEntryModal = false" />

    </AuthenticatedLayout>
</template>
