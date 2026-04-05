<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    projects: Array
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <Link v-for="proj in projects" :key="proj.id" :href="route('build.project.show', proj.id)" class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm cursor-pointer hover:border-blue-200 dark:hover:border-blue-900/50 transition-colors group">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">{{ proj.name }}</h3>
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ proj.status }}</span>
            </div>
            
            <div class="mb-2 flex justify-between text-xs font-bold text-gray-400">
                <span>Progress</span>
                <span>{{ proj.progress }}%</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden mb-4">
                <div class="bg-blue-500 h-2 rounded-full" :style="{ width: `${proj.progress}%` }"></div>
            </div>
        </Link>
        <div v-if="projects.length === 0" class="col-span-1 md:col-span-2 text-center text-gray-500 text-sm py-10 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl">
            No active projects. Start building something awesome!
        </div>
    </div>
</template>
