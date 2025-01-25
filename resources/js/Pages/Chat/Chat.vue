<script setup>
import Dashboard from "@/Pages/Dashboard.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { Button } from "@/components/ui/button";
import { Link } from "@inertiajs/vue3";
import {
    ChevronLeft,
    ChevronRight,
    LogOut,
    Plus,
    Trash,
    CheckCheck,
    Reply,
    Cross,
    CircleX,
    Download,
} from "lucide-vue-next";

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
const form = useForm({
    message: null,
    file: null,
    replyMessageId: null,
});

const addedEmail = ref("");
const messages = ref([...props.messages.data.reverse()]);
const messageContainer = ref(null);
const onlineUsers = ref([]); //online users in this conversations
const isUserTyping = ref(false);
const typingUserName = ref("");
const isUserTypingTimer = ref(null);

const users = ref([...props.conversation.users]);

const open = ref(false);

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

    // Update cursor position after emoji insertion
    await nextTick();
    cursorPosition.value += emoji.length;
    input.setSelectionRange(cursorPosition.value, cursorPosition.value);
    input.focus();
};

const replyMessageText = ref("");

// preview image snippet
const previewImage = ref(null);

const loadPreviewFile = (event) => {
   const file = event.target.files[0];   
   previewImage.value = URL.createObjectURL(file);   
}

// change to axios request later
const submit = () => {
        
    if (form.message || form.file) {
        form.post(route("messages.store", props.conversation.id), {
            message: form.message,
            replyMessageId: form.replyMessageId,
            file : form.file,
            onFinish: () => {
                form.message = null;
                replyMessageText.value = null;
                form.replyMessageId = null;
                form.file = null;
            },
        });
    }
};

const replyMessage = async (id, message) => {
    textInput.value.focus(); // auto focus on text input when replying
    
    replyMessageText.value = message ?? 'File Message';
    form.replyMessageId = id;
};

const deleteReplyMessage = () => {
    replyMessageText.value = "";
};
const inviteToGroup = async () => {
    if (addedEmail.value.trim() !== "") {
        try {
            const response = await axios.post(
                `/conversations/${props.conversation.id}/add`,
                {
                    email: addedEmail.value,
                }
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
    messageContainer.value.scrollTo({
        top: messageContainer.value.scrollHeight,
        behavior: "smooth",
    });
};

onMounted(() => {
    scrollToHeight();

    window.Echo.private(`conversation.${props.conversation.id}`)
        .listen("ChatMessageSent", async (response) => {
            const res = await addSeenByUser();
            messages.value.push(res.data.data);
            // console.log(response);
            
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

    window.Echo.join(`online`)
        .here((users) => {
            onlineUsers.value = users;
            // console.log("here");
        })
        .joining((user) => {
            // console.log("joining");

            onlineUsers.value.push(user);
        })
        .leaving((user) => {
            onlineUsers.value = onlineUsers.value.filter(
                (u) => u.id !== user.id
            );
            // console.log("leaving");
        });
});

onBeforeUnmount(() => {
    // console.log("unmount");
    window.Echo.leave(`conversation.${props.conversation.id}`, (user) => {
        // console.log("leave");
        onlineUsers.value = onlineUsers.value.filter((u) => u.id !== user.id);
    });
});
</script>

<template>
    <Dashboard :conversations="conversations">
        <div class="justify-between border-b py-4 flex flex-col md:flex-row">
            <div class="flex items-center">
                <Link :href="route('dashboard')">
                    <Button variant="outline" size="icon">
                        <ChevronLeft class="w-4 h-4" />
                    </Button>
                </Link>
                <div>
                    <div class="flex items-center bg-gray-100 rounded-xl px-4">
                        <span
                            class="text-lg font-semibold mx-2 bg-indigo-100 p-2 rounded"
                            v-if="conversation.is_group && conversation.name"
                            >{{ conversation.name?.toUpperCase() }}</span
                        >
                        <div v-for="user in users" :key="user.id" class="">
                            <div class="" v-if="user.id !== auth.user.id">
                                <div
                                    class="text-lg font-semibold mr-2 p-2 rounded"
                                    v-if="!conversation.is_group"
                                >
                                    <p>{{ user.name }}</p>

                                    <small
                                        class="font-normal text-xs"
                                        v-if="
                                            !onlineUsers.find(
                                                (u) => u.id == user.id
                                            )
                                        "
                                        >Last seen
                                        {{
                                            user.last_active_at
                                                ? moment(
                                                      user.last_active_at
                                                  ).fromNow()
                                                : "a long time ago"
                                        }}</small
                                    >
                                    <p class="font-normal text-xs" v-else>
                                        <span
                                            :class="
                                                onlineUsers.find(
                                                    (u) => u.id == user.id
                                                )
                                                    ? 'bg-green-500'
                                                    : 'bg-red-400'
                                            "
                                            class="inline-block h-2 w-2 rounded-full"
                                        ></span>
                                        Active now
                                    </p>
                                </div>
                                <div
                                    class="border h-10 text-md font-semibold mr-2 p-2 rounded"
                                    v-else
                                >
                                    {{ user.name.split(" ")[0] }}
                                    <span
                                        :class="
                                            onlineUsers.find(
                                                (u) => u.id == user.id
                                            )
                                                ? 'bg-green-500'
                                                : 'bg-red-400'
                                        "
                                        class="inline-block h-2 w-2 rounded-full"
                                    ></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mx-14 md:mx-4 mt-1 flex items-center gap-2">
                <Dialog>
                    <DialogTrigger as-child>
                        <Button variant="outline">
                            <Plus class="w-4 h-4" />Add</Button
                        >
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Add new member</DialogTitle>
                            <DialogDescription>
                                You can add someone to this group by email. New
                                member also got to see chat history.
                            </DialogDescription>
                        </DialogHeader>
                        <div>
                            <form
                                @submit.prevent="inviteToGroup"
                                class="w-full flex items-center space-x-2"
                            >
                                <div class="grid flex-1 gap-2">
                                    <Label for="email" class="sr-only">
                                        Email
                                    </Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        v-model="addedEmail"
                                    />
                                </div>
                                <Button type="submit" size="lg" class="px-3">
                                    <span class="sr-only">Copy</span>
                                    <ChevronRight class="w-4 h-4" />
                                </Button>
                            </form>
                        </div>
                    </DialogContent>
                </Dialog>
                <Button variant="outline" class="" @click="leaveGroup">
                    <LogOut class="w-4 h-4" />Leave
                </Button>
            </div>
        </div>

        <div
            class="flex flex-col h-full overflow-x-auto mb-4 [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-gray-400"
            ref="messageContainer"
        >
            <div class="flex flex-col h-full" v-if="messages.length">
                <div
                    class="grid grid-cols-12 gap-y-2"
                    v-for="message in messages"
                    :key="message.id"
                >
                    <div
                        class="col-start-6 col-end-13 p-3 rounded-lg"
                        v-if="auth.user.id === message.user_id"
                    >
                        <div
                            class="flex items-center justify-start flex-row-reverse"
                        >
                            <div class="relative">
                                <img
                                    v-if="message.user.image"
                                    :src="message.user.image"
                                    alt="Avatar"
                                    class="h-8 w-8 rounded-full border-3 border-indigo-200"
                                />
                                <div
                                    v-else
                                    class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full"
                                >
                                    {{ message.user.name[0] }}
                                </div>
                                <span
                                    :class="
                                        onlineUsers.find(
                                            (u) => u.id == message.user.id
                                        )
                                            ? 'bg-green-500'
                                            : 'bg-red-400'
                                    "
                                    class="inline-block h-3 w-3 rounded-full ml-2 absolute top-5 border-2 border-white left-3"
                                ></span>
                            </div>
                            <div class="mr-3 group flex items-center gap-2">
                                <div>
                                    <Reply
                                        class="w-4 h-4 cursor-pointer hidden group-hover:block"
                                        @click="
                                            replyMessage(
                                                message.id,
                                                message.message
                                            )
                                        "
                                    />
                                </div>
                                <div>
                                    <Trash
                                        class="w-4 h-4 cursor-pointer hidden group-hover:block"
                                        @click="deleteMessage(message.id)"
                                    />
                                </div>

                                <div
                                    :id="'message-' + message.id"
                                    class="relative text-sm bg-indigo-100 py-2 px-4 shadow rounded-xl flex gap-2 items-center justify-between"
                                >
                                    <div>
                                        <a :href="'#message-' + message.chat_message_id" v-if="message.reply" class="flex gap-3 items-center font-semibold border-b border-indigo-200 mb-3 p-1">
                                        <small class="">
                                            {{ message.reply?.message ?? 'File Messsage'}}
                                        </small>
                                        <Reply class="w-4 h-4" />
                                        </a>
                                        <div v-if="message.upload" class="mt-3">
                                            <div v-if="message.upload.type === 'image'">
                                                <img :src="'/'+message.upload.file_name" alt="" width="300px" height="300px">
                                            </div>
                                            <div v-else-if="message.upload.type === 'video'">
                                                <video src="'/'+message.upload.file_name"  width="300px" height="300px"></video>
                                            </div>
                                            <div v-else-if="message.upload.type === 'audio'">
                                                <audio src="'/'+message.upload.file_name"  width="300px" height="300px"></audio>
                                            </div>
                                            <div v-else>
                                                <a :href="'/'+message.upload.file_name" target="_blank" class="flex justify-center items-center gap-2 underline underline-offset-2">
                                                    <Download
                                                        class="w-4 h-4 cursor-pointer"
                                                    />
                                                    {{ message.upload.file_original_name }}
                                                </a>
                                            </div>
                                        </div>
                                        <p>
                                            {{ message.message }}
                                        </p>
                                        <p class="text-end">
                                            <small>{{
                                                moment(
                                                    message.created_at
                                                ).format("hh:mm a")
                                            }}</small>
                                            <small class="px-2">
                                                <CheckCheck
                                                    class="w-3 h-3 inline-block"
                                                    :class="[
                                                        message.seen_by
                                                            ? 'text-green-500'
                                                            : '',
                                                    ]"
                                                />
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-start-1 col-end-8 p-3 rounded-lg" v-else>
                        <div class="flex flex-row items-center">
                            <div class="relative">
                                <img
                                    v-if="message.user.image"
                                    :src="message.user.image"
                                    alt="Avatar"
                                    class="h-8 w-8 rounded-full border-3 border-indigo-200"
                                />
                                <div
                                    v-else
                                    class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full"
                                >
                                    {{ message.user.name[0] }}
                                </div>
                                <span
                                    :class="
                                        onlineUsers.find(
                                            (u) => u.id == message.user.id
                                        )
                                            ? 'bg-green-500'
                                            : 'bg-red-400'
                                    "
                                    class="inline-block h-3 w-3 rounded-full ml-2 absolute top-5 border-2 border-white left-3"
                                ></span>
                            </div>
                            <div class="flex justify-between items-center group gap-2">
                                <div :id="'message-' + message.id"
                                    class="relative ml-3 text-sm bg-white py-2 px-4 shadow rounded-xl"
                                >
                                <a :href="'#message-' + message.chat_message_id" v-if="message.reply" class="flex gap-3 items-center font-semibold border-b border-indigo-200 mb-3 p-1">
                                    <Reply class="w-4 h-4" />
                                        <small class="">
                                            {{ message.reply?.message ?? 'File Messsage' }}
                                        </small>
                                        </a>

                                        <div v-if="message.upload" class="mt-3">

                                            <div v-if="message.upload.type === 'image'">
                                                <img :src="'/'+message.upload.file_name" alt="" width="300px" height="300px">
                                            </div>
                                            <div v-else-if="message.upload.type === 'video'">
                                                <video width="320" height="240" controls>
                                                    <source :src="'/'+message.upload.file_name">
                                                </video>                                            
                                            </div>
                                            <div v-else-if="message.upload.type === 'audio'">
                                                <audio controls>
                                                    <source :src="'/'+message.upload.file_name" >
                                                </audio>
                                            </div>
                                            <div v-else>
                                                <a :href="'/'+message.upload.file_name" target="_blank" class="flex justify-center items-center gap-2 underline underline-offset-2">
                                                    <Download
                                                        class="w-4 h-4 cursor-pointer"
                                                    />
                                                    {{ message.upload.file_original_name }}
                                                </a>
                                            </div>
                                        </div>
                                    <p>
                                        {{ message.message }}
                                    </p>
                                    <p class="text-end">
                                        <small>{{
                                            moment(message.created_at).format(
                                                "hh:mm a"
                                            )
                                        }}</small>
                                    </p>
                                </div>
                                <div class="mr-3 flex items-center gap-2">
                                    <div>
                                        <Reply
                                            class="w-4 h-4 cursor-pointer hidden group-hover:block"
                                            @click="
                                                replyMessage(
                                                    message.id,
                                                    message.message
                                                )
                                            "
                                        />
                                    </div>
                                    <div>
                                        <!-- <Trash
                                            class="w-4 h-4 cursor-pointer hidden group-hover:block"
                                            @click="deleteMessage(message.id)"
                                        /> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="flex flex-col h-full justify-center items-center"
                v-else
            >
                <p class="text-2xl font-bold">No messages</p>
            </div>
        </div>
        <small v-if="isUserTyping"  class="text-gray-600 mb-2 ml-3">
            {{ typingUserName }} is typing...
        </small>
        <div
            class="block ml-3 text-sm bg-indigo-200 py-2 px-4 mb-1 shadow rounded-xl"
            v-if="replyMessageText"
        >
            <div class="flex justify-between items-center font-bold">
                <div class="flex items-center">
                    <Reply class="w-4 h-4 mr-2" />
                    <p>Replying to</p>
                </div>
                <CircleX
                    class="w-4 h-4 cursor-pointer"
                    @click="deleteReplyMessage"
                />
            </div>
            <small>
                {{ replyMessageText }}
            </small>
        </div>
        <div
            class="flex flex-row items-center h-16 rounded-xl bg-white w-full px-4"
        >
        
            <form
                @submit.prevent="submit"
                class="flex justify-center items-center flex-grow"
            >
            <div class="">
                <label
                    class="flex items-center justify-center text-gray-400 hover:text-gray-600 cursor-pointer"
                >        
                <input type="file" class="hidden" @input="form.file = $event.target.files[0]" @change="submit();">
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                        ></path>
                    </svg>
                </label>
            </div>            
                <div class="ml-4 flex-grow">
                    <div class="relative w-full py-2">
                        <input
                            type="text"
                            class="flex w-full border rounded-xl focus:outline-none focus:border-indigo-300 pl-4 h-10"
                            v-model="form.message"
                            ref="textInput"
                            @keydown="sendTypingEvent"
                            @click="updateCursorPosition"
                            @keyup="updateCursorPosition"
                        />

                        <button
                            type="button"
                            @click="open = !open"
                            class="absolute flex items-center justify-center h-full w-12 right-0 top-0 text-gray-400 hover:text-gray-600"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                        </button>
                    </div>

                </div>
                <div class="ml-4 relative">
                    <VueChatEmojiComponent
                        :open="open"
                        v-if="open"
                        width="280px"
                        @handle="selectedEmoji"
                        class="grid place-items-end"
                    />
                    <button
                        class="flex items-center justify-center bg-indigo-500 hover:bg-indigo-600 rounded-xl text-white px-4 py-2 flex-shrink-0"
                    >
                        <!-- <span>Send</span> -->
                        <span class="">
                            <svg
                                class="w-4 h-4 transform rotate-45 -mt-px"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                ></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </Dashboard>
</template>
