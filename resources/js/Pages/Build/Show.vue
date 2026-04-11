<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import EditTaskModal from './Components/EditTaskModal.vue';
import EditProjectModal from './Components/EditProjectModal.vue';

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
const showEditModal = ref(false);
const editingTask = ref(null);

const showEditProjectModal = ref(false);

const openEditMenu = (task) => {
    editingTask.value = task;
    showEditModal.value = true;
};

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

    <AuthenticatedLayout maxWidth="max-w-none">
        <div class="space-y-12 px-8 flex flex-col items-center">
            <!-- Breadcrumb Header -->
            <div class="flex items-center justify-between w-full max-w-7xl">
                <div class="flex items-center gap-6">
                    <Link :href="route('build.index')" class="text-atlas-muted hover:text-atlas-text transition-colors">
                        <h1 class="text-[10px] uppercase tracking-[0.2em] font-bold border border-atlas-border px-4 py-2 rounded-lg bg-atlas-panel shadow-sm flex items-center gap-2">
                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                             Build Workspace
                        </h1>
                    </Link>
                    <div class="flex items-center gap-4 group/header cursor-pointer" @click="showEditProjectModal = true">
                        <span class="w-1.5 h-1.5 rounded-full bg-atlas-primaryStart opacity-30"></span>
                        <span class="text-2xl font-serif text-atlas-text tracking-tight group-hover/header:opacity-80 transition-opacity">{{ project.name }}</span>
                        <button class="text-atlas-muted opacity-0 group-hover/header:opacity-100 transition-opacity hover:text-atlas-text px-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Mini Progress -->
                <div class="flex items-center gap-5">
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-[9px] font-bold text-atlas-muted uppercase tracking-[0.2em]">Build Mastery</span>
                        <span class="text-xs font-serif text-atlas-text opacity-70">{{ levelData.level }} / 10</span>
                    </div>
                    <div class="w-32 bg-atlas-surface border border-atlas-border/50 rounded-full h-1 overflow-hidden relative">
                        <div class="bg-atlas-text h-1 rounded-full relative z-10" :style="{ width: levelData.progress_percent + '%' }"></div>
                    </div>
                </div>
            </div>



            <div class="h-[calc(100vh-20rem)] flex gap-10 pb-8 pt-4 overflow-x-auto w-full max-w-7xl justify-center items-start">
                
                <!-- Column: Todo -->
                <div class="flex-shrink-0 w-80 flex flex-col items-center">
                    <div class="w-full flex justify-between items-center mb-6 pl-2 pr-1">
                        <h3 class="font-serif text-lg text-atlas-text flex items-center gap-3 tracking-tight">
                            <span class="w-1.5 h-1.5 rounded-full bg-atlas-muted opacity-30"></span>
                            To Do
                        </h3>
                        <span class="text-[10px] font-bold text-atlas-muted tracking-widest uppercase">{{ todoTasks.length }}</span>
                    </div>
                    
                    <div class="w-full flex flex-col gap-5 overflow-y-auto max-h-full pb-6 px-1">
                        <div v-for="task in todoTasks" :key="task.id" 
                            :class="['group relative p-6 rounded-[24px] border transition-all hover:-translate-y-1', 
                                     task.is_blocker 
                                     ? 'bg-red-500/5 border-red-500/40 shadow-[0_8px_30px_rgb(239,68,68,0.1)] hover:shadow-[0_8px_30px_rgb(239,68,68,0.2)]' 
                                     : 'bg-atlas-surface border-atlas-border shadow-sm hover:shadow-ambient']">
                            <div class="flex justify-between items-start gap-4">
                                <p class="text-sm font-serif text-atlas-text leading-relaxed tracking-wide flex-1">{{ task.title }}</p>
                                <button @click="openEditMenu(task)" class="text-atlas-muted hover:text-atlas-text opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </div>
                            <div class="flex justify-end mt-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="updateStatus(task, 'in_progress')" class="text-[9px] uppercase tracking-[0.2em] font-bold text-atlas-muted hover:text-atlas-text flex items-center gap-2 transition-colors">
                                    Engage <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="mt-2 pl-2 pr-2">
                            <form @submit.prevent="submitTask('todo')" class="flex gap-2 relative group/form">
                                <input type="text" v-model="taskForm.title" placeholder="Define objective..." required class="w-full text-xs rounded-xl border-atlas-border bg-atlas-panel focus:ring-1 focus:ring-atlas-text focus:border-atlas-text text-atlas-text placeholder:text-atlas-muted/30 py-4 px-5 pr-12 transition-all">
                                <button type="submit" :disabled="taskForm.processing" class="absolute right-4 top-3.5 text-atlas-muted hover:text-atlas-text p-1 transition-colors opacity-40 group-focus-within/form:opacity-100 group-hover/form:opacity-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Column: In Progress -->
                <div class="flex-shrink-0 w-80 flex flex-col items-center">
                    <div class="w-full flex justify-between items-center mb-6 pl-1 pr-1">
                        <h3 class="font-serif text-lg text-atlas-text flex items-center gap-3 tracking-tight">
                            <span class="w-1.5 h-1.5 rounded-full bg-atlas-primaryStart animate-pulse"></span>
                            Active
                        </h3>
                        <span class="text-[10px] font-bold text-atlas-primaryStart tracking-widest uppercase">{{ inProgressTasks.length }}</span>
                    </div>
                    
                    <div class="w-full flex flex-col gap-5 overflow-y-auto max-h-full pb-6 px-1">
                        <div v-for="task in inProgressTasks" :key="task.id" 
                            :class="['group relative p-6 rounded-[24px] border transition-all hover:-translate-y-1', 
                                     task.is_blocker 
                                     ? 'bg-red-500/5 border-red-500/40 shadow-[0_8px_30px_rgb(239,68,68,0.1)] ring-1 ring-red-500/10' 
                                     : 'bg-atlas-surface border-atlas-primaryStart/20 shadow-ambient ring-1 ring-atlas-primaryStart/5']">
                            <div class="absolute right-4 top-4">
                                <button @click="openEditMenu(task)" class="text-atlas-muted hover:text-atlas-text opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </div>
                            <p class="text-sm font-serif font-bold text-atlas-text leading-relaxed tracking-wide underline decoration-atlas-primaryStart/20 underline-offset-4 mt-2 mb-2">{{ task.title }}</p>
                            <div class="flex justify-between items-center mt-6 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="updateStatus(task, 'todo')" class="text-atlas-muted hover:text-atlas-text transition-colors text-[9px] uppercase tracking-widest font-bold">
                                    &larr; Revert
                                </button>
                                <button @click="updateStatus(task, 'done')" class="text-[9px] font-bold uppercase tracking-[0.2em] text-atlas-surface bg-atlas-text px-5 py-2.5 rounded-lg hover:opacity-90 transition-all flex items-center gap-2">
                                    Conclude <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column: Done -->
                <div class="flex-shrink-0 w-80 flex flex-col items-center">
                    <div class="w-full flex justify-between items-center mb-6 pl-1 pr-1">
                        <h3 class="font-serif text-lg text-atlas-muted flex items-center gap-3 tracking-tight opacity-50">
                            <span class="w-1.5 h-1.5 rounded-full bg-atlas-muted/30"></span>
                            Concluded
                        </h3>
                        <span class="text-[10px] font-bold text-atlas-muted tracking-widest uppercase opacity-30">{{ doneTasks.length }}</span>
                    </div>
                    
                    <div class="w-full flex flex-col gap-5 overflow-y-auto max-h-full pb-6 px-1">
                        <div v-for="task in doneTasks" :key="task.id" class="group bg-transparent p-6 rounded-[24px] border border-atlas-border/50 transition-all grayscale opacity-40 hover:opacity-100 hover:bg-atlas-panel hover:grayscale-0">
                            <p class="text-sm font-serif text-atlas-muted line-through leading-relaxed">{{ task.title }}</p>
                            <div class="flex justify-between mt-8 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="updateStatus(task, 'in_progress')" class="text-atlas-muted hover:text-atlas-text transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button @click="deleteTask(task)" class="text-red-500/30 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <EditTaskModal 
            :show="showEditModal" 
            :task="editingTask" 
            @close="showEditModal = false; editingTask = null" 
        />
        
        <EditProjectModal 
            :show="showEditProjectModal"
            :project="project"
            @close="showEditProjectModal = false"
        />
</AuthenticatedLayout>
</template>
