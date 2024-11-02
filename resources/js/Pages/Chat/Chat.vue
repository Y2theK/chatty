<script setup>
import Dashboard from "@/Pages/Dashboard.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";

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

const submit = () => {
    if (form.message) {
        form.post(route("conversations.store", props.conversation.id), {
            message: form.message,
            onFinish: () => (form.message = ""),
        });
    }
};

const messages = ref([...props.messages.data.reverse()]);
const messageContainer = ref(null);
const onlineUser = ref([]);
const isUserTyping = ref(false);
const typingUserName = ref("");
const isUserTypingTimer = ref(null);

const users = ref([...props.conversation.users]);

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
            .here(users => {
                onlineUser.value = users;
                console.log(users);
            })
            .joining(user => {
                onlineUser.value.push(user);
            })
            .leaving(user => {
                onlineUser.value =onlineUser.value.filter((u) => u.id !== user.id);
            })
});

    onBeforeUnmount(() => {
        
        window.Echo.leave(`conversation.${props.conversation.id}`,user => {
            
            onlineUser.value = onlineUser.value.filter((u) => u.id !== user.id);
        })
    })
</script>

<template>
    <Dashboard :conversations="conversations">
        <div class="flex items-center bg-gray-100  border-b py-4 rounded-xl px-4" >
            <div v-for="user in onlineUser" :key="user.id" class="mr-2 border p-2 rounded">
                <div class="text-lg font-semibold mr-2">{{ user.name }} 
                    <span :class="onlineUser.find((u) => u.id == user.id) ? 'bg-green-500' : 'bg-red-400'" class="inline-block h-2 w-2 rounded-full"></span>
                </div>
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
                            <div
                                class="flex items-center justify-center h-10 w-10 rounded-full bg-gray-200 flex-shrink-0"
                            >
                                {{ message.user.name[0] }}
                            </div>
                            <div
                                class="relative mr-3 text-sm bg-indigo-100 py-2 px-4 shadow rounded-xl"
                            >
                                <div>{{ message.message }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-start-1 col-end-8 p-3 rounded-lg" v-else>
                        <div class="flex flex-row items-center">
                            <div
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
                    <div class="relative w-full">
                        <input
                            type="text"
                            class="flex w-full border rounded-xl focus:outline-none focus:border-indigo-300 pl-4 h-10"
                            v-model="form.message"
                            required
                            @keydown="sendTypingEvent"
                            
                        />
                        <button
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
                <div class="ml-4">
                    <button
                        class="flex items-center justify-center bg-indigo-500 hover:bg-indigo-600 rounded-xl text-white px-4 py-2 flex-shrink-0"
                    >
                        <span>Send</span>
                        <span class="ml-2">
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
