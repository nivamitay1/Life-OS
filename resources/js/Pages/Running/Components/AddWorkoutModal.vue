<script setup>
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    weekId: Number,
    dateStr: String,
    editWorkout: { type: Object, default: null }
});

const emit = defineEmits(['close']);

const workoutForm = useForm({
    scheduled_date: '',
    workout_type: 'easy',
    title: '',
    target_distance_km: '',
    target_duration_sec: '',
    instructions: ''
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        if (props.editWorkout) {
            workoutForm.scheduled_date = props.editWorkout.scheduled_date ? props.editWorkout.scheduled_date.split('T')[0].split(' ')[0] : '';
            workoutForm.workout_type = props.editWorkout.workout_type || 'easy';
            workoutForm.title = props.editWorkout.title || '';
            workoutForm.target_distance_km = props.editWorkout.target_distance_km || '';
            workoutForm.target_duration_sec = props.editWorkout.target_duration_sec || '';
            workoutForm.instructions = props.editWorkout.instructions || '';
        } else if (props.dateStr) {
            workoutForm.scheduled_date = props.dateStr;
        }
    } else {
        workoutForm.reset();
    }
});

const submitWorkout = () => {
    if (props.editWorkout) {
        workoutForm.patch(route('running.workout.update', props.editWorkout.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
                workoutForm.reset();
            }
        });
    } else {
        workoutForm.post(route('running.workout.store', props.weekId), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
                workoutForm.reset();
            }
        });
    }
};
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="md">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ editWorkout ? 'Edit Workout' : 'Schedule Workout' }}</h2>
            <form @submit.prevent="submitWorkout">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input v-model="workoutForm.title" type="text" class="mt-1 block text-gray-900 dark:text-white w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" placeholder="e.g. 5K Tempo" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <select v-model="workoutForm.workout_type" class="mt-1 block text-gray-900 dark:text-white w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                            <option value="easy">Easy</option>
                            <option value="workout">Workout / Speed</option>
                            <option value="long">Long Run</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                        <input v-model="workoutForm.scheduled_date" type="date" class="mt-1 block text-gray-900 dark:text-white w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target Distance (km)</label>
                        <input v-model="workoutForm.target_distance_km" type="number" step="0.1" class="mt-1 block text-gray-900 dark:text-white w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target Duration (sec)</label>
                        <input v-model="workoutForm.target_duration_sec" type="number" step="60" class="mt-1 block text-gray-900 dark:text-white w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instructions</label>
                        <textarea v-model="workoutForm.instructions" rows="2" class="mt-1 block text-gray-900 dark:text-white w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="emit('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" :disabled="workoutForm.processing" class="px-5 py-2 text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors shadow-sm disabled:opacity-50">
                        {{ editWorkout ? 'Save Changes' : 'Schedule' }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
