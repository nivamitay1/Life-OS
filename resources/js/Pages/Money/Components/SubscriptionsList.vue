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
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden mt-6 sm:mt-8">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Recurring Subscriptions
            </h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div v-for="sub in subscriptions" :key="sub.id" :class="['border rounded-xl p-4 transition-colors relative group', sub.is_active ? 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' : 'bg-gray-50 border-dashed border-gray-200 dark:bg-gray-900 dark:border-gray-800 opacity-60']">
                    <template v-if="editingId === sub.id">
                        <form @submit.prevent="submitEdit(sub.id)" class="space-y-3">
                            <input type="text" v-model="editForm.name" required class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                            <div class="flex gap-2">
                                <input type="number" step="0.01" min="0.01" v-model="editForm.amount" required class="w-1/2 text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                                <select v-model="editForm.billing_cycle" class="w-1/2 text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <input type="date" v-model="editForm.next_billing_date" required class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                            <div class="flex gap-2">
                                <button type="button" @click="cancelEdit" class="flex-1 text-xs font-bold py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors">Cancel</button>
                                <button type="submit" class="flex-1 text-xs font-bold py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Save</button>
                            </div>
                        </form>
                    </template>
                    <template v-else>
                        <button @click="deleteSubscription(sub)" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        <button @click="startEdit(sub)" class="absolute top-3 right-9 text-gray-400 hover:text-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                        
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2 pr-12">{{ sub.name }}</h4>
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">${{ sub.amount }}</span>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">/ {{ sub.billing_cycle === 'monthly' ? 'mo' : (sub.billing_cycle === 'yearly' ? 'yr' : 'wk') }}</span>
                        </div>
                        
                        <p class="text-[10px] uppercase font-bold tracking-wider text-gray-500 mb-4">
                            Next Bill: <span class="text-gray-900 dark:text-gray-300">{{ formatDate(sub.next_billing_date) }}</span>
                        </p>

                        <div class="flex items-center gap-2 mt-auto">
                            <button @click="toggleSubscription(sub)" :class="['w-full py-1.5 text-[10px] uppercase font-bold tracking-wider rounded-lg transition-colors border', sub.is_active ? 'bg-indigo-50 text-indigo-600 border-indigo-100 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800/50 dark:hover:bg-indigo-900/50' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700']">
                                {{ sub.is_active ? 'Active (Pause)' : 'Paused (Resume)' }}
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Add New -->
                <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 p-4 rounded-xl flex flex-col justify-center bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white">
                    <form @submit.prevent="submitSubscription" class="space-y-3">
                        <input type="text" v-model="subForm.name" placeholder="Subscription (e.g. Netflix)" required class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                        <div class="flex gap-2">
                            <input type="number" step="0.01" min="0.01" v-model="subForm.amount" placeholder="Price" required class="w-1/2 text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                            <select v-model="subForm.billing_cycle" class="w-1/2 text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1 ml-1">Next Billing Date</label>
                            <input type="date" v-model="subForm.next_billing_date" required class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500 text-gray-900 dark:text-white">
                        </div>
                        <button type="submit" :disabled="subForm.processing" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-bold py-2 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">Add Subscription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
