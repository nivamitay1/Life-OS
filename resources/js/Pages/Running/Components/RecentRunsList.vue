<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    runs: Array
});

const filterType = ref('all');
const sortBy = ref('date_desc');

const filteredRuns = computed(() => {
    let result = props.runs;
    
    if (filterType.value !== 'all') {
        result = result.filter(r => r.type === filterType.value);
    }
    
    result = [...result].sort((a, b) => {
        if (sortBy.value === 'date_desc') return new Date(b.date) - new Date(a.date);
        if (sortBy.value === 'date_asc') return new Date(a.date) - new Date(b.date);
        if (sortBy.value === 'dist_desc') return parseFloat(b.distance) - parseFloat(a.distance);
        if (sortBy.value === 'dist_asc') return parseFloat(a.distance) - parseFloat(b.distance);
        return 0;
    });
    
    return result;
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <h3 class="font-bold text-gray-900 dark:text-white">Recent Runs</h3>
            <div class="flex gap-2">
                <select v-model="filterType" class="text-xs font-medium rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 py-1.5 focus:border-orange-500 text-gray-900 dark:text-white">
                    <option value="all">All Types</option>
                    <option value="easy">Easy</option>
                    <option value="workout">Workout</option>
                    <option value="long">Long Run</option>
                </select>
                <select v-model="sortBy" class="text-xs font-medium rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 py-1.5 focus:border-orange-500 text-gray-900 dark:text-white">
                    <option value="date_desc">Newest First</option>
                    <option value="date_asc">Oldest First</option>
                    <option value="dist_desc">Longest Distance</option>
                    <option value="dist_asc">Shortest Distance</option>
                </select>
            </div>
        </div>
        <ul v-if="filteredRuns.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700/50">
            <li v-for="run in filteredRuns" :key="run.id" class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white">{{ run.notes ? run.notes.split('\n')[0] : 'Run' }}</h4>
                        <span class="bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">{{ run.type }}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ run.date }}</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Dist</p>
                        <p class="font-black text-gray-900 dark:text-white">{{ run.distance }}km</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Pace</p>
                        <p class="font-bold text-gray-700 dark:text-gray-300">{{ run.pace }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Time</p>
                        <p class="font-bold text-gray-700 dark:text-gray-300">{{ run.duration }}</p>
                    </div>
                </div>
            </li>
        </ul>
        <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">No runs logged yet!</div>
    </div>
</template>
