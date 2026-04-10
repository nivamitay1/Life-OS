<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    subscriptions: Array
});

const subForm = useForm({
    name: '',
    amount: '',
    billing_cycle: 'monthly',
    next_billing_date: new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().split('T')[0]
});

const submitSubscription = () => {
    subForm.post(route('money.subscription.store'), {
        preserveScroll: true,
        onSuccess: () => subForm.reset('name', 'amount'),
    });
};

const editingId = ref(null);
const editForm = useForm({
    name: '',
    amount: '',
    billing_cycle: '',
    next_billing_date: ''
});

const startEdit = (sub) => {
    editingId.value = sub.id;
    editForm.name = sub.name;
    editForm.amount = sub.amount;
    editForm.billing_cycle = sub.billing_cycle;
    editForm.next_billing_date = sub.next_billing_date ? sub.next_billing_date.split('T')[0] : '';
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
};

const submitEdit = (id) => {
    editForm.patch(route('money.subscription.update', id), {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
        }
    });
};

const toggleSubscription = (sub) => {
    useForm({}).patch(route('money.subscription.toggle', sub.id), {
        preserveScroll: true
    });
};

const deleteSubscription = (sub) => {
    if (confirm(`Are you sure you want to delete ${sub.name}?`)) {
        useForm({}).delete(route('money.subscription.destroy', sub.id), {
            preserveScroll: true
        });
    }
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

<template>
    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient overflow-hidden mt-6 sm:mt-8">
        <div class="px-8 py-6 flex justify-between items-center">
            <h3 class="font-serif text-xl tracking-tight text-atlas-text flex items-center gap-3">
                <svg class="w-5 h-5 text-atlas-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Recurring Payments
            </h3>
        </div>
        
        <div class="p-8 pt-0 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="sub in subscriptions" :key="sub.id" :class="['rounded-[20px] p-5 transition-colors relative group', sub.is_active ? 'bg-atlas-surface border border-atlas-border hover:border-atlas-text/20 shadow-sm' : 'bg-transparent border border-dashed border-atlas-border/50 opacity-60']">
                <template v-if="editingId === sub.id">
                    <form @submit.prevent="submitEdit(sub.id)" class="space-y-4">
                        <input type="text" v-model="editForm.name" required class="w-full text-xs rounded-xl border-atlas-border bg-atlas-panel text-atlas-text focus:ring-0 focus:border-atlas-primaryStart">
                        <div class="flex gap-3">
                            <input type="number" step="0.01" min="0.01" v-model="editForm.amount" required class="w-1/2 text-xs rounded-xl border-atlas-border bg-atlas-panel text-atlas-text focus:ring-0 focus:border-atlas-primaryStart">
                            <select v-model="editForm.billing_cycle" class="w-1/2 text-xs rounded-xl border-atlas-border bg-atlas-panel text-atlas-text focus:ring-0 focus:border-atlas-primaryStart">
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <input type="date" v-model="editForm.next_billing_date" required class="w-full text-xs rounded-xl border-atlas-border bg-atlas-panel text-atlas-text focus:ring-0 focus:border-atlas-primaryStart">
                        <div class="flex gap-3">
                            <button type="button" @click="cancelEdit" class="flex-1 text-xs font-bold tracking-wide py-2 rounded-xl bg-atlas-panel hover:bg-atlas-background text-atlas-muted transition-colors border border-atlas-border">Cancel</button>
                            <button type="submit" class="flex-1 text-xs font-bold tracking-wide py-2 rounded-xl bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd text-atlas-surface transition-colors shadow-sm">Save</button>
                        </div>
                    </form>
                </template>
                <template v-else>
                    <button @click="deleteSubscription(sub)" class="absolute top-4 right-4 text-atlas-muted hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                    <button @click="startEdit(sub)" class="absolute top-4 right-10 text-atlas-muted hover:text-atlas-text opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    
                    <h4 class="font-medium text-sm text-atlas-text mb-2 pr-12">{{ sub.name }}</h4>
                    <div class="flex items-baseline gap-2 mb-4">
                        <span class="text-2xl font-serif tracking-tight text-atlas-text">${{ sub.amount }}</span>
                        <span class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest">/ {{ sub.billing_cycle === 'monthly' ? 'mo' : (sub.billing_cycle === 'yearly' ? 'yr' : 'wk') }}</span>
                    </div>
                    
                    <p class="text-[10px] uppercase font-bold tracking-widest text-atlas-muted mb-6">
                        Next Bill: <span class="text-atlas-text">{{ formatDate(sub.next_billing_date) }}</span>
                    </p>

                    <div class="flex items-center gap-2 mt-auto">
                        <button @click="toggleSubscription(sub)" :class="['w-full py-2 text-[10px] uppercase font-bold tracking-widest rounded-xl transition-all border', sub.is_active ? 'bg-atlas-surface text-atlas-text border-atlas-border hover:bg-atlas-panel' : 'bg-transparent text-atlas-muted border-atlas-border/50 hover:bg-atlas-surface']">
                            {{ sub.is_active ? 'Active (Pause)' : 'Paused (Resume)' }}
                        </button>
                    </div>
                </template>
            </div>

            <!-- Add New -->
            <div class="md:col-span-2 lg:col-span-3 border border-dashed border-atlas-border/70 p-5 rounded-[20px] flex flex-col justify-center bg-atlas-surface/30">
                <form @submit.prevent="submitSubscription" class="space-y-4">
                    <input type="text" v-model="subForm.name" placeholder="Entity (e.g. Netflix)" required class="w-full text-xs rounded-xl border-atlas-border bg-atlas-panel focus:ring-0 focus:border-atlas-primaryStart text-atlas-text placeholder:text-atlas-muted/50">
                    <div class="flex gap-3">
                        <input type="number" step="0.01" min="0.01" v-model="subForm.amount" placeholder="Capital" required class="w-1/2 text-xs rounded-xl border-atlas-border bg-atlas-panel focus:ring-0 focus:border-atlas-primaryStart text-atlas-text placeholder:text-atlas-muted/50">
                        <select v-model="subForm.billing_cycle" class="w-1/2 text-xs rounded-xl border-atlas-border bg-atlas-panel focus:ring-0 focus:border-atlas-primaryStart text-atlas-text">
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5 ml-1">Next Deduction</label>
                        <input type="date" v-model="subForm.next_billing_date" required class="w-full text-xs rounded-xl border-atlas-border bg-atlas-panel focus:ring-0 focus:border-atlas-primaryStart text-atlas-text">
                    </div>
                    <button type="submit" :disabled="subForm.processing" class="w-full bg-atlas-text text-atlas-surface text-xs tracking-wider font-bold py-2.5 rounded-xl transition-all hover:opacity-90">Create Recurring Payment</button>
                </form>
            </div>
        </div>
    </div>
</template>
