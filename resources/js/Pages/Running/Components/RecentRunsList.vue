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
    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient overflow-hidden">
        <div class="px-8 py-6 border-b border-atlas-border/50 flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-atlas-background/30">
            <h3 class="font-bold text-atlas-muted text-[10px] uppercase tracking-widest">Recent Runs</h3>
            <div class="flex gap-2">
                <select v-model="filterType" class="bg-atlas-surface text-atlas-text text-xs font-bold rounded-lg border border-atlas-border py-1.5 focus:border-atlas-text focus:ring-0 shadow-sm transition-colors cursor-pointer hover:bg-atlas-panel">
                    <option value="all">All Types</option>
                    <option value="easy">Easy</option>
                    <option value="workout">Workout</option>
                    <option value="long">Long Run</option>
                </select>
                <select v-model="sortBy" class="bg-atlas-surface text-atlas-text text-xs font-bold rounded-lg border border-atlas-border py-1.5 focus:border-atlas-text focus:ring-0 shadow-sm transition-colors cursor-pointer hover:bg-atlas-panel">
                    <option value="date_desc">Newest First</option>
                    <option value="date_asc">Oldest First</option>
                    <option value="dist_desc">Longest Distance</option>
                    <option value="dist_asc">Shortest Distance</option>
                </select>
            </div>
        </div>
        <ul v-if="filteredRuns.length > 0" class="divide-y divide-atlas-border/30">
            <li v-for="run in filteredRuns" :key="run.id" class="px-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between hover:bg-atlas-background/50 transition-colors gap-4 group">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h4 class="font-serif text-xl tracking-tight text-atlas-text group-hover:text-atlas-primaryStart transition-colors">{{ run.notes ? run.notes.split('\n')[0] : 'Log: Unnamed Run' }}</h4>
                        <span class="bg-atlas-surface border border-atlas-border/50 text-atlas-muted text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded shadow-sm">{{ run.type }}</span>
                    </div>
                    <p class="text-[10px] uppercase tracking-widest text-atlas-muted font-bold">{{ run.date }}</p>
                </div>
                <div class="flex items-center gap-8">
                    <div class="text-left w-16">
                        <p class="text-[10px] uppercase font-bold text-atlas-muted tracking-widest mb-1">Dist</p>
                        <p class="font-bold text-atlas-text text-base">{{ run.distance }} <span class="text-xs font-sans font-medium text-atlas-muted">km</span></p>
                    </div>
                    <div class="text-left w-16">
                        <p class="text-[10px] uppercase font-bold text-atlas-muted tracking-widest mb-1">Pace</p>
                        <p class="font-bold text-atlas-text text-base">{{ run.pace }}</p>
                    </div>
                    <div class="text-left w-16">
                        <p class="text-[10px] uppercase font-bold text-atlas-muted tracking-widest mb-1">Time</p>
                        <p class="font-bold text-atlas-text text-base">{{ run.duration }}</p>
                    </div>
                </div>
            </li>
        </ul>
        <div v-else class="p-12 text-center text-atlas-muted text-sm font-bold tracking-wide italic">No runs logged in this view.</div>
    </div>
</template>
