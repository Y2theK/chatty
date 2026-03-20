<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ChevronLeft } from 'lucide-vue-next';
import Dashboard from '@/Pages/Dashboard.vue';

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

    <Dashboard :conversations="[]">
        <div class="p-6 max-w-2xl mx-auto">
            <Link :href="route('dashboard')">
                <Button variant="ghost" size="icon" class="mb-4">
                    <ChevronLeft class="w-5 h-5" />
                </Button>
            </Link>

            <div class="space-y-6 pb-6">
                <!-- Profile Information Card -->
                <div class="rounded-xl border p-6 bg-white">
                    <h2 class="text-lg font-semibold mb-1 text-gray-900">Profile Information</h2>
                    <p class="text-sm mb-6 text-gray-600">
                        Update your account's profile information and email address.
                    </p>
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

                <!-- Update Password Card -->
                <div class="rounded-xl border p-6 bg-white">
                    <h2 class="text-lg font-semibold mb-1 text-gray-900">Update Password</h2>
                    <p class="text-sm mb-6 text-gray-600">
                        Ensure your account is using a long, random password to stay secure.
                    </p>
                    <UpdatePasswordForm />
                </div>

                <!-- Delete Account Card -->
                <div class="rounded-xl border p-6 bg-white">
                    <h2 class="text-lg font-semibold mb-1 text-red-600">Delete Account</h2>
                    <p class="text-sm mb-6 text-gray-600">
                        Once your account is deleted, all of its resources and data will be permanently deleted.
                    </p>
                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </Dashboard>
</template>
