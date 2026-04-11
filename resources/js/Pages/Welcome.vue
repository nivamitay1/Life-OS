<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});
</script>

<template>
    <Head title="Life OS - Spatial Command Center" />

    <div class="min-h-screen bg-atlas-background text-atlas-text flex flex-col items-center relative overflow-hidden font-sans">
        
        <!-- Abstract gradient for cinematic spatial depth -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1200px] h-[800px] bg-gradient-to-b from-atlas-primaryStart/5 to-transparent rounded-full blur-[100px] pointer-events-none -z-10"></div>

        <!-- Minimal Header -->
        <header class="w-full absolute top-0 z-50">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex items-center justify-between">
                    <ApplicationLogo />
                    
                    <nav v-if="canLogin" class="flex gap-6 items-center">
                        <template v-if="$page.props.auth.user">
                            <Link href="/home" class="px-6 py-2.5 text-sm tracking-wide text-atlas-surface bg-atlas-primaryStart rounded-full hover:bg-atlas-primaryEnd transition-colors shadow-ambient">
                                Open Canvas
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-medium text-atlas-muted hover:text-atlas-text transition-colors">
                                Log in
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="px-6 py-2.5 text-sm tracking-wide text-atlas-surface bg-atlas-primaryStart rounded-full hover:bg-atlas-primaryEnd transition-colors shadow-ambient">
                                Create Account
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Spatial Hero Section -->
        <main class="flex-1 w-full max-w-4xl mx-auto flex flex-col justify-center items-center py-48 px-4 text-center z-10">
            
            <div class="space-y-12">
                <h1 class="text-6xl md:text-8xl font-serif font-medium tracking-tight text-atlas-text leading-[1.05]">
                    Run your life from a <br class="hidden sm:block" />
                    <span class="italic text-atlas-muted">serene canvas.</span>
                </h1>
                
                <p class="max-w-2xl mx-auto text-lg md:text-xl text-atlas-muted font-normal leading-relaxed tracking-wide">
                    A deeply intentional operating system to track your finances, training, and goals. Reduce cognitive load. Regain focus.
                </p>

                <div class="flex flex-col sm:flex-row justify-center items-center gap-6 pt-12">
                    <template v-if="$page.props.auth.user">
                        <Link href="/home" class="px-10 py-4 text-base tracking-wide text-atlas-surface bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd rounded-full hover:shadow-ambient transition-all shadow-ambient">
                            Enter the Canvas
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('register')" class="px-10 py-4 text-base tracking-wide text-atlas-surface bg-gradient-to-br from-atlas-primaryStart to-atlas-primaryEnd rounded-3xl hover:shadow-ambient transition-all shadow-ambient">
                            Start Building Now
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Glassmorphic Preview Portal -->
            <div class="w-full max-w-5xl mx-auto mt-40">
                <div class="rounded-3xl border border-atlas-border bg-atlas-panel backdrop-blur-3xl shadow-ambient overflow-hidden aspect-[21/9] flex items-center justify-center relative">
                    <div class="absolute inset-0 bg-gradient-to-t from-atlas-background/50 via-transparent to-transparent z-10 pointer-events-none"></div>
                    <p class="text-sm font-serif italic text-atlas-muted z-20">The Digital Curator Preview</p>
                </div>
            </div>

        </main>
        
        <footer class="w-full py-16 text-center">
            <p class="text-sm font-serif italic text-atlas-muted">&copy; 2026 Life OS.</p>
        </footer>
    </div>
</template>
