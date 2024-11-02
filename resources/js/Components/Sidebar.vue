<script setup>
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import { onBeforeMount, onMounted, ref } from "vue";

const props = defineProps({
    conversations: {
        required: true,
    },
});

const groupColors = [
    'bg-red-200',
    'bg-green-200',
    'bg-blue-200',
    'bg-yellow-200',
    'bg-gray-200',
    'bg-scarlet-200'
]

const allOnlineUsers = ref([]);

onMounted(() => {
    window.Echo.join(`online`)
            .here(users => {
                allOnlineUsers.value = users;
            })
            .joining(user => {
                allOnlineUsers.value.push(user);
            })
            .leaving(user => {
                allOnlineUsers.value = allOnlineUsers.value.filter((u) => u.id !== user.id);
            })
})

onBeforeMount(() => {        
        window.Echo.leave(`online`,user => {
            allOnlineUsers.value = onlineUser.value.filter((u) => u.id !== user.id);
        })
    })
</script>

<template>
    <div class="flex flex-col py-8 pl-6 pr-2 w-64 bg-white flex-shrink-0">
        <Link
            :href="route('dashboard')"
            class="flex flex-row items-center justify-center h-12 w-full"
        >
            <div
                class="flex items-center justify-center rounded-2xl text-indigo-700 bg-indigo-100 h-10 w-10"
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
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                    ></path>
                </svg>
            </div>
            <div class="ml-2 font-bold text-2xl">ChattyB</div>
        </Link>
        <div
            class="flex flex-col items-center bg-indigo-100 border border-gray-200 mt-4 w-full py-6 px-4 rounded-lg"
        >
            <div class="h-20 w-20 rounded-full border overflow-hidden">
                <img
                    src="https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt="Avatar"
                    class="h-full w-full"
                />
            </div>
            <div class="text-sm font-semibold mt-2">
                {{ $page.props.auth.user.name }}
            </div>
            <div class="text-xs text-gray-500">
                {{ $page.props.auth.user.email }}
            </div>
            <div class="flex flex-row items-center mt-3">
                <div
                    class="flex flex-col justify-center h-4 w-8 bg-indigo-500 rounded-full"
                >
                    <div
                        class="h-3 w-3 bg-white rounded-full self-end mr-1"
                    ></div>
                </div>
                <div class="leading-none ml-1 text-xs">Active</div>
            </div>
        </div>
        <div class="flex flex-col mt-8">
            <div class="flex flex-row items-center justify-between text-xs">
                <span class="font-bold">Online</span>
                <!-- online count have to exclude current user count -->
                <span
                    class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
                    >{{ allOnlineUsers.length - 1 }}</span
                >
            </div>
            <div
                class="flex flex-col space-y-1 mt-4 -mx-2  overflow-y-auto [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-gray-400"
            >
                <Link
                    :href="route('conversations.show', conversation.id)"
                    v-for="conversation in conversations"
                    :class="[route().current('conversations.show', conversation.id) ? 'bg-gray-100' : '']"
                    class="rounded-xl"
                    :key="conversation.id"
                >
                    <div
                        v-if="conversation.users.length === 1"
                        class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2"
                    >
                        <div
                            class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full"
                        >
                            {{ conversation.users[0].name[0] }}
                        </div>
                        <div class="ml-2 text-sm font-semibold">
                            {{ conversation.users[0].name }}
                            <span :class="allOnlineUsers.find((u) => u.id == conversation.users[0].id) ? 'bg-green-500' : 'bg-red-400'" class="inline-block h-2 w-2 rounded-full"></span>

                        </div>
                    </div>
                    <div
                        v-else
                        class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2"
                    >                      
                        <div class=" flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full">
                                {{ $page.props.auth.user.name[0].toUpperCase() }} 
                        </div> 
                       <div v-for="(user,index) in conversation.users.slice(0,4)" :key="user.id">
                            <div :class="groupColors[index]"
                                class=" flex items-center justify-center -ml-3 h-8 w-8 rounded-full"
                            >
                              {{ user.name[0] }}
                            </div>
                            
                       </div>
                        <div class="ml-2 text-sm font-semibold">
                            {{ conversation.name }}
                            
                            <span :class="allOnlineUsers.find((u) => conversation.users.find((c) => c.id == u.id)) ? 'bg-green-500' : 'bg-red-400'" class="inline-block h-2 w-2 rounded-full"></span>
                        </div>
                    </div>
                </Link>
            </div>
                <!-- <div
                    class="flex flex-row items-center justify-between text-xs mt-6"
                >
                    <span class="font-bold">Offline</span>
                    <span
                        class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
                        >7</span
                    >
                </div>
                <div class="flex flex-col space-y-1 mt-4 -mx-2">
                    <button
                        class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2"
                    >
                        <div
                            class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full"
                        >
                            H
                        </div>
                        <div class="ml-2 text-sm font-semibold">Henry Boyd</div>
                    </button>
                </div> -->
        </div>
    </div>
</template>
