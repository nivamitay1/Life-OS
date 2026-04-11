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
    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient overflow-hidden">
        <div class="px-8 py-6 flex flex-col sm:flex-row justify-between sm:items-center gap-6">
            <h3 class="font-serif text-xl tracking-tight text-atlas-text">Recent Transactions</h3>
            <div class="flex gap-3">
                <select v-model="filterType" class="text-xs font-medium rounded-xl border-atlas-border bg-atlas-surface py-2 focus:border-atlas-primaryStart focus:ring-0 text-atlas-text shadow-sm">
                    <option value="all">All Types</option>
                    <option value="income">Income Only</option>
                    <option value="expense">Expenses Only</option>
                </select>
                <select v-model="sortBy" class="text-xs font-medium rounded-xl border-atlas-border bg-atlas-surface py-2 focus:border-atlas-primaryStart focus:ring-0 text-atlas-text shadow-sm">
                    <option value="date_desc">Newest</option>
                    <option value="date_asc">Oldest</option>
                    <option value="amount_desc">Highest</option>
                    <option value="amount_asc">Lowest</option>
                </select>
            </div>
        </div>
        <ul v-if="filteredEntries.length > 0" class="px-3 pb-4 space-y-1">
            <li v-for="tx in filteredEntries" :key="tx.id" class="group px-5 py-4 flex items-center justify-between hover:bg-atlas-surface rounded-2xl transition-colors">
                <div class="flex items-center gap-5">
                    <div :class="[tx.type === 'income' ? 'bg-atlas-primaryStart/20 text-atlas-primaryStart' : 'bg-atlas-muted/10 text-atlas-muted', 'h-10 w-10 rounded-[14px] flex items-center justify-center font-bold text-lg']">
                        {{ tx.type === 'income' ? '+' : '-' }}
                    </div>
                    <div>
                        <p class="font-medium text-sm text-atlas-text">{{ tx.description }}</p>
                        <p class="text-xs font-sans tracking-wide text-atlas-muted mt-1">{{ formatDate(tx.date) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-5">
                    <div :class="[tx.type === 'income' ? 'text-atlas-text' : 'text-atlas-muted', 'font-serif text-lg tracking-tight']">
                        {{ (tx.type === 'income' ? '+' : '') + tx.amount }}
                    </div>
                    <button @click="emit('duplicate', tx)" class="opacity-0 group-hover:opacity-100 transition-opacity p-2 rounded-xl bg-atlas-surface hover:bg-atlas-border border border-atlas-border text-atlas-muted" title="Duplicate">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </li>
        </ul>
        <div v-else class="p-12 text-center text-atlas-muted text-sm font-sans tracking-wide">No transactions yet. Add one above!</div>
    </div>
</template>
