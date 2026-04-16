<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    activePlan: Object,
    templates: Array,
});

const emit = defineEmits(['openCustomPlan', 'openAddWorkout', 'confirmAbandonPlan', 'openCompleteWorkout']);

const startPlan = (templateId) => {
    useForm({}).post(route('running.plan.start', templateId), {
        preserveScroll: true
    });
};

const seedTemplates = () => {
    useForm({}).post(route('running.seed'), { preserveScroll: true });
};

const deleteWorkout = (workoutId) => {
    if (confirm("Delete this workout?")) {
        useForm({}).delete(route('running.workout.destroy', workoutId), { preserveScroll: true });
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const normalizeDateString = (dateStr) => {
    if (!dateStr) return '';

    if (typeof dateStr === 'string') {
        return dateStr.split('T')[0].split(' ')[0];
    }

    return new Date(dateStr).toISOString().split('T')[0];
};

const updateWorkoutStatus = (workoutId, status) => {
    useForm({ status }).patch(route('running.workout.status', workoutId), { preserveScroll: true });
};

const currentWeek = computed(() => {
    if (!props.activePlan) return null;
    const now = new Date().toISOString().split('T')[0];
    return props.activePlan.weeks.find((week) => {
        return week.start_date && week.end_date && week.start_date <= now && week.end_date >= now;
    }) || props.activePlan.weeks[0] || null;
});

const weekDays = computed(() => {
    if (!currentWeek.value?.start_date) {
        return [];
    }

    const workouts = Array.isArray(currentWeek.value.workouts) ? currentWeek.value.workouts : [];
    const weekStart = new Date(`${currentWeek.value.start_date}T00:00:00`);

    return Array.from({ length: 7 }, (_, index) => {
        const date = new Date(weekStart);
        date.setDate(weekStart.getDate() + index);

        return {
            dayNumber: index + 1,
            date,
            dateString: date.toISOString().split('T')[0],
            workout: workouts.find((workout) => normalizeDateString(workout.scheduled_date) === date.toISOString().split('T')[0]) || null,
        };
    });
});
</script>

<template>
    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient overflow-hidden mt-6">
        <div class="px-8 py-6 border-b border-atlas-border/50 flex justify-between items-center bg-atlas-background/30">
            <h3 class="font-bold text-atlas-text text-sm uppercase tracking-widest flex items-center gap-3">
                <svg class="w-5 h-5 text-atlas-primaryStart" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Training Calendar
            </h3>
            <div v-if="activePlan" class="flex gap-4 items-center">
                <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg bg-atlas-surface border border-atlas-border shadow-sm text-atlas-text">
                    {{ activePlan.name }}
                </span>
                <button @click="emit('confirmAbandonPlan')" class="text-[10px] uppercase font-bold text-red-400 hover:text-red-300 transition-colors">
                    Abandon Plan
                </button>
            </div>
        </div>
        
        <div class="p-8">
            <div v-if="!activePlan" class="text-center py-12">
                <p class="text-atlas-muted text-sm tracking-wide mb-6">You don't have an active training plan.</p>
                <div v-if="templates.length === 0">
                    <button @click="seedTemplates" class="bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd text-atlas-surface px-6 py-3 rounded-xl text-xs font-bold tracking-wider uppercase shadow-ambient transition-transform hover:scale-95">
                        Load Available Plans
                    </button>
                </div>
                <div v-else class="flex flex-col items-center gap-12">
                    <button @click="emit('openCustomPlan')" class="bg-atlas-surface border border-atlas-border text-atlas-text px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm transition-transform hover:scale-95">
                        + Create Custom Plan
                    </button>
                    
                    <div class="flex flex-wrap gap-6 justify-center">
                        <div v-for="t in templates" :key="t.id" class="bg-atlas-panels border border-atlas-border p-6 rounded-2xl w-72 text-left hover:border-atlas-primaryStart/50 transition-colors">
                            <h4 class="font-serif text-xl tracking-tight text-atlas-text mb-2">{{ t.name }}</h4>
                            <p class="text-[10px] uppercase font-bold text-atlas-muted tracking-wide mb-6">{{ t.duration_weeks }} Weeks • {{ String(t.experience_level).toUpperCase() }}</p>
                            <button @click="startPlan(t.id)" class="w-full bg-atlas-surface border border-atlas-border hover:bg-atlas-background text-atlas-text py-2 rounded-lg text-[10px] uppercase font-bold tracking-widest transition-colors shadow-sm">
                                Start Target Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else-if="currentWeek">
                <div class="mb-6">
                    <h4 class="font-serif text-2xl tracking-tight text-atlas-text">Week {{ currentWeek.week_number }}: {{ currentWeek.theme_label || 'Training Block' }}</h4>
                    <p class="text-[10px] uppercase font-bold tracking-widest text-atlas-muted mt-2">{{ formatDate(currentWeek.start_date) }} to {{ formatDate(currentWeek.end_date) }}</p>
                </div>
                <div class="flex gap-5 overflow-x-auto pb-8 pt-2 snap-x snap-mandatory">
                    <div v-for="day in weekDays" :key="day.dateString" class="min-w-[280px] flex-shrink-0 snap-start min-h-[220px] flex flex-col">
                        <div v-if="day.workout" 
                                :class="['h-full border p-6 flex flex-col transition-colors', day.workout.status === 'completed' ? 'bg-atlas-primaryStart/5 border-atlas-primaryStart/40' : 'bg-atlas-panel border-atlas-border shadow-sm']">
                            
                            <template v-for="workout in [day.workout]">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-atlas-muted mb-3 flex justify-between">
                                    <span>{{ new Date(workout.scheduled_date).toLocaleDateString('en-US', {weekday: 'short'}) }}</span>
                                    <div class="flex gap-2">
                                        <span v-if="workout.status === 'completed'" class="text-atlas-primaryStart">✓</span>
                                        <button v-if="workout.status === 'scheduled'" @click.stop="deleteWorkout(workout.id)" class="text-red-400/50 hover:text-red-500 transition-colors">×</button>
                                    </div>
                                </div>
                                <h5 class="font-serif text-base text-atlas-text mb-2 leading-tight tracking-tight">{{ workout.title }}</h5>
                                <p v-if="workout.target_distance_km" class="text-sm font-bold text-atlas-primaryStart mb-3">{{ workout.target_distance_km }} <span class="text-[10px] uppercase tracking-widest text-atlas-muted align-middle">km</span></p>
                                <p v-else-if="workout.target_duration_sec" class="text-sm font-bold text-atlas-primaryStart mb-3">{{ Math.round(workout.target_duration_sec / 60) }} <span class="text-[10px] uppercase tracking-widest text-atlas-muted align-middle">mins</span></p>
                                <p class="text-[10px] font-sans font-medium text-atlas-muted mt-auto leading-relaxed">{{ workout.instructions }}</p>
                                
                                <div v-if="workout.status === 'scheduled'" class="mt-4 flex flex-col gap-2">
                                    <button @click.prevent="emit('openCompleteWorkout', workout)" class="w-full text-[10px] uppercase tracking-widest py-2 rounded bg-atlas-text text-atlas-surface border border-atlas-text font-black hover:opacity-90 transition-opacity">Done</button>
                                    <div class="flex gap-2">
                                        <button @click.prevent="updateWorkoutStatus(workout.id, 'skipped')" class="flex-1 text-[9px] uppercase tracking-[0.2em] py-1.5 border border-atlas-border rounded bg-transparent text-atlas-muted font-bold hover:text-atlas-text hover:border-atlas-text transition-colors">Skip</button>
                                        <button @click.prevent="emit('openAddWorkout', currentWeek.id, workout.scheduled_date, workout)" class="flex-1 text-[9px] uppercase tracking-[0.2em] py-1.5 border border-atlas-border rounded bg-atlas-surface text-atlas-text font-bold hover:border-atlas-text transition-colors">Edit</button>
                                    </div>
                                </div>
                                <div v-else-if="workout.status === 'completed' || workout.status === 'partially_completed'" class="mt-4 flex justify-between items-center gap-2">
                                    <span class="text-[9px] font-bold text-atlas-primaryStart uppercase tracking-[0.2em]">Completed</span>
                                    <button @click.prevent="emit('openAddWorkout', currentWeek.id, workout.scheduled_date, workout)" class="text-[9px] uppercase tracking-[0.2em] py-1 px-3 border border-atlas-primaryStart/30 text-atlas-primaryStart font-bold hover:bg-atlas-primaryStart/10 transition-colors">Edit</button>
                                </div>
                                <div v-else-if="workout.status === 'skipped'" class="mt-4 flex justify-between items-center gap-2">
                                    <span class="text-[10px] font-bold text-atlas-muted uppercase tracking-[0.2em] line-through opacity-50">Skipped</span>
                                    <div class="flex gap-2">
                                        <button @click.prevent="updateWorkoutStatus(workout.id, 'scheduled')" class="text-[9px] uppercase tracking-[0.2em] py-1 px-2 border border-atlas-border bg-transparent text-atlas-muted font-bold hover:text-atlas-text hover:border-atlas-text transition-colors">Undo</button>
                                        <button @click.prevent="emit('openAddWorkout', currentWeek.id, workout.scheduled_date, workout)" class="text-[9px] uppercase tracking-[0.2em] py-1 px-2 border border-atlas-border bg-atlas-surface text-atlas-text font-bold hover:border-atlas-text transition-colors">Edit</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div v-else @click="emit('openAddWorkout', currentWeek.id, day.dateString)" class="h-full border-[1.5px] border-dashed border-atlas-border/50 p-6 flex flex-col justify-center items-center opacity-60 hover:bg-atlas-surface hover:border-solid hover:border-atlas-text hover:opacity-100 cursor-pointer transition-all group">
                            <span class="text-[9px] uppercase tracking-[0.3em] font-bold text-atlas-muted mb-2 group-hover:text-atlas-text transition-colors">{{ day.date.toLocaleDateString('en-US', { weekday: 'short' }) }}</span>
                            <span class="text-xs uppercase tracking-[0.2em] font-bold text-atlas-muted group-hover:text-atlas-text">+ Add Drill</span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="py-12 text-center">
                <p class="text-sm tracking-wide text-atlas-muted">This plan has no scheduled weeks yet.</p>
                <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-atlas-muted">Seed or create workouts to populate the calendar.</p>
            </div>
        </div>
    </div>
</template>
