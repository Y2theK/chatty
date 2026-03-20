<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Checkbox from '@/Components/Checkbox.vue';
import { Mail, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-center p-3 rounded-xl" style="background-color: oklch(0.94 0.04 160); color: oklch(0.15 0.01 60);">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold mb-1" style="color: oklch(0.15 0.01 60);">
                    Welcome back
                </h1>
                <p style="color: oklch(0.45 0.01 60);">
                    Sign in to continue chatting
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
                        autofocus
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
                        autocomplete="current-password"
                        placeholder="Enter your password"
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

            <!-- Remember -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded" />
                    <span class="text-sm" style="color: oklch(0.45 0.01 60);">Remember me</span>
                </label>
            </div>

            <!-- Submit -->
            <Button
                type="submit"
                class="w-full h-12 rounded-xl font-semibold text-white text-base"
                style="background-color: oklch(0.65 0.18 50);"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                Sign in
            </Button>
        </form>

        <!-- Register Link -->
        <p class="text-center text-sm mt-6" style="color: oklch(0.45 0.01 60);">
            Don't have an account?
            <Link
                :href="route('register')"
                class="font-semibold hover:underline"
                style="color: oklch(0.65 0.18 50);"
            >
                Create one
            </Link>
        </p>
    </GuestLayout>
</template>
