<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import TrackResourceModal from './Components/TrackResourceModal.vue';
import LogSessionModal from './Components/LogSessionModal.vue';

const props = defineProps({
    activeResources: Array,
    revisionQueue: Array,
    levelData: Object,
    recentSessions: Array,
    draftResource: String,
    draftSession: Boolean,
});

const showSessionModal = ref(false);
const showTrackModal = ref(false);
const selectedResource = ref(null);

const resumeSession = (resource) => {
    selectedResource.value = resource;
    showSessionModal.value = true;
};

const resolveBgClass = (type) => {
    const map = {
        book: 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800',
        course: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
        article: 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800',
        podcast: 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800',
        other: 'bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700'
    };
    return map[type] || map.other;
};

onMounted(() => {
    if (props.draftResource) {
        const target = props.activeResources.find(r => r.id == props.draftResource);
        if (target) resumeSession(target);
    } else if (props.draftSession) {
        showTrackModal.value = true;
    }
});
</script>

<template>
    <Head title="Learn | Life OS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center px-2 gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Learn Workspace</h2>
                </div>
            </div>
        </template>

        <div class="space-y-6 sm:space-y-8 px-2 mt-4 sm:mt-6">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                
                <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                    
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">Continuity Queue</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div v-for="resource in activeResources" :key="resource.id" :class="['rounded-2xl border p-6 flex flex-col sm:flex-row gap-6 items-center justify-between shadow-sm cursor-pointer hover:shadow-md transition-all', resolveBgClass(resource.type)]">
                            <div class="flex-1 w-full text-center sm:text-left">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-gray-500 bg-white/50 dark:bg-black/20 px-2 py-1 rounded">{{ resource.type }}</span>
                                <h4 class="font-bold text-gray-900 dark:text-white text-lg mt-2">{{ resource.title }}</h4>
                                <p v-if="resource.current_position_label" class="text-sm text-gray-600 dark:text-gray-400 mt-1">Currently on: <span class="font-bold">{{ resource.current_position_label }}</span></p>
                                
                                <div class="mt-4 flex items-center gap-3">
                                    <template v-if="resource.total_units">
                                        <div class="w-full bg-white/50 dark:bg-black/20 rounded-full h-1.5 flex-1">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" :style="{ width: ((resource.current_unit / resource.total_units) * 100) + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-500">{{ resource.current_unit }} / {{ resource.total_units }} {{ resource.unit_label }}s</span>
                                    </template>
                                    <template v-else>
                                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Activity Summary: {{ resource.current_unit || 0 }} total duration captured.</p>
                                    </template>
                                </div>
                            </div>
                            
                            <button @click.prevent="resumeSession(resource)" class="flex-shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl shadow-sm transition-transform active:scale-95">
                                Resume Session
                            </button>
                        </div>
                    </div>
                    
                    <div @click="showTrackModal = true" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mt-8 hover:bg-gray-50 dark:hover:bg-gray-800/80 cursor-pointer transition-colors flex items-center justify-center p-6 border-dashed group">
                        <span class="text-gray-400 font-bold group-hover:text-emerald-500 transition-colors">+ Track New Resource</span>
                    </div>

                </div>
                
                <div class="space-y-6 sm:space-y-8">
                    <!-- Progress Card -->
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl shadow-sm p-6 text-white text-center">
                        <h3 class="text-emerald-100 text-sm font-bold uppercase tracking-wider mb-2">Module Level</h3>
                        <div class="text-4xl font-black mb-1">{{ levelData.level }}</div>
                        <div class="text-sm text-emerald-100 mb-4">{{ Number(levelData.xp).toLocaleString() }} / {{ Number(levelData.next_level_base).toLocaleString() }} XP</div>
                        <div class="w-full bg-black/20 rounded-full h-1.5 overflow-hidden mb-2">
                            <div class="bg-white h-1.5 rounded-full" :style="{ width: levelData.progress_percent + '%' }"></div>
                        </div>
                    </div>

                    <!-- Recent Sessions -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-4">Recent Sessions</h3>
                        <div class="space-y-3">
                            <div v-for="session in recentSessions" :key="session.id" class="flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="overflow-hidden">
                                     <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ session.resource }}</p>
                                     <p class="text-[10px] text-gray-500 uppercase font-bold">{{ session.date }}</p>
                                </div>
                                <div class="text-xs font-black text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 px-2 py-1 rounded">
                                    +{{ session.duration }}
                                </div>
                            </div>
                            <div v-if="!recentSessions.length" class="text-xs text-gray-500 text-center py-4">No recent sessions found.</div>
                        </div>
                    </div>

                    <!-- Revision Queue -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-4">Revision Queue</h3>
                        <div class="space-y-3">
                            <div v-for="resource in revisionQueue" :key="resource.id" class="flex justify-between items-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-2 rounded transition-colors group" @click="resumeSession(resource)">
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white truncate transition-colors">{{ resource.title }}</p>
                                </div>
                                <div class="text-gray-400 group-hover:text-amber-500 transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                </div>
                            </div>
                            <div v-if="!revisionQueue.length" class="text-xs text-gray-500 text-center py-4">No completed resources available to revise.</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <TrackResourceModal :show="showTrackModal" @close="showTrackModal = false" />
        <LogSessionModal :show="showSessionModal" :resource="selectedResource" @close="showSessionModal = false; selectedResource = null" />

    </AuthenticatedLayout>
</template>
