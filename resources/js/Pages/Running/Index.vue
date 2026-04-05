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
        <template #header>
            <div class="flex justify-between items-center px-2">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Running Workspace</h2>
                </div>
            </div>
        </template>

        <div class="space-y-6 sm:space-y-8 px-2">
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Weekly Mileage</dt>
                    <dd class="mt-2 text-3xl font-black text-gray-900 dark:text-white">{{ stats.weeklyMileage }}</dd>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Runs (Month)</dt>
                    <dd class="mt-2 text-3xl font-black text-orange-600 dark:text-orange-400">{{ totalRunsThisMonth }}</dd>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Longest Run</dt>
                    <dd class="mt-2 text-3xl font-black text-orange-600 dark:text-orange-400">{{ longestRunDist }} km</dd>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <RecentRunsList :runs="runs" />
                </div>
                <div class="space-y-6 sm:space-y-8">
                    <!-- Progress Card -->
                    <div v-if="levelData" class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-sm p-6 text-white text-center">
                        <h3 class="text-orange-100 text-sm font-bold uppercase tracking-wider mb-2">Module Level</h3>
                        <div class="text-4xl font-black mb-1">{{ levelData.level }}</div>
                        <div class="text-sm text-orange-100 mb-4">{{ Number(levelData.xp).toLocaleString() }} / {{ Number(levelData.next_level_base).toLocaleString() }} XP</div>
                        <div class="w-full bg-black/20 rounded-full h-1.5 overflow-hidden mb-2">
                            <div class="bg-white h-1.5 rounded-full" :style="{ width: levelData.progress_percent + '%' }"></div>
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
