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

const updateWorkoutStatus = (workoutId, status) => {
    useForm({ status }).patch(route('running.workout.status', workoutId), { preserveScroll: true });
};

const currentWeek = computed(() => {
    if (!props.activePlan) return null;
    const now = new Date().toISOString().split('T')[0];
    return props.activePlan.weeks.find(w => w.start_date <= now && w.end_date >= now) || props.activePlan.weeks[0];
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Training Calendar
            </h3>
            <div v-if="activePlan" class="flex gap-4 items-center">
                <span class="text-xs font-bold px-2 py-1 rounded bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400">
                    {{ activePlan.name }}
                </span>
                <button @click="emit('confirmAbandonPlan')" class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors">
                    Abandon Plan
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div v-if="!activePlan" class="text-center py-6">
                <p class="text-gray-500 dark:text-gray-400 mb-4">You don't have an active training plan.</p>
                <div v-if="templates.length === 0">
                    <button @click="seedTemplates" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition-colors hover:bg-gray-800 dark:hover:bg-gray-100">
                        Load Available Plans
                    </button>
                </div>
                <div v-else class="flex flex-col items-center gap-8">
                    <button @click="emit('openCustomPlan')" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-2 rounded-xl text-sm font-bold shadow-sm transition-colors hover:bg-gray-800 dark:hover:bg-gray-100">
                        + Create Custom Plan
                    </button>
                    
                    <div class="flex flex-wrap gap-4 justify-center">
                        <div v-for="t in templates" :key="t.id" class="border border-gray-200 dark:border-gray-700 p-5 rounded-xl w-64 text-left">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-1">{{ t.name }}</h4>
                            <p class="text-xs text-gray-500 mb-4">{{ t.duration_weeks }} Weeks • {{ String(t.experience_level).toUpperCase() }}</p>
                            <button @click="startPlan(t.id)" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                Start Target Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else-if="currentWeek">
                <div class="mb-4">
                    <h4 class="font-bold text-lg text-gray-900 dark:text-white">Week {{ currentWeek.week_number }}: {{ currentWeek.theme_label || 'Training Block' }}</h4>
                    <p class="text-xs text-gray-500">{{ formatDate(currentWeek.start_date) }} to {{ formatDate(currentWeek.end_date) }}</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                    <div v-for="day in 7" :key="day" class="min-h-32">
                        <div v-if="currentWeek.workouts.find(w => new Date(w.scheduled_date).getDay() === (day%7))" 
                                :class="['h-full border p-3 rounded-xl flex flex-col', currentWeek.workouts.find(w => new Date(w.scheduled_date).getDay() === (day%7)).status === 'completed' ? 'bg-orange-50/50 border-orange-200 dark:bg-orange-900/10 dark:border-orange-800/50' : 'bg-white border-gray-100 dark:bg-gray-800 dark:border-gray-700']">
                            
                            <template v-for="workout in [currentWeek.workouts.find(w => new Date(w.scheduled_date).getDay() === (day%7))]">
                                <div class="text-[10px] font-bold uppercase text-gray-400 mb-1 flex justify-between">
                                    <span>{{ new Date(workout.scheduled_date).toLocaleDateString('en-US', {weekday: 'short'}) }}</span>
                                    <div class="flex gap-2">
                                        <span v-if="workout.status === 'completed'" class="text-orange-500">✓</span>
                                        <button v-if="workout.status === 'scheduled'" @click.stop="deleteWorkout(workout.id)" class="text-red-400 hover:text-red-600 transition-colors">×</button>
                                    </div>
                                </div>
                                <h5 class="font-bold text-sm text-gray-900 dark:text-white mb-1 leading-tight">{{ workout.title }}</h5>
                                <p v-if="workout.target_distance_km" class="text-xs font-bold text-orange-600 dark:text-orange-400 mb-2">{{ workout.target_distance_km }} km</p>
                                <p v-else-if="workout.target_duration_sec" class="text-xs font-bold text-orange-600 dark:text-orange-400 mb-2">{{ Math.round(workout.target_duration_sec / 60) }} mins</p>
                                <p class="text-[10px] text-gray-500 mt-auto leading-relaxed">{{ workout.instructions }}</p>
                                
                                <div v-if="workout.status === 'scheduled'" class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 flex justify-between gap-1">
                                    <button @click.prevent="updateWorkoutStatus(workout.id, 'skipped')" class="flex-1 text-[9px] py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Skip</button>
                                    <button @click.prevent="emit('openAddWorkout', currentWeek.id, workout.scheduled_date, workout)" class="flex-1 text-[9px] py-1 rounded bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 font-bold hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-colors">Edit</button>
                                    <button @click.prevent="emit('openCompleteWorkout', workout)" class="flex-1 text-[9px] py-1 rounded bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400 font-bold hover:bg-orange-200 dark:hover:bg-orange-900/60 transition-colors">Done</button>
                                </div>
                                <div v-else-if="workout.status === 'completed' || workout.status === 'partially_completed'" class="mt-2 pt-2 border-t border-orange-100 dark:border-orange-800/30 flex justify-between items-center gap-1">
                                    <span class="text-[9px] font-bold text-orange-500 uppercase tracking-wide">Completed</span>
                                    <button @click.prevent="emit('openAddWorkout', currentWeek.id, workout.scheduled_date, workout)" class="text-[9px] py-1 px-2 rounded bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 font-bold hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-colors">Edit</button>
                                </div>
                                <div v-else-if="workout.status === 'skipped'" class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center gap-1">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wide">Skipped</span>
                                    <div class="flex gap-1">
                                        <button @click.prevent="updateWorkoutStatus(workout.id, 'scheduled')" class="text-[9px] py-1 px-2 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Undo</button>
                                        <button @click.prevent="emit('openAddWorkout', currentWeek.id, workout.scheduled_date, workout)" class="text-[9px] py-1 px-2 rounded bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 font-bold hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-colors">Edit</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div v-else @click="emit('openAddWorkout', currentWeek.id, new Date(new Date(currentWeek.start_date).getTime() + ((day - 1) * 24 * 60 * 60 * 1000)).toISOString().split('T')[0])" class="h-full border border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-3 flex flex-col justify-center items-center opacity-50 hover:bg-gray-50 dark:hover:bg-gray-800 hover:opacity-100 cursor-pointer transition-all">
                            <span class="text-[10px] font-bold uppercase text-gray-400 mb-1">Day {{ day }}</span>
                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500">+ Add</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
