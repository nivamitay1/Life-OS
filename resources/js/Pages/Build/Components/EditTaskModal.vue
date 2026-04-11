<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
    task: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
    title: '',
    is_blocker: false,
});

watch(() => props.task, (newTask) => {
    if (newTask) {
        form.title = newTask.title;
        // is_blocker is evaluated dynamically against Task statuses. ActiveTask uses true, maybe a generic task holds a boolean
        form.is_blocker = Boolean(newTask.is_blocker);
    }
}, { immediate: true });

const saveTask = () => {
    if (!props.task) return;
    
    form.patch(route('build.task.update', props.task.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        }
    });
};

const close = () => {
    emit('close');
    setTimeout(() => { form.reset(); form.clearErrors(); }, 300);
};
</script>

<template>
    <Modal :show="show" @close="close" maxWidth="md">
        <div class="px-6 py-5 border-b border-atlas-border/50 flex justify-between items-center text-atlas-text">
            <h3 class="font-serif text-lg tracking-tight">Edit Objective</h3>
            <button @click="close" class="text-atlas-muted hover:text-atlas-text transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <form @submit.prevent="saveTask" class="p-6 space-y-6 bg-atlas-surface/30">
            <div>
                <label class="block text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-2">Objective Title</label>
                <input type="text" v-model="form.title" autofocus class="w-full text-base font-serif bg-atlas-panel border border-atlas-border focus:border-atlas-text focus:ring-1 focus:ring-atlas-text rounded-xl shadow-sm text-atlas-text px-4 py-3" required />
            </div>
            
            <div class="flex items-center justify-between bg-atlas-panel border border-atlas-border p-4 rounded-xl shadow-sm">
                <div class="flex-1 pr-4">
                    <p class="text-sm font-bold text-atlas-text">Urgent Blocker</p>
                    <p class="text-xs text-atlas-muted mt-1 leading-tight">Prioritize this objective and highlight it in red across your workspace.</p>
                </div>
                <button type="button" @click="form.is_blocker = !form.is_blocker" 
                    :class="['w-12 h-6 rounded-full transition-all duration-300 relative flex-shrink-0 focus:outline-none border', form.is_blocker ? 'bg-red-500/20 border-red-500/50' : 'bg-atlas-background border-atlas-border']">
                    <div :class="['w-4 h-4 rounded-full absolute top-1 transition-all duration-300', form.is_blocker ? 'translate-x-6 bg-red-500' : 'translate-x-1 bg-atlas-muted']"></div>
                </button>
            </div>
            
            <div class="pt-2 flex justify-end gap-3">
                <button type="button" @click="close" class="px-5 py-2 text-xs uppercase tracking-widest font-bold text-atlas-muted hover:text-atlas-text transition-colors">Cancel</button>
                <button type="submit" :disabled="form.processing" class="px-5 py-2 text-xs uppercase tracking-widest font-bold bg-atlas-primaryStart text-atlas-surface border border-atlas-primaryEnd rounded-lg shadow-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                    <span v-if="form.processing">Engaging...</span>
                    <span v-else>Save Strategy</span>
                </button>
            </div>
        </form>
    </Modal>
</template>
