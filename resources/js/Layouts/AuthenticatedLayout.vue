<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Omnibar from '@/Components/Omnibar.vue';

const showingOmnibar = ref(false);

const handleKeydown = (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        const isInputField = ['input', 'textarea'].includes(activeTag) || document.activeElement.isContentEditable;
        if (isInputField) return;
        e.preventDefault();
        showingOmnibar.value = !showingOmnibar.value;
    }
};

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));

const navigation = [
    { name: 'Dashboard', routeName: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Money', routeName: 'money.index', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Running', routeName: 'running.index', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { name: 'Build', routeName: 'build.index', icon: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4' },
    { name: 'Learn', routeName: 'learn.index', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
];

defineProps({
    maxWidth: {
        type: String,
        default: 'max-w-5xl',
    },
});
</script>

<template>
    <div class="min-h-screen bg-atlas-background text-atlas-text flex flex-col items-center relative overflow-hidden font-sans">
        
        <!-- Abstract gradient for cinematic spatial depth -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1200px] h-[800px] bg-gradient-to-b from-atlas-muted/5 to-transparent rounded-full blur-[100px] pointer-events-none -z-10"></div>
        
        <!-- Page Header Container -->
        <header class="w-full z-10 pt-10" v-if="$slots.header">
            <div class="mx-auto px-4 sm:px-6 lg:px-8" :class="maxWidth">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Canvas Area -->
        <main class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-48 relative z-10 flex flex-col min-h-[80vh]" :class="maxWidth">
            <slot />
        </main>

        <!-- Floating Nav Dock (macOS style) -->
        <div class="fixed bottom-12 left-1/2 -translate-x-1/2 z-50">
            <nav class="flex items-center gap-2 px-4 py-3 bg-atlas-panel/80 backdrop-blur-2xl border border-atlas-border rounded-full shadow-ambient">
                
                <!-- Main Nav Links -->
                <template v-for="item in navigation" :key="item.name">
                    <Link v-if="item.routeName" :href="route(item.routeName)" :class="[route().current(item.routeName) ? 'bg-atlas-background text-atlas-text font-bold shadow-sm border border-atlas-border/50' : 'text-atlas-muted hover:text-atlas-text hover:bg-atlas-background/50', 'p-3 rounded-full transition-all group relative']" :title="item.name">
                        <svg class="w-5 h-5 opacity-90 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                        </svg>
                    </Link>
                </template>

                <div class="w-px h-6 bg-atlas-border mx-2"></div>
                
                <!-- Dropdown Profile / Settings -->
                <Dropdown align="top" width="48">
                    <template #trigger>
                        <button class="flex items-center p-1 rounded-full transition-all hover:bg-atlas-background/50 focus:outline-none">
                            <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-atlas-primaryStart to-atlas-primaryEnd flex items-center justify-center text-atlas-surface font-black text-xs uppercase shadow-sm">
                                {{ usePage().props.auth.user.name.charAt(0) }}
                            </div>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                    </template>
                </Dropdown>
            </nav>
        </div>

        <Omnibar v-if="showingOmnibar" @close="showingOmnibar = false" />
    </div>
</template>
