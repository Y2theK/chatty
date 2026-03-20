<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <form @submit.prevent="updatePassword" class="space-y-4">
        <!-- Current Password -->
        <div class="space-y-2">
            <Label for="current_password" class="text-sm font-medium text-gray-700">
                Current Password
            </Label>
            <Input
                id="current_password"
                ref="currentPasswordInput"
                v-model="form.current_password"
                type="password"
                class="h-10 rounded-lg border-gray-300"
                autocomplete="current-password"
                placeholder="Enter current password"
            />
            <p v-if="form.errors.current_password" class="text-xs text-red-600">{{ form.errors.current_password }}</p>
        </div>

        <!-- New Password -->
        <div class="space-y-2">
            <Label for="password" class="text-sm font-medium text-gray-700">
                New Password
            </Label>
            <Input
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="h-10 rounded-lg border-gray-300"
                autocomplete="new-password"
                placeholder="Enter new password"
            />
            <p v-if="form.errors.password" class="text-xs text-red-600">{{ form.errors.password }}</p>
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <Label for="password_confirmation" class="text-sm font-medium text-gray-700">
                Confirm Password
            </Label>
            <Input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="h-10 rounded-lg border-gray-300"
                autocomplete="new-password"
                placeholder="Confirm new password"
            />
            <p v-if="form.errors.password_confirmation" class="text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4 pt-2">
            <Button
                type="submit"
                class="h-10 rounded-lg px-6 font-medium text-white"
                style="background-color: oklch(0.65 0.18 50);"
                :disabled="form.processing"
            >
                Update Password
            </Button>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p
                    v-if="form.recentlySuccessful"
                    class="text-sm font-medium"
                    style="color: oklch(0.65 0.18 50);"
                >
                    Saved.
                </p>
            </Transition>
        </div>
    </form>
</template>
