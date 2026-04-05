<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});
</script>

<template>
    <Head title="Life OS - Your Personal Command Center" />

    <div class="min-h-screen bg-white dark:bg-gray-950 selection:bg-indigo-500 selection:text-white flex flex-col items-center">
        <!-- Navigation Bar -->
        <header class="w-full absolute top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-24">
                    <div class="flex items-center gap-3">
                        <ApplicationLogo class="h-10 w-auto text-indigo-600 dark:text-indigo-400" />
                        <span class="text-2xl font-black tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-500 dark:from-white dark:to-gray-400">Life OS</span>
                    </div>
                    
                    <nav v-if="canLogin" class="flex gap-4">
                        <template v-if="$page.props.auth.user">
                            <Link href="/" class="px-5 py-2.5 text-sm font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition-all shadow-sm">
                                Open Command Center
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                                Log in
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="px-5 py-2.5 text-sm font-bold bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full hover:bg-gray-800 dark:hover:gray-100 transition-all shadow-sm">
                                Register
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 w-full flex flex-col justify-center items-center py-32 relative overflow-hidden">
            
            <!-- Abstract Background Gradients -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 dark:from-indigo-500/10 dark:to-purple-500/10 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

            <div class="relative z-10 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Now in Beta
                </div>

                <h1 class="text-5xl md:text-7xl font-black tracking-tight text-gray-900 dark:text-white leading-[1.1]">
                    Run your life perfectly. <br class="hidden sm:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500">Without the noise.</span>
                </h1>
                
                <p class="max-w-2xl mx-auto text-lg md:text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    Life OS is a modular personal operating system. Track your finances, training, projects, and goals in one unified command center designed for maximum momentum.
                </p>

                <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-8">
                    <template v-if="$page.props.auth.user">
                        <Link href="/" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-white bg-indigo-600 rounded-full hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/30 flex items-center justify-center gap-2">
                            Enter Workspace
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('register')" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-gray-900 bg-white rounded-full hover:bg-gray-50 border border-gray-200 transition shadow-xl shadow-gray-200/50 dark:bg-gray-900 dark:text-white dark:border-gray-700 dark:shadow-none dark:hover:bg-gray-800 flex items-center justify-center gap-2">
                            Start Building Now
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </Link>
                        <Link :href="route('login')" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                            Login into OS
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Dashboard Preview Mockup -->
            <div class="relative w-full max-w-6xl mx-auto px-4 mt-24">
                <div class="rounded-2xl border border-gray-200/50 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50 backdrop-blur-xl shadow-2xl overflow-hidden aspect-[16/9] flex items-center justify-center text-gray-900 dark:text-white">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-50 dark:from-gray-950 via-transparent to-transparent z-10"></div>
                    <p class="text-sm font-bold text-gray-400 z-20">Premium Dashboard Interface Preview</p>
                </div>
            </div>

        </main>
        
        <footer class="w-full py-10 border-t border-gray-100 dark:border-gray-900 text-center">
            <p class="text-sm font-medium text-gray-400 dark:text-gray-600">&copy; 2026 Life OS. All rights reserved.</p>
        </footer>
    </div>
</template>
