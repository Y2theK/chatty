<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AvatarInitials from '@/Components/AvatarInitials.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    image: null
});

const submit = () => {
    form.post(route('profile.update'), {
        _method: 'put',
        image: form.image,
        name: form.name,
        email: form.email,
    });
};

const handleFileChange = (event) => {
    form.image = event.target.files[0];
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-5">
        <!-- Avatar Preview -->
        <div class="flex items-center gap-4">
            <AvatarInitials :user="user" size="lg" />
            <div>
                <p class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">{{ user.name }}</p>
                <p class="text-xs" style="color: oklch(0.45 0.01 60);">Profile photo</p>
            </div>
        </div>

        <!-- Name Field -->
        <div class="space-y-2">
            <Label for="name" class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">
                Name
            </Label>
            <Input
                id="name"
                type="text"
                class="h-11 rounded-xl border"
                style="border-color: oklch(0.92 0.005 80); background-color: oklch(0.98 0.002 80);"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
                placeholder="Your name"
            />
            <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
        </div>

        <!-- Email Field -->
        <div class="space-y-2">
            <Label for="email" class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">
                Email
            </Label>
            <Input
                id="email"
                type="email"
                class="h-11 rounded-xl border"
                style="border-color: oklch(0.92 0.005 80); background-color: oklch(0.98 0.002 80);"
                v-model="form.email"
                required
                autocomplete="username"
                placeholder="your@email.com"
            />
            <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>

            <!-- Email Verification -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mt-2 p-3 rounded-lg" style="background-color: oklch(0.95 0.03 250);">
                <p class="text-sm" style="color: oklch(0.15 0.01 60);">
                    Your email address is unverified.
                    <button
                        type="button"
                        class="font-medium underline hover:no-underline"
                        style="color: oklch(0.55 0.15 250);"
                        @click="$inertia.post(route('verification.send'))"
                    >
                        Click here to re-send the verification email.
                    </button>
                </p>
            </div>

            <div
                v-show="status === 'verification-link-sent'"
                class="mt-2 p-3 rounded-lg"
                style="background-color: oklch(0.93 0.04 160);"
            >
                <p class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">
                    A new verification link has been sent to your email address.
                </p>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="space-y-2">
            <Label for="image" class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">
                Profile Photo
            </Label>
            <Input
                id="image"
                type="file"
                accept="image/*"
                class="h-11 rounded-xl border file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium"
                style="border-color: oklch(0.92 0.005 80); background-color: oklch(0.98 0.002 80);"
                @input="handleFileChange"
            />
            <p v-if="form.errors.image" class="text-xs text-destructive">{{ form.errors.image }}</p>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <Button
                type="submit"
                class="h-10 rounded-xl px-6 font-medium text-white"
                style="background-color: oklch(0.65 0.18 50);"
                :disabled="form.processing"
            >
                Save changes
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
