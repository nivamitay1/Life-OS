<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
    resource: Object, // The selected resource or null
});

const emit = defineEmits(['close']);

const isConfirmingCompletion = ref(false);

const form = useForm({
    learn_resource_id: '',
    duration_minutes: 30,
    units_completed: null,
    ending_position_label: '',
    notes: '',
    date: new Date().toISOString().split('T')[0],
});

const completeForm = useForm({
    reflection_notes: '',
    date: new Date().toISOString().split('T')[0],
});

watch(() => props.resource, (newVal) => {
    if (newVal) {
        form.learn_resource_id = newVal.id;
        form.ending_position_label = newVal.current_position_label || '';
    }
}, { immediate: true });

watch(() => props.show, (newVal) => {
    if (!newVal) {
        setTimeout(() => {
            isConfirmingCompletion.value = false;
            form.reset();
            completeForm.reset();
        }, 300);
    }
});

const submitSession = () => {
    // Predict if this hits completion
    let willComplete = false;
    if (props.resource && props.resource.total_units) {
        const totalNow = (props.resource.current_unit || 0) + (form.units_completed || 0);
        if (totalNow >= props.resource.total_units) {
            willComplete = true;
        }
    }

    form.post(route('learn.session.store'), {
        preserveScroll: true,
        onSuccess: () => {
            if (willComplete) {
                isConfirmingCompletion.value = true;
                completeForm.date = form.date;
            } else {
                emit('close');
            }
        }
    });
};

const submitCompletion = () => {
    completeForm.post(route('learn.resource.complete', props.resource.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        }
    });
};

const skipCompletion = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="sm">
        <div v-if="resource" class="p-6">
            <template v-if="!isConfirmingCompletion">
                <form @submit.prevent="submitSession">
                    <div class="text-center mb-6">
                        <span class="inline-block p-3 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 mb-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Log Study Session</h2>
                        <p class="text-xs text-emerald-600 font-bold mt-1">{{ resource.title }}</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-1/2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Date</label>
                                <input v-model="form.date" type="date" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-sm text-gray-900 dark:text-white">
                            </div>
                            <div class="w-1/2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Time Spent (mins) <span class="text-red-500">*</span></label>
                                <input v-model="form.duration_minutes" type="number" required min="1" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-center font-bold text-lg text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div v-if="resource.total_units" class="bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                             <div class="flex justify-between items-center mb-1">
                                 <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ resource.unit_label }}s Read/Done</label>
                                 <span class="text-xs text-gray-400">Current: {{ resource.current_unit }} / {{ resource.total_units }}</span>
                             </div>
                             <input v-model="form.units_completed" type="number" min="0" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-sm text-gray-900 dark:text-white" placeholder="e.g. 15">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Current Location (Optional)</label>
                            <input v-model="form.ending_position_label" type="text" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-sm text-gray-900 dark:text-white" placeholder="e.g. Chapter 6: The Core">
                        </div>

                        <div v-if="form.duration_minutes < 10" class="mt-2 text-[10px] text-amber-600 bg-amber-50 dark:bg-amber-900/20 dark:text-amber-400 p-2 rounded-lg font-bold">
                            Notice: Sessions under 10 minutes do not yield XP bonuses.
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="emit('close')" class="px-4 py-3 text-sm font-medium text-gray-500 hover:text-gray-900 w-1/3">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="w-2/3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-sm transition-colors disabled:opacity-50">
                            Log Progress
                        </button>
                    </div>
                </form>
            </template>
            
            <!-- Phase 2: Completion Intercept -->
            <template v-else>
                <form @submit.prevent="submitCompletion">
                    <div class="text-center mb-6">
                        <div class="mx-auto w-16 h-16 bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full flex items-center justify-center mb-4">
                            🏆
                        </div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">Project Conquered!</h2>
                        <p class="text-sm text-gray-500 mt-2">You reached the finish line for <span class="font-bold text-gray-800 dark:text-gray-200">{{ resource.title }}</span>.</p>
                        <p class="text-xs text-yellow-600 font-bold mt-1">+50 XP Bonus Available</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Final Reflection (Optional)</label>
                            <textarea v-model="completeForm.reflection_notes" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-sm text-gray-900 dark:text-white" placeholder="What were your biggest takeaways?"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        <button type="submit" :disabled="completeForm.processing" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-black uppercase tracking-wider py-4 rounded-xl shadow-md transition-colors disabled:opacity-50">
                            Confirm Completion
                        </button>
                        <button type="button" @click="skipCompletion" class="text-xs font-bold text-gray-400 hover:text-yellow-500 transition-colors uppercase">
                            Not Yet (Keep Active)
                        </button>
                    </div>
                </form>
            </template>
        </div>
        <div v-else class="p-6 text-center text-gray-500">
            No resource selected.
        </div>
    </Modal>
</template>
