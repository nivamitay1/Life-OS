<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const emit = defineEmits(['close']);
const inputRef = ref(null);
const command = ref('');
const helperText = ref('Type "go to money", "spent 10 on lunch", or "run 5km"');
const helperType = ref('neutral'); // neutral, error, success
const isProcessing = ref(false);
const toast = ref(null); // { message, detail, undoId }

onMounted(() => {
    nextTick(() => {
        if (inputRef.value) inputRef.value.focus();
    });
});

const parsePreview = () => {
    if (toast.value) return; // don't parse if showing toast

    const text = command.value.trim().toLowerCase();
    
    if (!text) {
        helperText.value = 'Type "go to money", "spent 10 on lunch", or "run 5km"';
        helperType.value = 'neutral';
        return;
    }

    if (text.match(/^(go to|open)\s+(money|running|build|dashboard|home)/i)) {
        const dest = text.split(/\s+/)[text.split(/\s+/).length - 1];
        helperText.value = `Will open: ${dest.charAt(0).toUpperCase() + dest.slice(1)}`;
        helperType.value = 'neutral';
        return;
    }

    let match = text.match(/^spent\s+\$?(\d+(?:\.\d{2})?)\s+on\s+(.+)$/i);
    if (match) {
        helperText.value = `Will log expense: $${match[1]} · ${match[2]}`;
        helperType.value = 'neutral';
        return;
    }

    match = text.match(/^earned\s+\$?(\d+(?:\.\d{2})?)\s+from\s+(.+)$/i);
    if (match) {
        helperText.value = `Will log income: $${match[1]} · ${match[2]}`;
        helperType.value = 'neutral';
        return;
    }

    match = text.match(/^run\s+(\d+(?:\.\d+)?)\s*(km|mi)$/i);
    if (match) {
        helperText.value = `Will start run draft: ${match[1]} ${match[2]}`;
        helperType.value = 'neutral';
        return;
    }

    match = text.match(/^(?:read|study)\s+(\d+)\s*m(?:in(?:ute)?s?)?\s+(?:of|on)\s+(.+)$/i);
    if (match) {
        helperText.value = `Will log study session: ${match[1]} mins · ${match[2]}`;
        helperType.value = 'neutral';
        return;
    }

    helperText.value = "Unrecognized Command";
    helperType.value = 'error';
};

watch(command, parsePreview);

const submitCommand = async () => {
    if (!command.value.trim() || isProcessing.value) return;
    if (helperType.value === 'error') return; // Do not execute unrecognized

    isProcessing.value = true;
    try {
        const response = await axios.post(route('omnibar.dispatch'), { command: command.value });
        const data = response.data;

        if (data.action === 'navigate') {
            router.visit(data.url);
            emit('close');
        } else if (data.action === 'draft_run') {
            router.visit(route('running.index'), {
                data: { draft_distance: data.distance },
                onSuccess: () => emit('close')
            });
        } else if (data.action === 'draft_learn') {
            router.visit(route('learn.index'), {
                data: data.ambiguous ? { draftSession: true } : { draftResource: data.resource_id },
                onSuccess: () => emit('close')
            });
        } else if (data.action === 'money_insert') {
            toast.value = {
                message: data.message,
                detail: data.detail,
                undoId: data.entry_id
            };
            command.value = '';
            // Refresh underlying dashboard/money endpoints reactive polling automatically via inertia reload
            router.reload({ only: ['entries', 'stats', 'priorities', 'balances'] });
        } else {
            helperText.value = data.message || "Error processing request";
            helperType.value = 'error';
        }
    } catch (e) {
        helperText.value = "Network error";
        helperType.value = 'error';
    } finally {
        isProcessing.value = false;
    }
};

const undoAction = async () => {
    if (!toast.value || !toast.value.undoId) return;
    isProcessing.value = true;
    try {
        await axios.post(route('omnibar.undoMoney', toast.value.undoId));
        toast.value = null;
        router.reload({ only: ['entries', 'stats', 'priorities', 'balances'] });
        helperText.value = 'Action Undone';
        helperType.value = 'success';
        setTimeout(() => { emit('close'); }, 1000);
    } catch (e) {
        helperText.value = "Failed to undo";
        helperType.value = 'error';
    } finally {
        isProcessing.value = false;
    }
}
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-start justify-center pt-[15vh]">
        <div class="absolute inset-0 bg-atlas-background/80 backdrop-blur-sm transition-opacity" @click="emit('close')"></div>
        <div class="relative w-full max-w-2xl bg-atlas-panel/90 backdrop-blur-3xl rounded-[24px] shadow-ambient overflow-hidden border border-atlas-border animate-in fade-in zoom-in-95 duration-200">
            
            <div v-if="toast" class="p-10 text-center animate-in slide-in-from-bottom-4 duration-300">
                <div class="mx-auto w-16 h-16 bg-atlas-surface border border-atlas-border/50 rounded-2xl flex items-center justify-center mb-6 text-atlas-primaryStart shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-3xl font-serif tracking-tight text-atlas-text">{{ toast.message }}</h3>
                <p class="text-sm font-sans text-atlas-muted mt-3 tracking-wide">{{ toast.detail }}</p>
                
                <div class="mt-10 flex justify-center gap-3">
                    <button @click="undoAction" :disabled="isProcessing" class="px-6 py-3 text-sm font-medium tracking-wide text-atlas-muted bg-atlas-surface hover:text-atlas-text hover:bg-atlas-background border border-atlas-border rounded-xl transition-colors">
                        Undo Action
                    </button>
                    <button @click="emit('close')" class="px-6 py-3 text-sm font-medium tracking-wide text-atlas-surface bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd rounded-xl shadow-ambient hover:scale-95 transition-transform">
                        Done
                    </button>
                </div>
            </div>

            <div v-else class="flex flex-col">
                <div class="flex items-center px-4 relative">
                    <svg class="h-6 w-6 text-atlas-primaryStart flex-shrink-0 ml-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <input 
                        ref="inputRef"
                        v-model="command"
                        @keydown.enter.prevent="submitCommand"
                        @keydown.esc.prevent="emit('close')"
                        type="text" 
                        class="w-full border-0 bg-transparent py-8 pl-4 pr-4 text-atlas-text placeholder:text-atlas-muted/30 focus:ring-0 text-3xl font-serif tracking-tight" 
                        placeholder="Define your operation..." 
                        :disabled="isProcessing"
                    >
                    <div v-if="isProcessing" class="absolute right-8">
                        <svg class="animate-spin h-6 w-6 text-atlas-primaryStart" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <div class="px-8 py-4 bg-atlas-surface/80 border-t border-atlas-border/50 flex items-center justify-between">
                    <span :class="{'text-atlas-muted font-medium': helperType==='neutral', 'text-red-500 font-medium': helperType==='error', 'text-atlas-primaryStart font-medium': helperType==='success'}" class="text-xs font-sans tracking-wide">
                        {{ helperText }}
                    </span>
                    <span class="text-[10px] uppercase tracking-widest font-bold text-atlas-muted/70 flex items-center gap-1.5">
                        <kbd class="px-2 py-0.5 rounded bg-atlas-panel border border-atlas-border shadow-sm font-sans text-[10px] text-atlas-muted">Esc</kbd> to close
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
