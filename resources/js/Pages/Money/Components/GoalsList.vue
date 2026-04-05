<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import confetti from 'canvas-confetti';

const props = defineProps({
    goals: Array
});

const goalForm = useForm({
    name: '',
    target_amount: '',
    target_date: '',
    color: 'emerald',
});

const submitGoal = () => {
    goalForm.post(route('money.goal.store'), {
        onSuccess: () => goalForm.reset(),
    });
};

const addFundsForm = useForm({
    add_amount: '',
});

const showAddFundsModal = ref(false);
const activeGoal = ref(null);

const addFunds = (goal) => {
    activeGoal.value = goal;
    addFundsForm.add_amount = '';
    showAddFundsModal.value = true;
};

const confirmAddFunds = () => {
    if (activeGoal.value) {
        addFundsForm.patch(route('money.goal.update', activeGoal.value.id), {
            preserveScroll: true,
            onSuccess: () => closeAddFundsModal()
        });
    }
};

const closeAddFundsModal = () => {
    showAddFundsModal.value = false;
    activeGoal.value = null;
    addFundsForm.reset();
};

const claimGoal = (goal) => {
    useForm({}).post(route('money.goal.claim', goal.id), {
        preserveScroll: true,
        onSuccess: () => {
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b']
            });
        }
    });
};

const showDeleteGoalModal = ref(false);
const goalToDelete = ref(null);

const confirmDeleteGoal = (goal) => {
    goalToDelete.value = goal;
    showDeleteGoalModal.value = true;
};

const executeDeleteGoal = () => {
    if (goalToDelete.value) {
        useForm({}).delete(route('money.goal.destroy', goalToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeDeleteGoalModal()
        });
    }
};

const closeDeleteGoalModal = () => {
    showDeleteGoalModal.value = false;
    goalToDelete.value = null;
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Active Savings Goals
            </h3>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="goal in goals" :key="goal.id" class="border border-gray-100 dark:border-gray-700 rounded-xl p-5 hover:border-emerald-200 dark:hover:border-emerald-900/50 transition-colors group relative bg-white dark:bg-gray-800">
                <button @click="confirmDeleteGoal(goal)" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <h4 class="font-bold text-gray-900 dark:text-white mb-1">{{ goal.name }}</h4>
                <div class="flex justify-between items-end mb-2">
                    <div class="text-2xl font-black text-gray-900 dark:text-white">
                        ${{ goal.current_amount }} <span class="text-sm font-medium text-gray-400">/ ${{ goal.target_amount }}</span>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        {{ Math.round((goal.current_amount / goal.target_amount) * 100) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 mb-4 overflow-hidden">
                    <div :class="`bg-${goal.color}-500 h-2 rounded-full`" :style="{ width: Math.min(100, Math.round((goal.current_amount / goal.target_amount) * 100)) + '%' }"></div>
                </div>
                <button v-if="parseFloat(goal.current_amount) >= parseFloat(goal.target_amount) && !goal.is_claimed" @click="claimGoal(goal)" class="w-full py-2 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white shadow-lg shadow-amber-500/30 text-xs font-bold rounded-lg transition-all animate-pulse">
                    ðŸŽ‰ Claim Reward
                </button>
                <button v-else-if="goal.is_claimed" disabled class="w-full py-2 bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 text-xs font-bold rounded-lg cursor-not-allowed">
                    Target Reached
                </button>
                <button v-else @click="addFunds(goal)" class="w-full py-2 bg-gray-50 hover:bg-emerald-50 text-gray-600 hover:text-emerald-600 dark:bg-gray-700 dark:hover:bg-emerald-900/30 dark:text-gray-300 dark:hover:text-emerald-400 text-xs font-bold rounded-lg transition-colors border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800">
                    + Add Funds
                </button>
            </div>
            
            <!-- Add New Goal card -->
            <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 p-5 rounded-xl flex flex-col justify-center">
                <form @submit.prevent="submitGoal" class="space-y-3">
                    <input type="text" v-model="goalForm.name" placeholder="Goal Name (e.g. New PC)" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white">
                    <input type="number" v-model="goalForm.target_amount" placeholder="Target Amount" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white">
                    <select v-model="goalForm.color" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-600 dark:text-gray-400 border-none outline-none ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-emerald-500">
                        <option value="emerald">Emerald Green</option>
                        <option value="blue">Ocean Blue</option>
                        <option value="indigo">Deep Indigo</option>
                        <option value="purple">Royal Purple</option>
                        <option value="orange">Sunset Orange</option>
                    </select>
                    <button type="submit" :disabled="goalForm.processing" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-bold py-2.5 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">Create Goal</button>
                </form>
            </div>
        </div>

        <Modal :show="showAddFundsModal" @close="closeAddFundsModal" maxWidth="md">
            <div class="p-6 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 bg-emerald-500/10 w-40 h-40 rounded-full blur-2xl pointer-events-none"></div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-1">
                    Fund {{ activeGoal?.name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Level up your Money module
                </p>
                <form @submit.prevent="confirmAddFunds">
                    <div class="mb-8">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Amount to Add</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                            <input type="number" step="0.01" v-model="addFundsForm.add_amount" required min="0.01" class="w-full text-2xl font-black rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 shadow-inner focus:border-emerald-500 focus:ring-emerald-500 text-gray-900 dark:text-white pl-8 py-4 transition-colors placeholder:text-gray-300 dark:placeholder:text-gray-700" placeholder="0.00" autofocus>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeAddFundsModal" class="px-5 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="addFundsForm.processing" class="px-6 py-3 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl tracking-wide transition-colors shadow-sm shadow-emerald-600/20 disabled:opacity-50 flex items-center gap-2 relative overflow-hidden group/btn">
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300"></div>
                            <span class="relative">Deposit Funds</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showDeleteGoalModal" @close="closeDeleteGoalModal" maxWidth="sm">
            <div class="p-6 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 bg-red-500/10 w-40 h-40 rounded-full blur-2xl pointer-events-none"></div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2">
                    Delete Savings Goal
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Are you sure you want to delete "{{ goalToDelete?.name }}"? This action cannot be undone, though the funds will remain in your transaction ledger.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="closeDeleteGoalModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button @click="executeDeleteGoal" class="px-5 py-2.5 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors shadow-sm shadow-red-600/20">
                        Delete Goal
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>
