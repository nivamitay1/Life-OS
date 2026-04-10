<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

import ProjectsList from './Components/ProjectsList.vue';
import ActiveTasksList from './Components/ActiveTasksList.vue';
import BlueprintWizardModal from './Components/BlueprintWizardModal.vue';

const props = defineProps({
  projects: Array,
  tasks: Array,
  logs: Array,
  levelData: Object,
});

const showBlueprintModal = ref(false);

const activeProjects = computed(() => props.projects.filter(p => p.status === 'active').length);
const blockedTasks = computed(() => props.tasks.filter(t => t.is_blocker && t.status !== 'done').length);

</script>

<template>
    <Head title="Build | Life OS" />

    <AuthenticatedLayout>
        <div class="space-y-6 sm:space-y-8 px-2 mt-2">
            
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-[10px] uppercase tracking-[0.2em] font-bold text-atlas-muted border border-atlas-border px-4 py-2 rounded-lg bg-atlas-panel shadow-sm">Build Workspace</h1>
                
                <button @click="showBlueprintModal = true" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd px-5 py-2 text-xs font-bold tracking-wider text-atlas-surface shadow-ambient hover:scale-95 transition-transform">
                    + New Blueprint
                </button>
            </div>

            <!-- Contextual KPI Stats -->
            <div class="flex flex-wrap gap-x-16 gap-y-8 px-4 items-center">
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Active Projects</dt>
                    <dd class="text-3xl font-serif text-atlas-text">{{ activeProjects }}</dd>
                </div>
                <div class="w-px h-12 bg-atlas-border/50 hidden md:block"></div>
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Pending Tasks</dt>
                    <dd class="text-3xl font-serif text-atlas-text">{{ tasks.length }}</dd>
                </div>
                <div class="w-px h-12 bg-atlas-border/50 hidden md:block"></div>
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Urgent Blockers</dt>
                    <dd class="text-3xl font-serif text-atlas-text opacity-70">{{ blockedTasks }}</dd>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 pt-6">
                
                <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                    <ActiveTasksList :tasks="tasks" />
                    <ProjectsList :projects="projects" />
                </div>
                
                <div class="space-y-6 sm:space-y-8">
                    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient p-8 text-center flex flex-col items-center">
                        <h3 class="text-atlas-muted text-[10px] font-bold uppercase tracking-widest mb-3">Build Mastery</h3>
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
        
        <BlueprintWizardModal :show="showBlueprintModal" @close="showBlueprintModal = false" />
        
    </AuthenticatedLayout>
</template>
