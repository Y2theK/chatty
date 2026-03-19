<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, onMounted, onUnmounted, ref } from "vue";
import { Button } from "@/components/ui/button";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { PlusSquareIcon, Search, UserPen, MessageCircle } from "lucide-vue-next";
import AvatarInitials from "@/Components/AvatarInitials.vue";
import moment from "moment";

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { toTypedSchema } from "@vee-validate/zod";
import { useForm } from "vee-validate";

import { h } from "vue";
import * as z from "zod";

const appName = import.meta.env.VITE_APP_NAME || 'Chatty';

const formSchema = toTypedSchema(
    z.object({
        message: z.string().min(2).max(50),
        email: z.string().email(),
    })
);

const form = useForm({
    validationSchema: formSchema,
});

const props = defineProps({
    conversations: {
        required: true,
    },
    allOnlineUsers: {
        required: true,
    },
});

const conversations = computed(() => {
    return props.conversations.sort(
        (a, b) => new Date(b.updated_at) - new Date(a.updated_at)
    );
});

const user = computed(() => usePage().props.auth.user);

const searchUsers = ref([]);
const search = ref(null);

const isLatestMessageSeenByAuthUser = (conversation) => {
    if (conversation.latest_message.user_id == user.value.id) {
        return true;
    }
    if (conversation.latest_message.seen_by) {
        return conversation.latest_message.seen_by
            .split(",")
            .includes(user.value.id.toString());
    }
    return false;
};

const getUnreadCount = (conversation) => {
    return conversation.unread_count || 0;
};

const formatTimestamp = (timestamp) => {
    if (!timestamp) return '';
    const date = moment(timestamp);
    const now = moment();
    
    if (date.isSame(now, 'day')) {
        return date.format('h:mm A');
    } else if (date.isSame(now.subtract(1, 'day'), 'day')) {
        return 'Yesterday';
    } else if (date.isAfter(now.subtract(7, 'day'))) {
        return date.format('ddd');
    }
    return date.format('MMM D');
};

const getOtherUser = (conversation) => {
    if (conversation.is_group) return null;
    return conversation.users.find(u => u.id !== user.value.id);
};

const isUserOnline = (userId) => {
    return props.allOnlineUsers.some(u => u.id === userId);
};

const moveConversationOnTop = (updatedConversation) => {
    const index = conversations.value.findIndex(
        (c) => c.id === updatedConversation.id
    );

    if (index !== -1) {
        conversations.value.splice(index, 1);
    }
    conversations.value.unshift(updatedConversation);
};

onMounted(() => {
    window.Echo.private(`user.${user.value.id}`).listen(
        "ConversationUpdate",
        (response) => {
            moveConversationOnTop(response.conversation);
        }
    );
});

onUnmounted(() => {
    updateLastActiveAt();
});

const onSubmit = form.handleSubmit(async (values) => {
    await createConversation(values.email, values.message);
});

const createConversation = async (email, message) => {
    try {
        const response = await axios.post(`/conversations/create`, {
            email: email,
            message: message,
        });
        window.location.href = response.data.redirect;
    } catch (error) {
        console.error("Failed to send message:", error);
    }
};

const updateLastActiveAt = async (id) => {
    try {
        const response = await axios.post(`/users/updateLastActiveAt`);
    } catch (error) {
        console.error("Failed to send message:", error);
    }
};

const fetchUsers = async () => {
    const params = { search: search.value };

    try {
        const response = await axios.get(route("users.index"), { params });
        searchUsers.value = response.data;
    } catch (error) {
        console.error("Failed to fetch users:", error);
    }
};
</script>

<template>
    <aside
        class="flex flex-col h-full w-80 flex-shrink-0 bg-bg-surface border-r border-border-default"
    >
        <!-- App Header -->
        <div class="flex items-center justify-between px-4 py-4">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <div class="flex items-center justify-center rounded-xl h-10 w-10" style="background-color: oklch(0.65 0.18 50);">
                    <MessageCircle class="w-5 h-5 text-white" />
                </div>
                <span class="font-semibold text-xl" style="color: oklch(0.15 0.01 60);">{{ appName }}</span>
            </Link>
        </div>

        <!-- User Profile Card -->
        <div class="mx-4 p-4 bg-bg-surface-raised rounded-xl border border-border-subtle">
            <div class="flex items-center gap-3">
                <AvatarInitials :user="user" size="lg" :show-online="true" :is-online="true" />
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-text-primary truncate">{{ user.name }}</p>
                    <p class="text-xs text-text-muted truncate">{{ user.email }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-status-online"></span>
                    <span class="text-xs text-text-secondary">Active</span>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="route('profile.edit')">
                        <Button variant="ghost" size="icon" class="h-8 w-8">
                            <UserPen class="w-4 h-4 text-text-secondary" />
                        </Button>
                    </Link>
                    <ResponsiveNavLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="inline-flex items-center justify-center h-8 px-3 rounded-lg text-xs font-medium bg-destructive text-destructive-foreground hover:bg-destructive/90"
                    >
                        Quit
                    </ResponsiveNavLink>
                </div>
            </div>
        </div>

        <!-- Search & New Chat -->
        <div class="px-4 py-3 space-y-2">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted" />
                <Input
                    id="search"
                    type="text"
                    placeholder="Search conversations..."
                    class="pl-9 h-10 bg-bg-input border-border-default rounded-xl focus:border-accent-primary"
                    v-model="search"
                    @keyup="fetchUsers"
                />
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="outline" class="w-full h-10 rounded-xl" style="border-color: oklch(0.91 0.005 60);">
                        <PlusSquareIcon class="w-4 h-4 mr-2" />
                        New Conversation
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[425px] rounded-2xl" style="border-color: oklch(0.91 0.005 60);">
                    <DialogHeader>
                        <DialogTitle class="text-lg font-semibold" style="color: oklch(0.15 0.01 60);">
                            New Conversation
                        </DialogTitle>
                        <DialogDescription class="text-sm" style="color: oklch(0.45 0.01 60);">
                            Start a new conversation by inviting someone you know.
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit="onSubmit" class="space-y-4">
                        <FormField v-slot="{ componentField }" name="email">
                            <FormItem class="space-y-2">
                                <FormLabel class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">Email</FormLabel>
                                <FormControl>
                                    <Input
                                        type="email"
                                        placeholder="friend@example.com"
                                        class="h-11 rounded-xl border"
                                        style="border-color: oklch(0.91 0.005 60); background-color: oklch(0.99 0.003 60);"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <FormField
                            v-slot="{ componentField }"
                            name="message"
                        >
                            <FormItem class="space-y-2">
                                <FormLabel class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">Message (optional)</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Say hello..."
                                        class="h-11 rounded-xl border"
                                        style="border-color: oklch(0.91 0.005 60); background-color: oklch(0.99 0.003 60);"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <div class="flex justify-end gap-3 pt-2">
                            <Button
                                type="button"
                                variant="outline"
                                class="h-10 rounded-xl px-5"
                            >
                                Cancel
                            </Button>
                            <Button
                                class="h-10 rounded-xl px-5"
                                style="background-color: oklch(0.65 0.18 50);"
                                type="submit"
                            >
                                Start Chat
                            </Button>
                        </div>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto px-2 pb-4 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-border-default [&::-webkit-scrollbar-thumb]:rounded-full">
            <!-- Conversations -->
            <div v-show="!search" class="space-y-1">
                <Link
                    :href="route('conversations.show', conversation.id)"
                    v-for="conversation in conversations"
                    :key="conversation.id"
                    class="block"
                >
                    <div
                        :class="[
                            'flex items-center gap-3 p-3 rounded-xl transition-colors hover:bg-bg-surface-raised',
                            route().current('conversations.show', conversation.id)
                                ? 'bg-accent-primary-subtle'
                                : '',
                        ]"
                    >
                        <!-- Avatar -->
                        <AvatarInitials
                            v-if="!conversation.is_group"
                            :user="getOtherUser(conversation)"
                            :show-online="true"
                            :is-online="isUserOnline(getOtherUser(conversation)?.id)"
                            size="md"
                        />
                        <div v-else class="relative">
                            <div class="flex -space-x-2">
                                <AvatarInitials
                                    v-for="(convUser, idx) in conversation.users.slice(0, 3)"
                                    :key="convUser.id"
                                    :user="convUser"
                                    size="sm"
                                />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span
                                    :class="[
                                        'text-sm truncate',
                                        getUnreadCount(conversation) > 0
                                            ? 'font-semibold text-text-primary'
                                            : 'font-medium text-text-secondary',
                                    ]"
                                >
                                    {{
                                        conversation.is_group
                                            ? conversation.name || 'Group Chat'
                                            : getOtherUser(conversation)?.name || 'Unknown'
                                    }}
                                </span>
                                <span class="text-[11px] text-text-muted ml-2 flex-shrink-0">
                                    {{ formatTimestamp(conversation.latest_message?.created_at) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-0.5">
                                <p
                                    :class="[
                                        'text-xs truncate',
                                        getUnreadCount(conversation) > 0
                                            ? 'text-text-secondary font-medium'
                                            : 'text-text-muted',
                                    ]"
                                >
                                    {{ conversation.latest_message?.message?.slice(0, 30) || 'No messages yet' }}
                                </p>
                                <span
                                    v-if="getUnreadCount(conversation) > 0"
                                    class="ml-2 flex-shrink-0 flex items-center justify-center h-5 min-w-5 px-1.5 rounded-full bg-accent-primary text-text-inverse text-[10px] font-semibold animate-scale-pop"
                                >
                                    {{ getUnreadCount(conversation) > 99 ? '99+' : getUnreadCount(conversation) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </Link>

                <!-- Empty State -->
                <div v-if="conversations.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-bg-surface-raised flex items-center justify-center mb-4">
                        <MessageCircle class="w-8 h-8 text-text-muted" />
                    </div>
                    <p class="text-sm font-medium text-text-secondary">No conversations yet</p>
                    <p class="text-xs text-text-muted mt-1">Start a new conversation!</p>
                </div>
            </div>

            <!-- Search Results -->
            <div v-show="search" class="space-y-1">
                <div
                    v-for="searchUser in searchUsers"
                    :key="searchUser.id"
                    class="flex items-center gap-3 p-3 rounded-xl transition-colors hover:bg-bg-surface-raised cursor-pointer"
                    @click="createConversation(searchUser.email)"
                >
                    <AvatarInitials
                        :user="searchUser"
                        :show-online="true"
                        :is-online="isUserOnline(searchUser.id)"
                    />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-primary truncate">{{ searchUser.name }}</p>
                        <p class="text-xs text-text-muted truncate">{{ searchUser.email }}</p>
                    </div>
                </div>
                <div v-if="searchUsers.length === 0 && search" class="text-center py-8">
                    <p class="text-sm text-text-muted">No users found</p>
                </div>
            </div>
        </div>
    </aside>
</template>
