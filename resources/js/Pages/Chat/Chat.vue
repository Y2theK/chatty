<script setup>
import Dashboard from "@/Pages/Dashboard.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { Button } from "@/components/ui/button";
import { Link } from "@inertiajs/vue3";
import { ChevronLeft,ChevronRight, LogOut, Plus, Trash  } from 'lucide-vue-next';

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
    message: "",
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
    const before = form.message.slice(0, cursorPosition.value);
    const after =  form.message.slice(cursorPosition.value);
    form.message = before + emoji + after;
    console.log(args);
    
    // Update cursor position after emoji insertion
    await nextTick();
    cursorPosition.value += emoji.length;
    input.setSelectionRange(cursorPosition.value, cursorPosition.value);
    input.focus();
};

const submit = () => {
    if (form.message) {
        form.post(route("messages.store", props.conversation.id), {
            message: form.message,
            onFinish: () => (form.message = ""),
        });
    }
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

const deleteMessage = async(id) => {
    try {
        document.getElementById(`message-${id}`).innerHTML = 'Message deleted';
        const response = await axios.delete(
            `/conversations/${props.conversation.id}/messages/${id}`,
        );

    } catch (error) {
        console.error("Failed to send message:", error);
    }
}

const leaveGroup = async() => {
    try {
        const response = await axios.delete(
            `/conversations/${props.conversation.id}/leave`
        );
        window.location.href = response.data.redirect;
    } catch (error) {
        console.error("Failed to send message:", error);
    }
}

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
        .listen("ChatMessageSent", (response) => {
            messages.value.push(response.chatMessage);
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

    window.Echo.join(`conversation.${props.conversation.id}`)
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
        <div class="justify-between border-b py-4 flex flex-col md:flex-row ">
            <div class="flex items-center">
                <Link :href="route('dashboard')">
                    <Button variant="outline" size="icon">
                        <ChevronLeft class="w-4 h-4" />
                    </Button>
                </Link>
                <div>
                    <div
                        class="flex items-center bg-gray-100 rounded-xl px-4"
                        v-if="!conversation.is_group"
                    >
                        <div class="text-lg font-semibold mr-2 p-2 rounded">
                           <div>
                            {{ users[0].name }}
                            <span
                                :class="
                                    onlineUsers.find((u) => u.id == users[0].id)
                                        ? 'bg-green-500'
                                        : 'bg-red-400'
                                "
                                class="inline-block h-2 w-2 rounded-full"
                            ></span>
                           </div>
                           <small v-if="! onlineUsers.find((u) => u.id == users[0].id)" class=" font-normal text-xs">Last seen {{ users[0].last_active_at ? moment(users[0].last_active_at).fromNow() : 'a long time ago'  }}</small>
                        </div>
                    </div>
                    <div
                        class="flex items-center bg-gray-100 rounded-xl px-4"
                        v-else
                    >
                        <span
                            class="text-lg font-semibold mx-2 bg-indigo-100 p-2 rounded"
                            v-show="conversation.name"
                            >{{ conversation.name?.toUpperCase() }}</span
                        >
                        <div
                            v-for="user in onlineUsers"
                            :key="user.id"
                            class=""
                        >
                            <div
                                class="text-md font-semibold mr-2 border p-2 rounded h-10"
                                v-if="user.id !== auth.user.id"
                            >
                                {{ user.name.split(" ")[0] }}
                                <span
                                    :class="
                                        onlineUsers.find((u) => u.id == user.id)
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
            <div class="mx-14 md:mx-4 mt-1 flex items-center gap-2" >
                <Dialog>
                    <DialogTrigger as-child>
                        <Button variant="outline">  <Plus class="w-4 h-4" />Add</Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Add new member</DialogTitle>
                            <DialogDescription>
                                You can add someone to this group by email. New member also got to see chat history.
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
                            <img  v-if="message.user.image"
                                :src="message.user.image"
                                alt="Avatar"
                                class="h-8 w-8 rounded-full border-3 border-indigo-200"
                            />
                            <div v-else
                                class="flex items-center justify-center h-10 w-10 rounded-full bg-gray-200 flex-shrink-0"
                            >
                                {{ message.user.name[0] }}
                            </div>
                            
                            <div :id="'message-'+message.id"
                                class="relative mr-3 text-sm bg-indigo-100 py-2 px-4 shadow rounded-xl flex gap-2 items-center justify-between group"
                            >
                            <div>{{ message.message }}</div>
                            <Trash class="w-4 h-4 cursor-pointer hidden group-hover:block"  @click="deleteMessage(message.id)"/>

                        </div>


                        </div>
                    </div>
                    <div class="col-start-1 col-end-8 p-3 rounded-lg" v-else>
                        <div class="flex flex-row items-center">
                            <img  v-if="message.user.image"
                                :src="message.user.image"
                                alt="Avatar"
                                class="h-8 w-8 rounded-full border-3 border-indigo-200"
                            />
                            <div v-else
                                class="flex items-center justify-center h-10 w-10 rounded-full bg-gray-200 flex-shrink-0"
                            >
                                {{ message.user.name[0] }}
                            </div>
                            <div
                                class="relative ml-3 text-sm bg-white py-2 px-4 shadow rounded-xl"
                            >
                                <div>{{ message.message }}</div>
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
        <div
            class="flex flex-row items-center h-16 rounded-xl bg-white w-full px-4"
        >
            <div>
                <button
                    class="flex items-center justify-center text-gray-400 hover:text-gray-600"
                >
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
                </button>
            </div>

            <form
                @submit.prevent="submit"
                class="flex justify-center items-center flex-grow"
            >
          
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
                     
                        <button type="button"
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
                   
                    <small v-if="isUserTyping" class="text-gray-600 mt-5">
                        {{ typingUserName }} is typing...
                    </small>
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
