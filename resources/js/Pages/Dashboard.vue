<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    priorities: Array,
    suggestedAction: Object,
    badges: Array,
    streaks: Object,
    stats: Object,
});

const showAllPriorities = ref(false);
const visiblePriorities = computed(() => {
    return showAllPriorities.value ? props.priorities : props.priorities.slice(0, 4);
});
</script>

<template>
    <Head title="Command Center | Life OS" />

    <AuthenticatedLayout>
        <div class="space-y-8 mt-4">
            
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <h1 class="text-[10px] uppercase tracking-[0.2em] font-bold text-atlas-muted border border-atlas-border px-4 py-2 rounded-lg bg-atlas-panel shadow-sm">Command Center</h1>
                    <div class="flex items-center gap-3 opacity-80">
                        <span class="text-[10px] font-bold text-atlas-text uppercase tracking-widest">Level {{ stats.overallLevel }}</span>
                        <div class="w-1 h-1 bg-atlas-border rounded-full"></div>
                        <span class="text-xs text-atlas-muted font-sans font-medium">{{ Number(stats.overallXp).toLocaleString() }} XP</span>
                    </div>
                </div>
                
                <div v-if="suggestedAction" class="flex-shrink-0">
                    <Link :href="suggestedAction.url" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd px-5 py-2 text-xs font-bold tracking-wider text-atlas-surface shadow-ambient hover:scale-95 transition-transform">
                        Next Action &rarr;
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Main Activity Column -->
                <div class="md:col-span-2 space-y-8">
                    
                    <!-- Action Feed -->
                    <div class="bg-atlas-panel backdrop-blur-2xl rounded-[24px] shadow-ambient border border-atlas-border overflow-hidden">
                        <div class="px-8 py-6 border-b border-atlas-border/50 flex justify-between items-center">
                            <h3 class="font-serif text-xl tracking-tight text-atlas-text flex items-center gap-3">
                                Today's Intentions
                            </h3>
                        </div>
                        <ul v-if="visiblePriorities.length > 0" class="divide-y divide-atlas-border/30">
                            <li v-for="priority in visiblePriorities" :key="priority.id" class="px-8 py-5 flex items-center justify-between hover:bg-atlas-background/50 transition-colors group">
                                <div class="flex items-center gap-5">
                                    <div :class="['w-1.5 h-1.5 rounded-full', priority.isOverdue ? 'bg-red-500/80 shadow-[0_0_8px_rgba(239,68,68,0.5)]' : 'bg-atlas-primaryStart/80 shadow-[0_0_8px_rgba(148,163,184,0.5)]']"></div>
                                    <div>
                                        <p :class="[priority.isOverdue ? 'text-atlas-text font-medium' : 'text-atlas-text font-medium', 'text-base']">
                                            {{ priority.text }}
                                        </p>
                                        <p class="text-xs text-atlas-muted mt-1 tracking-wide">{{ priority.isOverdue ? 'Overdue' : 'Due Today' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-atlas-surface text-atlas-muted border border-atlas-border/50">
                                        {{ priority.module }}
                                    </span>
                                    <Link :href="priority.url" class="opacity-0 group-hover:opacity-100 transition-opacity text-sm font-medium text-atlas-text hover:text-atlas-primaryStart">Execute &rarr;</Link>
                                </div>
                            </li>
                        </ul>
                        <div v-else class="p-12 text-center">
                            <p class="font-serif text-2xl text-atlas-muted mb-2 italic">A serene canvas.</p>
                            <p class="text-sm text-atlas-muted/70">You have no pending priorities designed for today.</p>
                        </div>
                        <div v-if="priorities.length > 4" class="border-t border-atlas-border/30 p-4 text-center">
                            <button @click="showAllPriorities = !showAllPriorities" class="text-sm tracking-wide text-atlas-muted hover:text-atlas-text transition-colors">
                                {{ showAllPriorities ? 'Collapse' : `View all ${priorities.length} intentions` }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Secondary Context Column -->
                <div class="space-y-8">
                    
                    <!-- Streaks Widget -->
                    <div class="bg-atlas-panel border border-orange-500/20 backdrop-blur-2xl rounded-[24px] shadow-ambient p-8 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 bg-orange-500/5 w-32 h-32 rounded-full group-hover:scale-150 transition-transform duration-700 ease-out"></div>
                        <div class="flex justify-between items-center z-10 relative">
                            <div>
                                <h3 class="text-orange-600/80 text-[10px] uppercase tracking-widest font-bold">Active Momentum</h3>
                                <div class="text-4xl font-serif text-atlas-text mt-2 flex items-baseline gap-2">
                                    {{ streaks.current }} <span class="text-sm font-sans tracking-wide text-atlas-muted">days</span>
                                </div>
                            </div>
                            <div class="text-4xl filter drop-shadow-md opacity-90">
                                🔥
                            </div>
                        </div>
                    </div>

                    <!-- Recent Wins (Badges) -->
                    <div class="bg-atlas-panel backdrop-blur-2xl rounded-[24px] shadow-ambient border border-atlas-border p-8">
                        <div class="mb-6">
                            <h3 class="font-serif text-xl tracking-tight text-atlas-text">Milestones</h3>
                        </div>
                        <div class="space-y-4">
                            <div v-for="badge in badges" :key="badge.name" class="flex items-start gap-4 group">
                                <div :class="[badge.color, 'h-10 w-10 rounded-2xl flex items-center justify-center text-xl bg-atlas-surface border border-atlas-border shadow-sm group-hover:-translate-y-0.5 transition-transform']">
                                    {{ badge.icon }}
                                </div>
                                <div class="pt-0.5">
                                    <h4 class="font-medium text-sm text-atlas-text leading-tight">{{ badge.name }}</h4>
                                    <div class="text-[10px] uppercase tracking-wider text-atlas-muted mt-1">{{ badge.module }} &bull; {{ badge.date }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
