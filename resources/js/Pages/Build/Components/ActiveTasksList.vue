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
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <h3 class="font-bold text-gray-900 dark:text-white">Active Tasks</h3>
            <div class="flex gap-2">
                <select v-model="filterStatus" class="text-xs font-medium rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 py-1.5 focus:border-blue-500 text-gray-900 dark:text-white">
                    <option value="all">All Tasks</option>
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                </select>
                <select v-model="sortBy" class="text-xs font-medium rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 py-1.5 focus:border-blue-500 text-gray-900 dark:text-white">
                    <option value="urgent">Urgent First</option>
                    <option value="new">Newest First</option>
                </select>
            </div>
        </div>
        <ul v-if="filteredTasks.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700/50">
            <li v-for="task in filteredTasks" :key="task.id" class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <p class="text-sm font-medium text-gray-900 dark:text-white flex items-center gap-2">
                    <span v-if="task.is_blocker" class="text-[10px] text-red-500 uppercase tracking-wider font-bold">Blocker</span>
                    {{ task.title }}
                </p>
                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ task.project }}</span>
            </li>
        </ul>
        <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">No active tasks match this filter.</div>
    </div>
</template>
