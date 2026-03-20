<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { User, Mail, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" class="space-y-4">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold mb-1" style="color: oklch(0.15 0.01 60);">
                    Create account
                </h1>
                <p style="color: oklch(0.45 0.01 60);">
                    Join Chatty and start connecting
                </p>
            </div>

            <!-- Name Field -->
            <div class="space-y-2">
                <Label for="name" class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">
                    Full name
                </Label>
                <div class="relative">
                    <User class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5" style="color: oklch(0.55 0.008 60);" />
                    <Input
                        id="name"
                        type="text"
                        class="h-12 pl-11 rounded-xl border"
                        style="border-color: oklch(0.91 0.005 60); background-color: oklch(0.99 0.003 60);"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="John Doe"
                    />
                </div>
                <p v-if="form.errors.name" class="text-xs" style="color: oklch(0.55 0.15 25);">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Email Field -->
            <div class="space-y-2">
                <Label for="email" class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">
                    Email
                </Label>
                <div class="relative">
                    <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5" style="color: oklch(0.55 0.008 60);" />
                    <Input
                        id="email"
                        type="email"
                        class="h-12 pl-11 rounded-xl border"
                        style="border-color: oklch(0.91 0.005 60); background-color: oklch(0.99 0.003 60);"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="you@example.com"
                    />
                </div>
                <p v-if="form.errors.email" class="text-xs" style="color: oklch(0.55 0.15 25);">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <Label for="password" class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">
                    Password
                </Label>
                <div class="relative">
                    <Lock class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5" style="color: oklch(0.55 0.008 60);" />
                    <Input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="h-12 pl-11 pr-11 rounded-xl border"
                        style="border-color: oklch(0.91 0.005 60); background-color: oklch(0.99 0.003 60);"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="At least 8 characters"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1"
                    >
                        <Eye v-if="!showPassword" class="w-5 h-5" style="color: oklch(0.55 0.008 60);" />
                        <EyeOff v-else class="w-5 h-5" style="color: oklch(0.55 0.008 60);" />
                    </button>
                </div>
                <p v-if="form.errors.password" class="text-xs" style="color: oklch(0.55 0.15 25);">
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-2">
                <Label for="password_confirmation" class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">
                    Confirm password
                </Label>
                <div class="relative">
                    <Lock class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5" style="color: oklch(0.55 0.008 60);" />
                    <Input
                        id="password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        class="h-12 pl-11 pr-11 rounded-xl border"
                        style="border-color: oklch(0.91 0.005 60); background-color: oklch(0.99 0.003 60);"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    />
                    <button
                        type="button"
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1"
                    >
                        <Eye v-if="!showConfirmPassword" class="w-5 h-5" style="color: oklch(0.55 0.008 60);" />
                        <EyeOff v-else class="w-5 h-5" style="color: oklch(0.55 0.008 60);" />
                    </button>
                </div>
                <p v-if="form.errors.password_confirmation" class="text-xs" style="color: oklch(0.55 0.15 25);">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <!-- Submit -->
            <Button
                type="submit"
                class="w-full h-12 rounded-xl font-semibold text-white text-base"
                style="background-color: oklch(0.65 0.18 50);"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                Create account
            </Button>
        </form>

        <!-- Login Link -->
        <p class="text-center text-sm mt-6" style="color: oklch(0.45 0.01 60);">
            Already have an account?
            <Link
                :href="route('login')"
                class="font-semibold hover:underline"
                style="color: oklch(0.65 0.18 50);"
            >
                Sign in
            </Link>
        </p>
    </GuestLayout>
</template>
