<script setup>
import { ref, watch, onMounted } from 'vue';

const theme = ref('system');

onMounted(() => {
    if (localStorage.theme === 'dark') {
        theme.value = 'dark';
    } else if (localStorage.theme === 'light') {
        theme.value = 'light';
    } else {
        theme.value = 'system';
    }
});

const switchTheme = (newTheme) => {
    theme.value = newTheme;
    if (newTheme === 'dark') {
        localStorage.theme = 'dark';
        document.documentElement.classList.add('dark');
    } else if (newTheme === 'light') {
        localStorage.theme = 'light';
        document.documentElement.classList.remove('dark');
    } else {
        localStorage.removeItem('theme');
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-atlas-text">
                Appearance
            </h2>

            <p class="mt-1 text-sm text-atlas-muted">
                Customize the interface by selecting between light and dark mode.
            </p>
        </header>

        <div class="mt-6 flex flex-wrap gap-4">
            <button
                @click="switchTheme('light')"
                :class="['px-4 py-2 rounded-xl flex flex-col items-center gap-2 border-2 transition-all', theme === 'light' ? 'border-atlas-primaryStart bg-atlas-background' : 'border-atlas-border bg-atlas-panel hover:border-atlas-primaryStart/50']"
            >
                <div class="w-16 h-12 bg-gray-100 rounded-md border border-gray-200"></div>
                <span class="text-sm font-medium text-atlas-text">Light</span>
            </button>
            
            <button
                @click="switchTheme('dark')"
                :class="['px-4 py-2 rounded-xl flex flex-col items-center gap-2 border-2 transition-all', theme === 'dark' ? 'border-atlas-primaryStart bg-atlas-background' : 'border-atlas-border bg-atlas-panel hover:border-atlas-primaryStart/50']"
            >
                <div class="w-16 h-12 bg-slate-800 rounded-md border border-slate-700"></div>
                <span class="text-sm font-medium text-atlas-text">Dark</span>
            </button>

            <button
                @click="switchTheme('system')"
                :class="['px-4 py-2 rounded-xl flex flex-col items-center gap-2 border-2 transition-all', theme === 'system' ? 'border-atlas-primaryStart bg-atlas-background' : 'border-atlas-border bg-atlas-panel hover:border-atlas-primaryStart/50']"
            >
                <div class="w-16 h-12 bg-gradient-to-br from-gray-100 to-slate-800 rounded-md border border-atlas-border"></div>
                <span class="text-sm font-medium text-atlas-text">System</span>
            </button>
        </div>
    </section>
</template>
