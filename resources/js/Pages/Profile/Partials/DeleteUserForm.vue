<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from '@/components/ui/dialog';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div>
        <Button
            variant="outline"
            class="h-10 rounded-xl px-6 font-medium border-destructive text-destructive hover:bg-destructive/10 hover:text-destructive"
            @click="confirmUserDeletion"
        >
            Delete Account
        </Button>

        <Dialog :open="confirmingUserDeletion" @update:open="confirmingUserDeletion = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Delete Account</DialogTitle>
                    <DialogDescription>
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label for="delete-password" class="sr-only">Password</Label>
                        <Input
                            id="delete-password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="h-11 rounded-xl"
                            placeholder="Enter your password"
                            @keyup.enter="deleteUser"
                        />
                        <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" class="rounded-xl" @click="closeModal">
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        class="rounded-xl"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
