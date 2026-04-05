<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    entries: Array
});

const emit = defineEmits(['duplicate']);

const filterType = ref('all');
const sortBy = ref('date_desc');

const filteredEntries = computed(() => {
    let result = props.entries;
    
    if (filterType.value !== 'all') {
        result = result.filter(e => e.type === filterType.value);
    }
    
    result = [...result].sort((a, b) => {
        if (sortBy.value === 'date_desc') return new Date(b.date) - new Date(a.date);
        if (sortBy.value === 'date_asc') return new Date(a.date) - new Date(b.date);
        if (sortBy.value === 'amount_desc') return Math.abs(b.amount) - Math.abs(a.amount);
        if (sortBy.value === 'amount_asc') return Math.abs(a.amount) - Math.abs(b.amount);
        return 0;
    });
    
    return result;
});

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <h3 class="font-bold text-gray-900 dark:text-white">Recent Transactions</h3>
            <div class="flex gap-2">
                <select v-model="filterType" class="text-xs font-medium rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 py-1.5 focus:border-emerald-500 text-gray-900 dark:text-white">
                    <option value="all">All Types</option>
                    <option value="income">Income Only</option>
                    <option value="expense">Expenses Only</option>
                </select>
                <select v-model="sortBy" class="text-xs font-medium rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 py-1.5 focus:border-emerald-500 text-gray-900 dark:text-white">
                    <option value="date_desc">Newest First</option>
                    <option value="date_asc">Oldest First</option>
                    <option value="amount_desc">Highest Amount</option>
                    <option value="amount_asc">Lowest Amount</option>
                </select>
            </div>
        </div>
        <ul v-if="filteredEntries.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700/50">
            <li v-for="tx in filteredEntries" :key="tx.id" class="group px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-center gap-4">
                    <div :class="[tx.type === 'income' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'h-10 w-10 rounded-xl flex items-center justify-center font-bold text-lg']">
                        {{ tx.type === 'income' ? '+' : '-' }}
                    </div>
                    <div>
                        <p class="font-bold text-sm text-gray-900 dark:text-white">{{ tx.description }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ formatDate(tx.date) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div :class="[tx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white', 'font-black']">
                        {{ (tx.type === 'income' ? '+' : '') + tx.amount }}
                    </div>
                    <button @click="emit('duplicate', tx)" class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400" title="Duplicate">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </li>
        </ul>
        <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">No transactions yet. Add one above!</div>
    </div>
</template>
