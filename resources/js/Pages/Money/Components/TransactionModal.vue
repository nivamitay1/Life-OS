<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
    duplicateEntry: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
    amount: '',
    description: '',
    type: 'expense',
    date: new Date().toISOString().split('T')[0],
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        if (props.duplicateEntry) {
            form.amount = Math.abs(props.duplicateEntry.amount);
            form.description = props.duplicateEntry.description;
            form.type = props.duplicateEntry.type;
            form.date = new Date().toISOString().split('T')[0];
        } else {
            form.reset();
            form.date = new Date().toISOString().split('T')[0];
            // Memory preference: Keep the last used type as a smart default (if we had localstorage, we'd pull it here. For now, default to previously committed form type naturally).
        }
    }
});

const submit = () => {
    form.post(route('money.store'), {
        onSuccess: () => {
            form.reset('amount', 'description');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ duplicateEntry ? 'Duplicate Transaction' : 'Record Transaction' }}
                </h2>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Type</label>
                        <select v-model="form.type" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm text-gray-900 dark:text-white">
                            <option value="expense">Expense</option>
                            <option value="income">Income</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Amount</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" step="0.01" v-model="form.amount" required class="w-full pl-7 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-bold placeholder-gray-300 dark:placeholder-gray-500 text-gray-900 dark:text-white" placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Merchant / Description</label>
                    <input type="text" v-model="form.description" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm text-gray-900 dark:text-white" placeholder="Grocery, Salary, etc.">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Date</label>
                    <input type="date" v-model="form.date" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm text-gray-900 dark:text-white">
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" @click="emit('close')" class="px-4 py-2 font-bold text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-xl text-sm font-bold tracking-wide transition-colors shadow-sm disabled:opacity-50">
                        Confirm & Save
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
