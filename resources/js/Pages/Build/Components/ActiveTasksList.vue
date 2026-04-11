<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    tasks: Array
});

const filterStatus = ref('all');
const sortBy = ref('urgent');

const filteredTasks = computed(() => {
    let result = props.tasks;
    
    if (filterStatus.value !== 'all') {
        result = result.filter(t => t.status === filterStatus.value);
    }
    
    result = [...result].sort((a, b) => {
        if (sortBy.value === 'urgent') {
            // Priority 1: Blockers
            if (a.is_blocker && !b.is_blocker) return -1;
            if (!a.is_blocker && b.is_blocker) return 1;
            
            // Priority 2: In Progress
            if (a.status === 'in_progress' && b.status !== 'in_progress') return -1;
            if (a.status !== 'in_progress' && b.status === 'in_progress') return 1;

            // Priority 3: Fallback newly created
            return b.id - a.id;
        }
        if (sortBy.value === 'new') return b.id - a.id;
        return 0;
    });
    
    return result;
});
</script>

<template>
    <div class="bg-atlas-panel backdrop-blur-2xl border border-atlas-border rounded-[24px] shadow-ambient overflow-hidden">
        <div class="px-8 py-6 border-b border-atlas-border/50 flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-atlas-surface/30">
            <h3 class="font-serif text-lg text-atlas-text">Active Tasks</h3>
            <div class="flex gap-3">
                <select v-model="filterStatus" class="text-[10px] uppercase font-bold tracking-widest rounded-lg border-atlas-border bg-atlas-panel py-2 focus:ring-0 focus:border-atlas-primaryStart text-atlas-muted">
                    <option value="all">All Tasks</option>
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                </select>
                <select v-model="sortBy" class="text-[10px] uppercase font-bold tracking-widest rounded-lg border-atlas-border bg-atlas-panel py-2 focus:ring-0 focus:border-atlas-primaryStart text-atlas-muted">
                    <option value="urgent">Urgent First</option>
                    <option value="new">Newest First</option>
                </select>
            </div>
        </div>
        <ul v-if="filteredTasks.length > 0" class="divide-y divide-atlas-border/30">
            <li v-for="task in filteredTasks" :key="task.id" class="px-8 py-5 flex items-center justify-between hover:bg-atlas-surface transition-colors group">
                <p class="text-sm font-medium text-atlas-text flex items-center gap-3">
                    <span v-if="task.is_blocker" class="text-[10px] text-atlas-muted border border-red-500/30 bg-red-500/10 px-2 py-0.5 rounded uppercase tracking-widest font-bold">Blocker</span>
                    {{ task.title }}
                </p>
                <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded bg-atlas-surface border border-atlas-border text-atlas-muted opacity-60 group-hover:opacity-100 transition-opacity">{{ task.project }}</span>
            </li>
        </ul>
        <div v-else class="p-10 text-center text-atlas-muted text-xs tracking-wide">No active tasks match this filter.</div>
    </div>
</template>
