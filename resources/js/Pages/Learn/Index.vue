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
        <div class="space-y-6 sm:space-y-8 px-2 mt-2">
            
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-[10px] uppercase tracking-[0.2em] font-bold text-atlas-muted border border-atlas-border px-4 py-2 rounded-lg bg-atlas-panel shadow-sm">Learn Workspace</h1>
                
                <button @click="showTrackModal = true" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd px-5 py-2 text-xs font-bold tracking-wider text-atlas-surface shadow-ambient hover:scale-95 transition-transform">
                    + Track Resource
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 pt-6">
                
                <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                    
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-atlas-muted mb-4 ml-1">Continuity Queue</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div v-for="resource in activeResources" :key="resource.id" class="rounded-[24px] bg-atlas-panel border border-atlas-border p-6 flex flex-col sm:flex-row gap-6 items-center justify-between shadow-sm cursor-pointer hover:shadow-md transition-all">
                            <div class="flex-1 w-full text-center sm:text-left">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-atlas-muted border border-atlas-border bg-atlas-surface px-2 py-1 rounded">{{ resource.type }}</span>
                                <h4 class="font-serif text-atlas-text text-xl mt-3 tracking-tight">{{ resource.title }}</h4>
                                <p v-if="resource.current_position_label" class="text-sm text-atlas-muted mt-1">Currently on: <span class="font-bold text-atlas-text">{{ resource.current_position_label }}</span></p>
                                
                                <div class="mt-4 flex items-center gap-3">
                                    <template v-if="resource.total_units">
                                        <div class="w-full bg-atlas-surface border border-atlas-border/50 rounded-full h-1.5 flex-1">
                                            <div class="bg-atlas-text h-1.5 rounded-full" :style="{ width: ((resource.current_unit / resource.total_units) * 100) + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold text-atlas-muted">{{ resource.current_unit }} / {{ resource.total_units }} {{ resource.unit_label }}s</span>
                                    </template>
                                    <template v-else>
                                        <p class="text-[10px] uppercase font-bold text-atlas-muted tracking-widest">Activity Summary: <span class="text-atlas-text">{{ resource.current_unit || 0 }}</span> duration</p>
                                    </template>
                                </div>
                            </div>
                            
                            <button @click.prevent="resumeSession(resource)" class="flex-shrink-0 bg-atlas-surface border border-atlas-border hover:bg-atlas-background text-atlas-text text-xs tracking-wider font-bold py-3 px-8 rounded-xl shadow-sm transition-transform active:scale-95">
                                Resume Session
                            </button>
                        </div>
                    </div>

                </div>
                
                <div class="space-y-6 sm:space-y-8">
                    <!-- Progress Card -->
                    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient p-8 text-center flex flex-col items-center">
                        <h3 class="text-atlas-muted text-[10px] font-bold uppercase tracking-widest mb-3">Learn Mastery</h3>
                        <div v-if="levelData" class="w-full">
                            <div class="text-5xl font-serif text-atlas-text mb-2">{{ levelData.level }}</div>
                            <div class="text-xs tracking-wide text-atlas-muted mb-6">{{ Number(levelData.xp).toLocaleString() }} / {{ Number(levelData.next_level_base).toLocaleString() }} XP</div>
                            <div class="w-full bg-atlas-surface border border-atlas-border/50 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-atlas-text h-1.5 rounded-full" :style="{ width: levelData.progress_percent + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Sessions -->
                    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient p-6">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-atlas-muted mb-4">Recent Sessions</h3>
                        <div class="space-y-3">
                            <div v-for="session in recentSessions" :key="session.id" class="flex justify-between items-center bg-atlas-surface p-3 rounded-xl border border-atlas-border">
                                <div class="overflow-hidden">
                                     <p class="text-xs font-bold text-atlas-text truncate">{{ session.resource }}</p>
                                     <p class="text-[10px] text-atlas-muted uppercase font-bold mt-0.5">{{ session.date }}</p>
                                </div>
                                <div class="text-xs font-black text-atlas-surface bg-atlas-text px-2 py-1 rounded">
                                    +{{ session.duration }}
                                </div>
                            </div>
                            <div v-if="!recentSessions.length" class="text-xs text-atlas-muted text-center py-4">No recent sessions found.</div>
                        </div>
                    </div>

                    <!-- Revision Queue -->
                    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient p-6">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-atlas-muted mb-4">Revision Queue</h3>
                        <div class="space-y-3">
                            <div v-for="resource in revisionQueue" :key="resource.id" class="flex justify-between items-center cursor-pointer bg-atlas-surface hover:bg-atlas-background border border-transparent hover:border-atlas-border p-3 rounded-xl transition-all group" @click="resumeSession(resource)">
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-atlas-muted group-hover:text-atlas-text truncate transition-colors">{{ resource.title }}</p>
                                </div>
                                <div class="text-atlas-muted/30 group-hover:text-atlas-primaryStart transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                </div>
                            </div>
                            <div v-if="!revisionQueue.length" class="text-xs text-atlas-muted text-center py-4">No completed resources available to revise.</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <TrackResourceModal :show="showTrackModal" @close="showTrackModal = false" />
        <LogSessionModal :show="showSessionModal" :resource="selectedResource" @close="showSessionModal = false; selectedResource = null" />

    </AuthenticatedLayout>
</template>
