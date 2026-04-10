<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
    project: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    description: '',
});

watch(() => props.project, (newProject) => {
    if (newProject) {
        form.name = newProject.name || '';
        form.description = newProject.description || '';
    }
}, { immediate: true });

const saveProject = () => {
    if (!props.project) return;
    
    form.patch(route('build.project.update', props.project.id), {
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
            <h3 class="font-serif text-lg tracking-tight">Edit Project Context</h3>
            <button @click="close" class="text-atlas-muted hover:text-atlas-text transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <form @submit.prevent="saveProject" class="p-6 space-y-6 bg-atlas-surface/30">
            <div>
                <label class="block text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-2">Project Classification</label>
                <input type="text" v-model="form.name" autofocus class="w-full text-base font-serif bg-atlas-panel border border-atlas-border focus:border-atlas-text focus:ring-1 focus:ring-atlas-text rounded-xl shadow-sm text-atlas-text px-4 py-3" required />
            </div>

            <div>
                <label class="block text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-2">Primary Directive</label>
                <textarea v-model="form.description" rows="3" class="w-full text-sm bg-atlas-panel border border-atlas-border focus:border-atlas-text focus:ring-1 focus:ring-atlas-text rounded-xl shadow-sm text-atlas-text px-4 py-3 resize-none"></textarea>
            </div>
            
            <div class="pt-2 flex justify-end gap-3">
                <button type="button" @click="close" class="px-5 py-2 text-xs uppercase tracking-widest font-bold text-atlas-muted hover:text-atlas-text transition-colors">Abort</button>
                <button type="submit" :disabled="form.processing" class="px-5 py-2 text-xs uppercase tracking-widest font-bold bg-atlas-primaryStart text-atlas-surface border border-atlas-primaryEnd rounded-lg shadow-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                    <span v-if="form.processing">Committing...</span>
                    <span v-else>Recalibrate</span>
                </button>
            </div>
        </form>
    </Modal>
</template>
