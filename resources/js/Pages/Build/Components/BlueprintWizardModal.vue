<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({});

const loadSaaSPipeline = () => {
    form.post(route('build.seed-blueprint'), {
        onSuccess: () => {
            emit('close');
        }
    });
};
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="xl">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center text-gray-900 dark:text-white font-bold">
            <h3>New Blueprint Pipeline</h3>
            <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-500 mb-6">Execution is everything. Start a new project with a predefined workflow designed for action.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- SaaS Pipeline -->
                <div class="border-2 border-blue-100 dark:border-blue-900/50 hover:border-blue-500 rounded-xl p-5 cursor-pointer transition-all bg-white dark:bg-gray-800 group" @click="loadSaaSPipeline">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400 flex items-center justify-center mb-4 transition-transform group-hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-2">SaaS Launchpad</h4>
                    <p class="text-xs text-gray-500">Perfect for shipping modern web tools. Includes schema design, auth scaffold, layout, and billing configuration blocks.</p>
                    <div class="mt-4 flex gap-2">
                        <span class="bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 text-[10px] uppercase font-bold tracking-wider px-2 py-1 rounded">5 Tasks</span>
                        <span class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400 text-[10px] uppercase font-bold tracking-wider px-2 py-1 rounded">Web App</span>
                    </div>
                </div>

                <!-- Custom / Empty -->
                <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 hover:border-gray-400 rounded-xl p-5 cursor-pointer flex flex-col justify-center items-center text-center bg-gray-50 dark:bg-gray-800/50 transition-all opacity-70 hover:opacity-100">
                    <div class="w-10 h-10 rounded-lg bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-2">Blank Canvas</h4>
                    <p class="text-xs text-gray-500">Start entirely from scratch. You define the workflow.</p>
                </div>

            </div>
        </div>
    </Modal>
</template>
