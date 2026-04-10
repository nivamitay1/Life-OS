<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-atlas-background text-atlas-text flex flex-col items-center relative overflow-hidden font-sans">
        
        <!-- Abstract gradient for cinematic spatial depth -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1200px] h-[800px] bg-gradient-to-b from-atlas-muted/5 to-transparent rounded-full blur-[100px] pointer-events-none -z-10"></div>
        
        <!-- Main Canvas Area with massive whitespace padding -->
        <main class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-48 relative z-10 flex flex-col min-h-screen">
            <slot />
        </main>

        <!-- Floating Nav Dock (macOS style) -->
        <div class="fixed bottom-12 left-1/2 -translate-x-1/2 z-50">
            <nav class="flex items-center gap-2 px-4 py-3 bg-atlas-panel/80 backdrop-blur-2xl border border-atlas-border rounded-full shadow-ambient">
                <Link href="/" class="p-3 text-atlas-text hover:bg-atlas-background/80 rounded-full transition-all">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                </Link>
                <div class="w-px h-6 bg-atlas-border mx-1"></div>
                <Link class="p-3 text-atlas-muted hover:text-atlas-text hover:bg-atlas-background/80 rounded-full transition-all">
                    <!-- Dashboard icon -->
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                </Link>
                <Link class="p-3 text-atlas-muted hover:text-atlas-text hover:bg-atlas-background/80 rounded-full transition-all">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </Link>
            </nav>
        </div>
    </div>
</template>
