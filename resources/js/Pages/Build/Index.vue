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
        <template #header>
            <div class="flex justify-between items-center px-2">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Build Workspace</h2>
                </div>
                <div>
                    <button @click="showBlueprintModal = true" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-500 transition-colors">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        New Blueprint
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6 sm:space-y-8 px-2 mt-4 sm:mt-6">
            
            <!-- Contextual KPI Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <dt class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Active Projects</dt>
                    <dd class="mt-2 text-3xl font-black text-gray-900 dark:text-white">{{ activeProjects }}</dd>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <dt class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Pending Tasks</dt>
                    <dd class="mt-2 text-3xl font-black text-blue-600 dark:text-blue-400">{{ tasks.length }}</dd>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <dt class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Urgent Blockers</dt>
                    <dd class="mt-2 text-3xl font-black text-red-500">{{ blockedTasks }}</dd>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                
                <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                    <ActiveTasksList :tasks="tasks" />
                    <ProjectsList :projects="projects" />
                </div>
                
                <div class="space-y-6 sm:space-y-8">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-sm p-6 text-white text-center">
                        <h3 class="text-blue-100 text-sm font-bold uppercase tracking-wider mb-2">Module Level</h3>
                        <div v-if="levelData">
                            <div class="text-4xl font-black mb-1">{{ levelData.level }}</div>
                            <div class="text-sm text-blue-100 mb-4">{{ Number(levelData.xp).toLocaleString() }} / {{ Number(levelData.next_level_base).toLocaleString() }} XP</div>
                            <div class="w-full bg-black/20 rounded-full h-1.5 overflow-hidden mb-2">
                                <div class="bg-white h-1.5 rounded-full" :style="{ width: levelData.progress_percent + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <BlueprintWizardModal :show="showBlueprintModal" @close="showBlueprintModal = false" />
        
    </AuthenticatedLayout>
</template>
