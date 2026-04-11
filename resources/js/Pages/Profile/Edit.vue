<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdateThemeForm from './Partials/UpdateThemeForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-[10px] uppercase tracking-widest font-bold text-atlas-muted border border-atlas-border px-3 py-1.5 rounded-md bg-atlas-panel shadow-sm">Profile Settings</h1>
                </div>

                <div
                    class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl p-4 shadow-ambient rounded-[24px] sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl p-4 shadow-ambient rounded-[24px] sm:p-8"
                >
                    <UpdateThemeForm class="max-w-xl" />
                </div>

                <div
                    class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl p-4 shadow-ambient rounded-[24px] sm:p-8"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-atlas-panel border border-atlas-border backdrop-blur-2xl p-4 shadow-ambient rounded-[24px] sm:p-8"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
