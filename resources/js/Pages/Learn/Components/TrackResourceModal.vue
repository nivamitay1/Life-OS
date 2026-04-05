<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({
    title: '',
    author: '',
    type: 'book',
    total_units: null,
    unit_label: 'page',
});

const submit = () => {
    form.post(route('learn.resource.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};

const validateUnits = () => {
    // optional helper
};
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="md">
        <form @submit.prevent="submit" class="p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Track New Resource</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Title <span class="text-red-500">*</span></label>
                    <input v-model="form.title" type="text" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-gray-900 dark:text-white" placeholder="e.g. Designing Data-Intensive Applications">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Type</label>
                        <select v-model="form.type" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-gray-900 dark:text-white">
                            <option value="book">Book</option>
                            <option value="course">Course</option>
                            <option value="article">Article</option>
                            <option value="podcast">Podcast</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Author</label>
                        <input v-model="form.author" type="text" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-gray-900 dark:text-white" placeholder="Optional">
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Does this resource have a measurable finish line? (Optional)</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Total Units</label>
                            <input v-model="form.total_units" type="number" min="1" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-gray-900 dark:text-white" placeholder="e.g. 350">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Unit Type</label>
                            <select v-model="form.unit_label" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-emerald-500 text-gray-900 dark:text-white">
                                <option value="page">Pages</option>
                                <option value="chapter">Chapters</option>
                                <option value="lesson">Lessons</option>
                                <option value="module">Modules</option>
                                <option value="episode">Episodes</option>
                                <option value="article">Articles</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="emit('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900">Cancel</button>
                <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition-colors disabled:opacity-50">
                    Create Resource
                </button>
            </div>
        </form>
    </Modal>
</template>
