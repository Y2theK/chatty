<script setup>
import Dashboard from "@/Pages/Dashboard.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick, onBeforeUnmount, onMounted, ref, watch, computed } from "vue";
import { useToast } from "@/components/ui/toast";
import { Button } from "@/components/ui/button";
import { Link } from "@inertiajs/vue3";
import {
    ChevronLeft,
    ChevronRight,
    LogOut,
    Plus,
    Trash,
    Reply,
    CircleX,
    Download,
    Video,
    Paperclip,
    Send,
    Smile,
    Users,
} from "lucide-vue-next";
import AvatarInitials from "@/Components/AvatarInitials.vue";
import TypingIndicator from "@/Components/TypingIndicator.vue";
import DateSeparator from "@/Components/DateSeparator.vue";
import MessageStatus from "@/Components/MessageStatus.vue";

import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import moment from "moment";
import axios from "axios";
import { VueChatEmojiComponent } from "@nguyenvanlong/vue3-chat-emoji";
import "@nguyenvanlong/vue3-chat-emoji/dist/index.mjs.css";

const props = defineProps({
    conversations: {
        required: true,
    },
    conversation: {
        required: true,
    },
    messages: {
        required: true,
    },
    auth: {
        require: true,
    },
});
const { toast } = useToast();

const form = useForm({
    message: null,
    file: null,
    replyMessageId: null,
});

watch(() => form.errors.file, (error) => {
    if (error) {
        toast({
            title: "Invalid file format",
            description: "The file you selected is not supported. Please upload an image, video, audio, document, or archive.",
            variant: "destructive",
        });
        form.file = null;
    }
});

const addedEmail = ref("");
const messages = ref([...props.messages.data.reverse()]);
const messageContainer = ref(null);
const onlineUsers = ref([]);
const isUserTyping = ref(false);
const typingUserName = ref("");
const isUserTypingTimer = ref(null);

const users = ref([...props.conversation.users]);

const open = ref(false);

const visibleMessages = computed(() => {
    return messages.value.filter(msg => 
        (msg.message && msg.message.trim() !== '') || msg.upload
    );
});

const cursorPosition = ref(0);
const textInput = ref(null);

const updateCursorPosition = (event) => {
    cursorPosition.value = event.target.selectionStart;
};

const selectedEmoji = async (args) => {
    const input = textInput.value;
    const emoji = args.unicode;
    const before = form.message ? form.message.slice(0, cursorPosition.value) : '';
    const after = form.message ? form.message.slice(cursorPosition.value) : '';
    form.message = before + emoji + after;

    await nextTick();
    cursorPosition.value += emoji.length;
    input.setSelectionRange(cursorPosition.value, cursorPosition.value);
    input.focus();
};

const replyMessageText = ref("");
const previewImage = ref(null);

const loadPreviewFile = (event) => {
   const file = event.target.files[0];   
   previewImage.value = URL.createObjectURL(file);   
};

const submit = () => {
    if (!form.message?.trim() && !form.file) {
        return;
    }
    form.post(route("messages.store", props.conversation.id), {
        message: form.message?.trim() || null,
        replyMessageId: form.replyMessageId,
        file : form.file,
        onFinish: () => {
            form.message = null;
            replyMessageText.value = null;
            form.replyMessageId = null;
            form.file = null;
            previewImage.value = null;
        },
    });
};

const replyMessage = async (id, message) => {
    textInput.value.focus();
    replyMessageText.value = message ?? 'File Message';
    form.replyMessageId = id;
};

const deleteReplyMessage = () => {
    replyMessageText.value = "";
    form.replyMessageId = null;
};

const inviteToGroup = async () => {
    if (addedEmail.value.trim() !== "") {
        try {
            const response = await axios.post(
                `/conversations/${props.conversation.id}/add`,
                { email: addedEmail.value }
            );
            addedEmail.value = "";
            location.reload();
        } catch (error) {
            console.error("Failed to send message:", error);
        }
    }
};

const deleteMessage = async (id) => {
    try {
        document.getElementById(`message-${id}`).innerHTML = "Message deleted";
        const response = await axios.delete(
            `/conversations/${props.conversation.id}/messages/${id}`
        );
    } catch (error) {
        console.error("Failed to send message:", error);
    }
};

const leaveGroup = async () => {
    try {
        const response = await axios.delete(
            `/conversations/${props.conversation.id}/leave`
        );
        window.location.href = response.data.redirect;
    } catch (error) {
        console.error("Failed to send message:", error);
    }
};

const activeRoom = ref(null);

const startCall = async () => {
    const res = await axios.post(`/conversations/${props.conversation.id}/call/start`);
    window.dispatchEvent(new CustomEvent('call:started', {
        detail: {
            token: res.data.token,
            livekitUrl: res.data.livekit_url,
            roomName: res.data.room_name,
            conversationId: props.conversation.id,
        },
    }));
};

const rejoinCall = async () => {
    const res = await axios.post(`/conversations/${props.conversation.id}/call/join`, {
        room_name: activeRoom.value.roomName,
    });
    window.dispatchEvent(new CustomEvent('call:started', {
        detail: {
            token: res.data.token,
            livekitUrl: res.data.livekit_url,
            roomName: res.data.room_name,
            conversationId: props.conversation.id,
        },
    }));
};

const addSeenByUser = async () => {
    try {
        const response = await axios.post(
            `/conversations/${props.conversation.id}/messages/add-seen-by`
        );
        return response;
    } catch (error) {
        console.error("Failed to send message:", error);
    }
};

watch(
    messages,
    () => {
        nextTick(() => {
            scrollToHeight();
        });
    },
    { deep: true }
);

const sendTypingEvent = () => {
    window.Echo.private(`conversation.${props.conversation.id}`).whisper(
        "typing",
        {
            userID: props.auth.user.id,
            userName: props.auth.user.name,
        }
    );
};

const scrollToHeight = () => {
    messageContainer.value?.scrollTo({
        top: messageContainer.value.scrollHeight,
        behavior: "smooth",
    });
};

const getOtherUser = computed(() => {
    if (props.conversation.is_group) return null;
    return users.value.find(u => u.id !== props.auth.user.id);
});

const isUserOnline = (userId) => {
    return onlineUsers.value.some(u => u.id === userId);
};

const formatMessageTime = (date) => {
    return moment(date).format('h:mm A');
};

const shouldShowDateSeparator = (index) => {
    if (index === 0) return true;
    const current = moment(messages.value[index].created_at).format('YYYY-MM-DD');
    const previous = moment(messages.value[index - 1].created_at).format('YYYY-MM-DD');
    return current !== previous;
};

const getMessageFileIcon = (type) => {
    switch (type) {
        case 'image': return 'image';
        case 'video': return 'video';
        case 'audio': return 'audio';
        default: return 'file';
    }
};

onMounted(() => {
    scrollToHeight();

    window.Echo.private(`conversation.${props.conversation.id}`)
        .listen("ChatMessageSent", async (response) => {
            const res = await addSeenByUser();
            messages.value.push(res.data.data);
        })
        .listen("CallEnded", () => {
            activeRoom.value = null;
            window.dispatchEvent(new CustomEvent('call:ended'));
        })
        .listenForWhisper("typing", (response) => {
            isUserTyping.value = response.userID !== props.auth.user.id;
            typingUserName.value = response.userName;

            if (isUserTypingTimer.value) {
                clearTimeout(isUserTypingTimer.value);
            }

            isUserTypingTimer.value = setTimeout(() => {
                isUserTyping.value = false;
            }, 1000);
        });

    window.addEventListener('call:left', (e) => {
        if (e.detail.conversationId === props.conversation.id) {
            activeRoom.value = { roomName: e.detail.roomName, conversationId: e.detail.conversationId };
        }
    });

    window.addEventListener('call:ended', () => {
        activeRoom.value = null;
    });

    window.Echo.join(`online`)
        .here((users) => {
            onlineUsers.value = users;
        })
        .joining((user) => {
            onlineUsers.value.push(user);
        })
        .leaving((user) => {
            onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
        });
});

onBeforeUnmount(() => {
    window.Echo.leave(`conversation.${props.conversation.id}`, (user) => {
        onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
    });
});
</script>

<template>
    <Dashboard :conversations="conversations">
    <div class="flex flex-col h-full overflow-hidden">
        <!-- Chat Header -->
        <header class="flex items-center justify-between px-4 py-3 border-b border-border-default bg-bg-surface">
            <div class="flex items-center gap-3">
                <Link :href="route('conversations.index')">
                    <Button variant="ghost" size="icon" class="h-9 w-9 rounded-xl">
                        <ChevronLeft class="w-5 h-5" />
                    </Button>
                </Link>

                <div class="flex items-center gap-3">
                    <AvatarInitials
                        v-if="!conversation.is_group"
                        :user="getOtherUser"
                        :show-online="true"
                        :is-online="isUserOnline(getOtherUser?.id)"
                        size="md"
                    />
                    <div v-else>
                        <div class="flex -space-x-2">
                            <AvatarInitials
                                v-for="(user, idx) in conversation.users.slice(0, 3)"
                                :key="user.id"
                                :user="user"
                                size="sm"
                            />
                        </div>
                    </div>

                    <div>
                        <h2 class="font-semibold text-text-primary">
                            {{ conversation.is_group ? (conversation.name || 'Group Chat') : (getOtherUser?.name || 'Unknown') }}
                        </h2>
                        <p class="text-xs text-text-muted">
                            <template v-if="!conversation.is_group">
                                <span v-if="isUserOnline(getOtherUser?.id)" class="flex items-center gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-status-online"></span>
                                    Active now
                                </span>
                                <span v-else-if="getOtherUser?.last_active_at">
                                    Last seen {{ moment(getOtherUser.last_active_at).fromNow() }}
                                </span>
                                <span v-else>Offline</span>
                            </template>
                            <span v-else>{{ conversation.users.length }} members</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Button v-if="activeRoom && activeRoom.conversationId === conversation.id" variant="outline" class="h-9 rounded-xl text-status-online border-status-online">
                    <Video class="w-4 h-4 mr-1.5" />
                    Rejoin Call
                </Button>
                <Button v-else variant="outline" class="h-9 rounded-xl" @click="startCall">
                    <Video class="w-4 h-4 mr-1.5" />
                    Call
                </Button>

                <Dialog v-if="conversation.is_group">
                    <DialogTrigger as-child>
                        <Button variant="outline" size="icon" class="h-9 w-9 rounded-xl">
                            <Plus class="w-4 h-4" />
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-sm">
                        <DialogHeader class="mb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: oklch(0.96 0.04 50);">
                                    <Users class="w-5 h-5" style="color: oklch(0.65 0.18 50);" />
                                </div>
                                <DialogTitle class="text-lg" style="color: oklch(0.15 0.01 60);">Add new member</DialogTitle>
                            </div>
                            <DialogDescription class="text-sm" style="color: oklch(0.45 0.01 60);">
                                Invite someone to join this group by entering their email address.
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="inviteToGroup" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="invite-email" class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">Email address</Label>
                                <Input 
                                    id="invite-email" 
                                    type="email" 
                                    v-model="addedEmail" 
                                    placeholder="friend@example.com"
                                    class="h-11 rounded-xl"
                                    style="border-color: oklch(0.91 0.005 60); background-color: oklch(0.99 0.003 60);"
                                />
                            </div>
                            <Button 
                                type="submit" 
                                class="w-full h-11 rounded-xl text-white font-medium"
                                style="background-color: oklch(0.65 0.18 50);"
                                :disabled="!addedEmail"
                            >
                                Send Invitation
                            </Button>
                        </form>
                    </DialogContent>
                </Dialog>

                <Button v-if="conversation.is_group" variant="outline" class="h-9 rounded-xl text-destructive border-destructive/50 hover:bg-destructive/10" @click="leaveGroup">
                    <LogOut class="w-4 h-4 mr-1.5" />
                    Leave
                </Button>
            </div>
        </header>

        <!-- Messages Area -->
        <div
            ref="messageContainer"
            class="flex-1 overflow-y-auto py-4 px-4 min-h-0 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-border-default [&::-webkit-scrollbar-thumb]:rounded-full"
        >
            <div v-if="visibleMessages.length" class="space-y-4">
                <template v-for="(message, index) in visibleMessages" :key="message.id">
                    <!-- Date Separator -->
                    <DateSeparator
                        v-if="shouldShowDateSeparator(index)"
                        :date="message.created_at"
                    />

                    <!-- System Message -->
                    <div v-if="message.type === 'system'" class="flex justify-center">
                        <div class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-bg-surface-raised border border-border-subtle">
                            <Video class="w-3 h-3 text-text-muted" />
                            <span class="text-xs text-text-muted">{{ message.message }}</span>
                            <span class="text-[10px] text-text-muted">·</span>
                            <span class="text-[10px] text-text-muted">{{ formatMessageTime(message.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Chat Message -->
                    <div
                        v-else
                        class="flex animate-slide-up"
                        :class="auth.user.id === message.user_id ? 'justify-end' : 'justify-start'"
                    >
                        <div
                            class="group flex items-end gap-2 max-w-[70%]"
                            :class="auth.user.id === message.user_id ? 'flex-row-reverse' : 'flex-row'"
                        >
                            <!-- Avatar (only show for received messages and first in a sequence) -->
                            <AvatarInitials
                                v-if="auth.user.id !== message.user_id"
                                :user="message.user"
                                :show-online="true"
                                :is-online="isUserOnline(message.user.id)"
                                size="sm"
                                class="flex-shrink-0"
                            />
                            <div v-else class="w-6 flex-shrink-0"></div>

                            <!-- Message Bubble -->
                            <div class="relative">
                                <!-- Reply Preview -->
                                <a
                                    v-if="message.reply"
                                    :href="'#message-' + message.chat_message_id"
                                    class="flex items-center gap-2 mb-2 p-2 rounded-lg bg-black/5 hover:bg-black/10 transition-colors"
                                >
                                    <Reply class="w-3 h-3 text-text-muted flex-shrink-0" />
                                    <span class="text-xs font-medium text-text-secondary truncate">
                                        {{ message.reply?.message || 'File Message' }}
                                    </span>
                                </a>

                                <!-- Media -->
                                <div v-if="message.upload" class="mb-2">
                                    <img
                                        v-if="message.upload.type === 'image'"
                                        :src="'/uploads/'+message.upload.id"
                                        alt=""
                                        class="rounded-xl max-w-[280px] max-h-[280px] object-cover"
                                    />
                                    <video
                                        v-else-if="message.upload.type === 'video'"
                                        :src="'/uploads/'+message.upload.id"
                                        class="rounded-xl max-w-[280px] max-h-[280px] object-cover"
                                        controls
                                    ></video>
                                    <audio
                                        v-else-if="message.upload.type === 'audio'"
                                        :src="'/uploads/'+message.upload.id"
                                        class="w-[280px] rounded-xl"
                                        controls
                                    ></audio>
                                    <a
                                        v-else
                                        :href="'/uploads/'+message.upload.id"
                                        target="_blank"
                                        class="flex items-center gap-2 p-3 rounded-xl bg-black/5 hover:bg-black/10 transition-colors"
                                    >
                                        <Download class="w-4 h-4 text-text-muted" />
                                        <span class="text-sm text-text-secondary truncate">
                                            {{ message.upload.file_original_name }}
                                        </span>
                                    </a>
                                </div>

                                <!-- Text Content -->
                                <div
                                    :id="'message-' + message.id"
                                    class="relative rounded-2xl px-4 py-2.5 shadow-sm"
                                    :class="auth.user.id === message.user_id
                                        ? 'bg-bubble-sent text-bubble-sent-text rounded-br-md'
                                        : 'bg-bubble-received text-bubble-received-text border border-border-default rounded-bl-md'
                                    "
                                >
                                    <!-- Sender name for group chats -->
                                    <p
                                        v-if="conversation.is_group && auth.user.id !== message.user_id"
                                        class="text-xs font-semibold mb-1 text-accent-primary"
                                    >
                                        {{ message.user.name }}
                                    </p>

                                    <p v-if="message.message?.trim()" class="text-sm leading-relaxed whitespace-pre-wrap break-words">
                                        {{ message.message }}
                                    </p>

                                    <div
                                        class="flex items-center justify-end gap-1 mt-1"
                                        :class="auth.user.id === message.user_id ? 'flex-row-reverse' : ''"
                                    >
                                        <span
                                            class="text-[10px]"
                                            :class="auth.user.id === message.user_id ? 'text-white/70' : 'text-text-muted'"
                                        >
                                            {{ formatMessageTime(message.created_at) }}
                                        </span>
                                        <MessageStatus
                                            v-if="auth.user.id === message.user_id"
                                            :is-read="message.seen_by"
                                            :is-sent="true"
                                        />
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="absolute -top-3 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1"
                                    :class="auth.user.id === message.user_id ? '-left-2' : '-right-2'"
                                >
                                    <button
                                        @click="replyMessage(message.id, message.message)"
                                        class="flex items-center justify-center h-6 w-6 rounded-full bg-bg-surface border border-border-default shadow-sm hover:bg-bg-surface-raised transition-colors"
                                        title="Reply"
                                    >
                                        <Reply class="w-3 h-3 text-text-secondary" />
                                    </button>
                                    <button
                                        v-if="auth.user.id === message.user_id"
                                        @click="deleteMessage(message.id)"
                                        class="flex items-center justify-center h-6 w-6 rounded-full bg-bg-surface border border-border-default shadow-sm hover:bg-destructive/10 transition-colors"
                                        title="Delete"
                                    >
                                        <Trash class="w-3 h-3 text-destructive" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center h-full text-center">
                <div class="w-20 h-20 rounded-full bg-bg-surface-raised flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <p class="text-lg font-semibold text-text-secondary">No messages yet</p>
                <p class="text-sm text-text-muted mt-1">Say hi to start the conversation!</p>
            </div>
        </div>

        <!-- Typing Indicator -->
        <TypingIndicator
            v-if="isUserTyping"
            :user-name="typingUserName"
        />

        <!-- Reply Preview -->
        <div
            v-if="replyMessageText"
            class="flex items-center justify-between px-4 py-2 bg-accent-primary-subtle border-t border-border-default"
        >
            <div class="flex items-center gap-2">
                <Reply class="w-4 h-4 text-accent-primary" />
                <span class="text-sm text-text-secondary">Replying to:</span>
                <span class="text-sm text-text-primary truncate max-w-[200px]">{{ replyMessageText }}</span>
            </div>
            <button @click="deleteReplyMessage" class="p-1 hover:bg-black/5 rounded-lg transition-colors">
                <CircleX class="w-4 h-4 text-text-muted" />
            </button>
        </div>

        <!-- Message Input -->
        <div class="p-4 border-t border-border-default bg-bg-surface">
            <form @submit.prevent="submit" class="flex items-end gap-2">
                <!-- Emoji Button -->
                <div class="relative">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex items-center justify-center h-10 w-10 rounded-xl hover:bg-bg-surface-raised transition-colors"
                    >
                        <Smile class="w-5 h-5 text-text-muted" />
                    </button>
                    
                    <!-- Emoji Picker -->
                    <div v-if="open" class="absolute left-0 bottom-full mb-2 z-50">
                        <VueChatEmojiComponent
                            :open="open"
                            @handle="selectedEmoji"
                            width="300px"
                            class="shadow-xl rounded-xl border bg-white"
                        />
                    </div>
                </div>

                <!-- Attachment Button -->
                <label class="flex items-center justify-center h-10 w-10 rounded-xl cursor-pointer hover:bg-bg-surface-raised transition-colors">
                    <input type="file" class="hidden" @input="form.file = $event.target.files[0]" @change="submit" />
                    <Paperclip class="w-5 h-5 text-text-muted" />
                </label>

                <!-- Text Input -->
                <div class="flex-1 relative">
                    <input
                        type="text"
                        class="w-full h-11 px-4 rounded-xl bg-bg-input border border-border-default text-text-primary placeholder:text-text-muted focus:outline-none focus:border-input-border-focus transition-colors"
                        v-model="form.message"
                        ref="textInput"
                        placeholder="Type a message..."
                        @keydown="sendTypingEvent"
                        @click="updateCursorPosition"
                        @keyup="updateCursorPosition"
                    />
                </div>

                <!-- Send Button -->
                <Button
                    type="submit"
                    size="icon"
                    class="h-10 w-10 rounded-xl bg-button-send hover:bg-button-send-hover transition-colors"
                    :disabled="!form.message && !form.file"
                >
                    <Send class="w-4 h-4" />
                </Button>
            </form>
        </div>
    </div>
</Dashboard>
</template>
