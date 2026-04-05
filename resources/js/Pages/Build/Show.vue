<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  project: Object,
  tasks: Array,
  levelData: Object,
});

const taskForm = useForm({
    title: '',
    project_id: props.project.id,
    status: 'todo',
});

const activeColumn = ref('todo');

const submitTask = (status) => {
    taskForm.status = status;
    taskForm.post(route('build.task.store'), {
        onSuccess: () => {
            taskForm.reset('title');
        },
    });
};

const updateStatus = (task, newStatus) => {
    useForm({
        status: newStatus
    }).patch(route('build.task.update', task.id), {
        preserveScroll: true
    });
};

const deleteTask = (task) => {
    if (confirm('Are you sure you want to delete this task?')) {
        useForm({}).delete(route('build.task.destroy', task.id), {
            preserveScroll: true
        });
    }
};

const todoTasks = computed(() => props.tasks.filter(t => t.status === 'todo'));
const inProgressTasks = computed(() => props.tasks.filter(t => t.status === 'in_progress'));
const doneTasks = computed(() => props.tasks.filter(t => t.status === 'done'));

const getInitial = (name) => {
    return name ? name.charAt(0).toUpperCase() : '?';
};
</script>

<template>
    <Head :title="`${project.name} | Build | Life OS`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 px-2">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <Link :href="route('build.index')" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </Link>
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">{{ project.name }}</h2>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 ml-8">{{ project.description || 'No description provided.' }}</p>
                </div>
                <!-- Mini Module Progress Widget -->
                <div class="bg-gradient-to-r from-blue-500/10 to-indigo-500/10 dark:from-blue-500/20 dark:to-indigo-500/20 border border-blue-100 dark:border-blue-900/50 rounded-xl px-4 py-2 flex items-center gap-4">
                    <div class="text-center">
                        <div class="text-[10px] uppercase font-bold text-blue-600 dark:text-blue-400">Build Lvl</div>
                        <div class="font-black text-gray-900 dark:text-white">{{ levelData.level }}</div>
                    </div>
                    <div class="w-24 bg-white/50 dark:bg-black/20 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-500 h-2 rounded-full" :style="{ width: levelData.progress_percent + '%' }"></div>
                    </div>
                </div>
            </div>
        </template>

        <div class="h-[calc(100vh-12rem)] flex gap-6 px-2 overflow-x-auto pb-4 pt-4">
            
            <!-- Column: Todo -->
            <div class="flex-shrink-0 w-80 flex flex-col bg-gray-50/50 dark:bg-gray-800/20 rounded-2xl border border-gray-100 dark:border-gray-800">
                <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-white dark:bg-gray-800/80 rounded-t-2xl shadow-sm z-10">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        To Do
                    </h3>
                    <span class="bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 text-xs font-bold px-2 py-0.5 rounded-full">{{ todoTasks.length }}</span>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-for="task in todoTasks" :key="task.id" class="group bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md hover:border-blue-200 dark:hover:border-blue-900/50 transition-all flex flex-col justify-between min-h-[100px]">
                        <div>
                            <p class="font-medium text-sm text-gray-900 dark:text-white leading-snug">{{ task.title }}</p>
                        </div>
                        <div class="flex justify-end mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="updateStatus(task, 'in_progress')" class="text-xs bg-gray-100 hover:bg-blue-50 dark:bg-gray-700 dark:hover:bg-blue-900/30 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-1.5 rounded-lg border border-transparent hover:border-blue-100 dark:hover:border-blue-800 transition-all font-medium flex items-center gap-1">
                                Start <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white/50 dark:bg-gray-800/50 rounded-b-2xl border-t border-gray-100 dark:border-gray-800">
                    <form @submit.prevent="submitTask('todo')" class="flex gap-2 relative">
                        <input type="text" v-model="taskForm.title" placeholder="Add a new task..." required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3 pr-10 text-gray-900 dark:text-white">
                        <button type="submit" :disabled="taskForm.processing" class="absolute right-2 top-1.5 text-gray-400 hover:text-blue-600 p-1 rounded-md transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Column: In Progress -->
            <div class="flex-shrink-0 w-80 flex flex-col bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-50 dark:border-blue-900/30">
                <div class="p-4 border-b border-blue-50 dark:border-blue-900/30 flex justify-between items-center bg-white dark:bg-gray-800/80 rounded-t-2xl shadow-sm z-10">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        In Progress
                    </h3>
                    <span class="bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 text-xs font-bold px-2 py-0.5 rounded-full">{{ inProgressTasks.length }}</span>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-for="task in inProgressTasks" :key="task.id" class="group bg-white dark:bg-gray-800 p-4 rounded-xl border border-blue-100 dark:border-gray-700 shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all flex flex-col justify-between min-h-[100px]">
                        <div>
                            <p class="font-medium text-sm text-gray-900 dark:text-white leading-snug">{{ task.title }}</p>
                        </div>
                        <div class="flex justify-between mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="updateStatus(task, 'todo')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <button @click="updateStatus(task, 'done')" class="text-xs bg-blue-50 hover:bg-emerald-50 dark:bg-blue-900/20 dark:hover:bg-emerald-900/30 text-blue-600 dark:text-blue-400 hover:text-emerald-600 dark:hover:text-emerald-400 px-3 py-1.5 rounded-lg border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 transition-all font-bold flex items-center gap-1">
                                Complete <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column: Done -->
            <div class="flex-shrink-0 w-80 flex flex-col bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-50 dark:border-emerald-900/30">
                <div class="p-4 border-b border-emerald-50 dark:border-emerald-900/30 flex justify-between items-center bg-white dark:bg-gray-800/80 rounded-t-2xl shadow-sm z-10">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500/50"></span>
                        Done
                    </h3>
                    <span class="bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-300 text-xs font-bold px-2 py-0.5 rounded-full">{{ doneTasks.length }}</span>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-for="task in doneTasks" :key="task.id" class="group bg-white dark:bg-gray-800 p-4 rounded-xl border border-emerald-50 dark:border-gray-800 shadow-sm hover:border-gray-200 dark:hover:border-gray-600 transition-all flex flex-col justify-between min-h-[100px] opacity-75 hover:opacity-100">
                        <div>
                            <p class="font-medium text-sm text-gray-500 dark:text-gray-400 line-through leading-snug">{{ task.title }}</p>
                        </div>
                        <div class="flex justify-between mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="updateStatus(task, 'in_progress')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <button @click="deleteTask(task)" class="text-red-400 hover:text-red-600 dark:hover:text-red-400 p-1.5 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
