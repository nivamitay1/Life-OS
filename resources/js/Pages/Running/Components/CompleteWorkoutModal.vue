<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
    workout: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    distance: '',
    duration: '',
    pace: '',
    effort: '5',
    type: 'easy',
    notes: '',
    date: new Date().toISOString().split('T')[0],
    plan_workout_id: null,
});

watch(() => props.show, (newVal) => {
    if (newVal && props.workout) {
        form.name = props.workout.title;
        form.date = new Date().toISOString().split('T')[0];
        form.plan_workout_id = props.workout.id;
        form.distance = props.workout.target_distance_km ? String(props.workout.target_distance_km) : '';
        form.duration = props.workout.target_duration_sec ? String(Math.round(props.workout.target_duration_sec / 60)) : '';
        if (props.workout.title.toLowerCase().includes('long')) form.type = 'long';
        else if (props.workout.title.toLowerCase().includes('workout') || props.workout.title.toLowerCase().includes('interval')) form.type = 'workout';
        else form.type = 'easy';

        // Pre-fill notes line 1 with title
        form.notes = props.workout.title + '\n';
    }
});

const exactMatch = () => {
    // If the user hit "Matched Plan Exactly", submit it immediately
    form.transform((data) => ({
        ...data,
        duration: data.duration && !String(data.duration).includes(':') ? `${data.duration}:00` : String(data.duration),
    })).post(route('running.store'), {
        onSuccess: () => {
            emit('close');
        },
    });
};

const submitEdited = () => {
    form.transform((data) => ({
        ...data,
        duration: data.duration && !String(data.duration).includes(':') ? `${data.duration}:00` : String(data.duration),
    })).post(route('running.store'), {
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5 border-b border-gray-100 dark:border-gray-800 pb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Complete Workout</h2>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="bg-orange-50/50 dark:bg-orange-900/10 p-4 rounded-xl border border-orange-100 dark:border-orange-800/50 mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400 mb-1">Target Values</h3>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ workout?.title }}</p>
                    <p class="text-xs text-gray-500">{{ workout?.target_distance_km ? workout?.target_distance_km + ' km' : '' }} {{ workout?.target_duration_sec ? Math.round(workout?.target_duration_sec/60) + ' mins' : '' }}</p>
                </div>
                <button type="button" @click="exactMatch" :disabled="form.processing" class="flex-shrink-0 bg-transparent hover:bg-orange-600 text-orange-600 hover:text-white border-2 border-orange-600 font-bold px-4 py-2 rounded-lg text-xs tracking-wide transition-colors whitespace-nowrap disabled:opacity-50">
                    Match Exactly
                </button>
            </div>
            
            <form @submit.prevent="submitEdited" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Actual Distance</label>
                        <div class="relative">
                            <input type="number" step="0.01" v-model="form.distance" class="w-full pr-8 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 font-bold placeholder-gray-300 dark:placeholder-gray-500 text-gray-900 dark:text-white">
                            <span class="absolute inset-y-0 right-3 flex items-center text-xs font-bold text-gray-400">km</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Actual Time</label>
                        <div class="relative">
                            <input type="number" step="1" v-model="form.duration" class="w-full pr-10 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 font-bold placeholder-gray-300 dark:placeholder-gray-500 text-gray-900 dark:text-white">
                            <span class="absolute inset-y-0 right-3 flex items-center text-xs font-bold text-gray-400">min</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pace / Extra</label>
                        <input type="text" v-model="form.pace" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm placeholder-gray-400 text-gray-900 dark:text-white" placeholder="5:30/km">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Type</label>
                        <select v-model="form.type" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm font-bold text-gray-900 dark:text-white">
                            <option value="easy">Easy</option>
                            <option value="workout">Workout</option>
                            <option value="long">Long Run</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 flex justify-end gap-3">
                    <button type="submit" :disabled="form.processing" class="w-full bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-sm disabled:opacity-50">
                        Confirm Modified Run
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
