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
    color: 'atlas',
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
                colors: ['#475569', '#94a3b8', '#cbd5e1']
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
    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient overflow-hidden">
        <div class="px-8 py-6 flex justify-between items-center">
            <h3 class="font-serif text-xl tracking-tight text-atlas-text flex items-center gap-3">
                <svg class="w-5 h-5 text-atlas-primaryStart" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Active Saving Missions
            </h3>
        </div>
        
        <div class="p-8 pt-0 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="goal in goals" :key="goal.id" class="border border-atlas-border rounded-[20px] p-6 hover:bg-atlas-surface/50 transition-colors group relative bg-atlas-surface shadow-sm">
                <button @click="confirmDeleteGoal(goal)" class="absolute top-4 right-4 text-atlas-muted hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <h4 class="font-medium text-sm text-atlas-text mb-1">{{ goal.name }}</h4>
                <div class="flex justify-between items-end mb-4">
                    <div class="text-3xl font-serif text-atlas-text">
                        ${{ goal.current_amount }} <span class="text-sm font-sans tracking-wide text-atlas-muted">/ ${{ goal.target_amount }}</span>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 rounded-md bg-atlas-panel border border-atlas-border text-atlas-muted tracking-widest">
                        {{ Math.round((goal.current_amount / goal.target_amount) * 100) }}%
                    </span>
                </div>
                <div class="w-full bg-atlas-panel border border-atlas-border/50 rounded-full h-1.5 mb-5 overflow-hidden">
                    <div class="bg-atlas-text h-1.5 rounded-full" :style="{ width: Math.min(100, Math.round((goal.current_amount / goal.target_amount) * 100)) + '%' }"></div>
                </div>
                <button v-if="parseFloat(goal.current_amount) >= parseFloat(goal.target_amount) && !goal.is_claimed" @click="claimGoal(goal)" class="w-full py-2.5 bg-gradient-to-r from-atlas-primaryStart to-atlas-primaryEnd text-atlas-surface shadow-lg text-xs tracking-wider font-bold rounded-xl transition-all hover:scale-[0.98]">
                    Claim Success
                </button>
                <button v-else-if="goal.is_claimed" disabled class="w-full py-2.5 bg-atlas-panel text-atlas-muted text-xs tracking-wider rounded-xl cursor-not-allowed">
                    Target Reached
                </button>
                <button v-else @click="addFunds(goal)" class="w-full py-2.5 bg-atlas-panel hover:bg-atlas-background text-atlas-text text-xs tracking-wider font-medium rounded-xl transition-colors border border-atlas-border hover:border-atlas-text/20">
                    + Deposit Funds
                </button>
            </div>
            
            <!-- Add New Goal card -->
            <div class="border border-dashed border-atlas-border/70 p-6 rounded-[20px] flex flex-col justify-center bg-atlas-surface/30">
                <form @submit.prevent="submitGoal" class="space-y-4">
                    <input type="text" v-model="goalForm.name" placeholder="Mission Name" required class="w-full text-sm rounded-xl border-atlas-border bg-atlas-panel text-atlas-text focus:border-atlas-primaryStart focus:ring-0 placeholder:text-atlas-muted/50">
                    <input type="number" v-model="goalForm.target_amount" placeholder="Target Capital" required class="w-full text-sm rounded-xl border-atlas-border bg-atlas-panel text-atlas-text focus:border-atlas-primaryStart focus:ring-0 placeholder:text-atlas-muted/50">
                    <button type="submit" :disabled="goalForm.processing" class="w-full bg-atlas-text text-atlas-surface text-sm tracking-wide font-medium py-2.5 rounded-xl transition-all hover:opacity-90">Design Mission</button>
                </form>
            </div>
        </div>

        <Modal :show="showAddFundsModal" @close="closeAddFundsModal" maxWidth="md">
            <div class="p-8 relative overflow-hidden bg-atlas-panel border border-atlas-border rounded-[24px]">
                <h2 class="text-2xl font-serif text-atlas-text mb-2">
                    Fund {{ activeGoal?.name }}
                </h2>
                <p class="text-sm font-sans text-atlas-muted tracking-wide mb-8">
                    Deposit capital towards this objective.
                </p>
                <form @submit.prevent="confirmAddFunds">
                    <div class="mb-10">
                        <label class="block text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-3">Deposit Amount</label>
                        <div class="relative">
                            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-atlas-muted font-bold">$</span>
                            <input type="number" step="0.01" v-model="addFundsForm.add_amount" required min="0.01" class="w-full text-3xl font-serif rounded-2xl border-atlas-border bg-atlas-surface text-atlas-text pl-12 py-5 focus:border-atlas-primaryStart focus:ring-0 placeholder:text-atlas-border" placeholder="0.00" autofocus>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 flex-col sm:flex-row">
                        <button type="button" @click="closeAddFundsModal" class="px-6 py-3.5 text-sm font-medium tracking-wide text-atlas-muted hover:text-atlas-text transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="addFundsForm.processing" class="px-6 py-3.5 text-sm font-medium tracking-wide text-atlas-surface bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd rounded-xl shadow-ambient transition-all hover:scale-95 disabled:opacity-50">
                            Confirm Deposit
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showDeleteGoalModal" @close="closeDeleteGoalModal" maxWidth="sm">
            <div class="p-8 relative overflow-hidden bg-atlas-panel border border-atlas-border rounded-[24px]">
                <h2 class="text-2xl font-serif text-atlas-text mb-3">
                    Delete Mission
                </h2>
                <p class="text-sm tracking-wide text-atlas-muted leading-relaxed mb-8">
                    Are you sure you want to abandon "{{ goalToDelete?.name }}"? The capital will remain in your ledger.
                </p>
                <div class="flex justify-end gap-3 flex-col sm:flex-row">
                    <button @click="closeDeleteGoalModal" class="px-6 py-3 text-sm tracking-wide font-medium text-atlas-muted hover:text-atlas-text transition-colors">
                        Cancel
                    </button>
                    <button @click="executeDeleteGoal" class="px-6 py-3 text-sm tracking-wide font-medium text-white bg-red-500 hover:bg-red-600 rounded-xl transition-all shadow-sm">
                        Confirm Deletion
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>
