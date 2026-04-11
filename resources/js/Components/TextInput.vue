<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-xl border-atlas-border bg-atlas-surface shadow-sm focus:border-atlas-primaryStart focus:ring-atlas-primaryStart text-atlas-text transition-colors placeholder:text-atlas-muted"
        v-model="model"
        ref="input"
    />
</template>
