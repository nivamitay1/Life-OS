<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

// Sub-components
import TrainingCalendar from './Components/TrainingCalendar.vue';
import RecentRunsList from './Components/RecentRunsList.vue';
import CustomPlanModal from './Components/CustomPlanModal.vue';
import AddWorkoutModal from './Components/AddWorkoutModal.vue';
import CompleteWorkoutModal from './Components/CompleteWorkoutModal.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  runs: Array,
  stats: Object,
  levelData: Object,
  activePlan: Object,
  templates: Array,
  draftDistance: [String, Number],
});

onMounted(() => {
    if (props.draftDistance) {
        const fakeWorkout = {
            id: null,
            title: `Ad-Hoc Run (${props.draftDistance} km)`,
            target_distance_km: props.draftDistance,
            target_duration_sec: null,
        };
        openCompleteWorkoutMenu(fakeWorkout);
    }
});

// Stats Computers
const totalRunsThisMonth = computed(() => {
    const startOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
    return props.runs.filter(r => new Date(r.date) >= startOfMonth).length;
});

const longestRunDist = computed(() => {
    if (!props.runs.length) return 0;
    return Math.max(...props.runs.map(r => parseFloat(r.distance))).toFixed(1);
});

// Modals State
const showCustomPlanModal = ref(false);
const showAddWorkoutModal = ref(false);
const showAbandonPlanModal = ref(false);
const showCompleteWorkoutModal = ref(false);

const selectedWeekId = ref(null);
const selectedDateStr = ref('');
const selectedWorkout = ref(null);
const selectedEditWorkout = ref(null);

const openCustomPlanMenu = () => { showCustomPlanModal.value = true; };
const closeCustomPlanMenu = () => { showCustomPlanModal.value = false; };

const openAddWorkoutMenu = (weekId, dateStr, editWorkout = null) => { 
    selectedWeekId.value = weekId; 
    selectedDateStr.value = dateStr;
    selectedEditWorkout.value = editWorkout;
    showAddWorkoutModal.value = true; 
};
const closeAddWorkoutMenu = () => { showAddWorkoutModal.value = false; };

const openCompleteWorkoutMenu = (workout) => {
    selectedWorkout.value = workout;
    showCompleteWorkoutModal.value = true;
};
const closeCompleteWorkoutMenu = () => { showCompleteWorkoutModal.value = false; };

const openAbandonPlanModal = () => { showAbandonPlanModal.value = true; };
const cancelPlan = () => {
    if (props.activePlan) {
        useForm({}).delete(route('running.plan.destroy', props.activePlan.id), { 
            preserveScroll: true,
            onSuccess: () => {
                showAbandonPlanModal.value = false;
            }
        });
    }
};

</script>

<template>
    <Head title="Running | Life OS" />

    <AuthenticatedLayout>
        <div class="space-y-12 mt-2 px-2">
            
            <div class="flex items-center justify-between">
                <h1 class="text-[10px] uppercase tracking-[0.2em] font-bold text-atlas-muted border border-atlas-border px-4 py-2 rounded-lg bg-atlas-panel shadow-sm">Running Workspace</h1>
            </div>

            <!-- Floating Data Row (Key Stats) -->
            <div class="flex flex-wrap gap-x-16 gap-y-8 px-4 items-center">
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Weekly Mileage</dt>
                    <dd class="text-3xl font-serif text-atlas-text">{{ stats.weeklyMileage }} <span class="text-sm font-sans text-atlas-muted">km</span></dd>
                </div>
                <div class="w-px h-12 bg-atlas-border/50 hidden md:block"></div>
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Total Runs (Month)</dt>
                    <dd class="text-3xl font-serif text-atlas-text">{{ totalRunsThisMonth }}</dd>
                </div>
                <div class="w-px h-12 bg-atlas-border/50 hidden md:block"></div>
                <div>
                    <dt class="text-[10px] font-bold text-atlas-muted uppercase tracking-widest mb-1.5">Longest Run</dt>
                    <dd class="text-3xl font-serif text-atlas-text">{{ longestRunDist }} <span class="text-sm font-sans text-atlas-muted">km</span></dd>
                </div>
            </div>

            <TrainingCalendar 
                :activePlan="activePlan" 
                :templates="templates" 
                @openCustomPlan="openCustomPlanMenu"
                @openAddWorkout="openAddWorkoutMenu"
                @openCompleteWorkout="openCompleteWorkoutMenu"
                @confirmAbandonPlan="openAbandonPlanModal"
            />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pt-6">
                <div class="lg:col-span-2 space-y-12">
                    <RecentRunsList :runs="runs" />
                </div>
                <div class="space-y-12">
                    <!-- Progress Card -->
                    <div class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl rounded-[24px] shadow-ambient p-8 text-center flex flex-col items-center">
                        <h3 class="text-atlas-muted text-[10px] font-bold uppercase tracking-widest mb-3">Running Mastery</h3>
                        <div v-if="levelData" class="w-full">
                            <div class="text-5xl font-serif text-atlas-text mb-2">{{ levelData.level }}</div>
                            <div class="text-xs tracking-wide text-atlas-muted mb-6">{{ Number(levelData.xp).toLocaleString() }} / {{ Number(levelData.next_level_base).toLocaleString() }} XP</div>
                            <div class="w-full bg-atlas-surface border border-atlas-border/50 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-atlas-text h-1.5 rounded-full" :style="{ width: levelData.progress_percent + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CustomPlanModal :show="showCustomPlanModal" @close="closeCustomPlanMenu" />
        
        <AddWorkoutModal 
            :show="showAddWorkoutModal" 
            :weekId="selectedWeekId" 
            :dateStr="selectedDateStr"
            :editWorkout="selectedEditWorkout"
            @close="closeAddWorkoutMenu" 
        />
        
        <CompleteWorkoutModal 
            :show="showCompleteWorkoutModal"
            :workout="selectedWorkout"
            @close="closeCompleteWorkoutMenu"
        />

        <!-- Abandon Plan Modal (Simple enough to keep inline) -->
        <Modal :show="showAbandonPlanModal" @close="showAbandonPlanModal = false" maxWidth="sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Abandon Plan?</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to stop this training plan? All future scheduled workouts will be permanently removed. Your completed runs are preserved.</p>
                <div class="flex justify-end gap-3">
                    <button @click="showAbandonPlanModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Keep Plan</button>
                    <button @click="cancelPlan" class="px-5 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">Yes, Abandon</button>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
