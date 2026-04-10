<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center pt-24 sm:justify-center sm:pt-0 bg-atlas-background text-atlas-text relative overflow-hidden font-sans">
        
        <!-- Abstract gradient for cinematic spatial depth -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1200px] h-[800px] bg-gradient-to-b from-atlas-primaryStart/5 to-transparent rounded-full blur-[100px] pointer-events-none -z-10"></div>

        <div class="z-10 mb-12">
            <Link href="/">
                <ApplicationLogo class="text-atlas-text" />
            </Link>
        </div>

        <div class="z-10 w-full mt-6 px-10 py-12 bg-atlas-panel backdrop-blur-2xl shadow-ambient border border-atlas-border sm:max-w-md rounded-[24px]">
            <slot />
        </div>
    </div>
</template>
