<script setup>
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean
});

const emit = defineEmits(['close']);

const customPlanForm = useForm({
    name: 'My Custom Plan',
    duration_weeks: 4,
    goal_type: 'fitness'
});

const createCustomPlan = () => {
    customPlanForm.post(route('running.custom-plan.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            customPlanForm.reset();
        }
    });
};
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="md">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Create Custom Training Plan</h2>
            <form @submit.prevent="createCustomPlan">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plan Name</label>
                        <input v-model="customPlanForm.name" type="text" class="mt-1 block text-gray-900 dark:text-white w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (Weeks)</label>
                        <input v-model="customPlanForm.duration_weeks" type="number" min="1" max="52" class="mt-1 block text-gray-900 dark:text-white w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" required>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="emit('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" :disabled="customPlanForm.processing" class="px-5 py-2 text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors shadow-sm disabled:opacity-50">Launch Plan</button>
                </div>
            </form>
        </div>
    </Modal>
</template>
