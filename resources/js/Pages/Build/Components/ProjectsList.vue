<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    projects: Array
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
        <Link v-for="proj in projects" :key="proj.id" :href="route('build.project.show', proj.id)" class="bg-atlas-panel backdrop-blur-2xl rounded-[24px] p-8 border border-atlas-border shadow-ambient cursor-pointer hover:-translate-y-1 transition-all group">
            <div class="flex justify-between items-start mb-6">
                <h3 class="font-serif text-xl text-atlas-text group-hover:text-atlas-primaryStart transition-colors tracking-tight">{{ proj.name }}</h3>
                <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded bg-atlas-surface border border-atlas-border text-atlas-muted">{{ proj.status }}</span>
            </div>
            
            <div class="mb-3 flex justify-between items-end">
                <span class="text-[10px] uppercase tracking-widest font-bold text-atlas-muted">Progress</span>
                <span class="text-sm font-serif text-atlas-text">{{ proj.progress }}%</span>
            </div>
            <div class="w-full bg-atlas-surface border border-atlas-border/50 rounded-full h-1.5 overflow-hidden">
                <div class="bg-atlas-text h-1.5 rounded-full" :style="{ width: `${proj.progress}%` }"></div>
            </div>
        </Link>
        <div v-if="projects.length === 0" class="col-span-1 md:col-span-2 text-center text-gray-500 text-sm py-10 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl">
            No active projects. Start building something awesome!
        </div>
    </div>
</template>
