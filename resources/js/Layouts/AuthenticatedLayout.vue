<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';
import Omnibar from '@/Components/Omnibar.vue';
import { onMounted, onUnmounted } from 'vue';

const showingNavigationSidebar = ref(false);
const showingOmnibar = ref(false);

const handleKeydown = (e) => {
    // Check for Ctrl+K or Cmd+K
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        const isInputField = ['input', 'textarea'].includes(activeTag) || document.activeElement.isContentEditable;
        
        // Suppress if the user is already typing in an explicit text field
        if (isInputField) return;

        e.preventDefault();
        showingOmnibar.value = !showingOmnibar.value;
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});

const navigation = [
    { name: 'Dashboard', routeName: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Money', routeName: 'money.index', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Running', routeName: 'running.index', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { name: 'Build', routeName: 'build.index', icon: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4' },
    { name: 'Learn', routeName: 'learn.index', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
    { name: 'Timeline', routeName: null, icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Reviews', routeName: null, icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
];

</script>

<template>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900 font-sans text-gray-900 dark:text-gray-100 overflow-hidden">
        
        <!-- Mobile sidebar backdrop -->
        <div v-if="showingNavigationSidebar" @click="showingNavigationSidebar = false" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden blur-sm transition-opacity"></div>
        
        <!-- Sidebar -->
        <div :class="[showingNavigationSidebar ? 'translate-x-0' : '-translate-x-full', 'fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-950 border-r border-gray-200 dark:border-gray-800 transition duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex lg:flex-col']">
            
            <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-800 px-6">
                <Link :href="route('dashboard')" class="flex items-center gap-3 w-full">
                    <ApplicationLogo class="block h-8 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                    <span class="text-xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400">Life OS</span>
                </Link>
            </div>
            
            <div class="flex-1 overflow-y-auto px-4 py-6">
                <nav class="space-y-1">
                    <template v-for="item in navigation" :key="item.name">
                        <Link v-if="item.routeName" :href="route(item.routeName)" :class="[route().current(item.routeName) ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white', 'group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200']">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0 transition-colors duration-200" :class="[route().current(item.routeName) ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300']" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                            </svg>
                            {{ item.name }}
                        </Link>
                        <!-- For non-implemented routes currently -->
                        <div v-else class="group flex items-center px-3 py-2.5 text-sm rounded-lg transition-all duration-200 text-gray-400 dark:text-gray-500 cursor-not-allowed opacity-60">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                            </svg>
                            {{ item.name }}
                            <span class="ml-auto text-[10px] uppercase font-bold tracking-wider py-0.5 px-1.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">Soon</span>
                        </div>
                    </template>
                </nav>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-800 p-4">
                <Dropdown align="top" width="48">
                    <template #trigger>
                        <button type="button" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition ease-in-out duration-150">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xs uppercase shadow-sm">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                            <span class="ml-3 truncate">{{ $page.props.auth.user.name }}</span>
                            <svg class="ml-auto h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </div>

        <!-- Main Area -->
        <div class="flex-1 flex flex-col w-0 overflow-hidden">
            <!-- Mobile Header -->
            <div class="lg:hidden flex items-center justify-between bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 px-4 py-3">
                <Link :href="route('dashboard')" class="flex items-center gap-2">
                    <ApplicationLogo class="h-6 w-auto text-indigo-600 dark:text-indigo-400" />
                    <span class="text-lg font-bold">Life OS</span>
                </Link>
                <button @click="showingNavigationSidebar = true" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Page Header Container -->
            <header class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 z-10 sticky top-0" v-if="$slots.header">
                <div class="px-6 py-5 sm:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-6 sm:p-8">
                <div class="mx-auto max-w-7xl">
                    <slot />
                </div>
            </main>
        </div>

        <Omnibar v-if="showingOmnibar" @close="showingOmnibar = false" />
    </div>
</template>
