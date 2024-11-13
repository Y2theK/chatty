<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chat from "@/Pages/Chat/Chat.vue";
import Sidebar from "@/Components/Sidebar.vue";
import { onBeforeUnmount, onMounted, ref } from "vue";

const props = defineProps({
    conversations: {
        required: true,
    },
  
});

const allOnlineUsers = ref([]);

onMounted(() => {

    window.Echo.join(`online`)
        .here((users) => {
            allOnlineUsers.value = users;
        })
        .joining((user) => {
            allOnlineUsers.value.push(user);
        })
        .leaving((user) => {
            allOnlineUsers.value = allOnlineUsers.value.filter(
                (u) => u.id !== user.id
            );
        });        

});

onBeforeUnmount(() => {
    window.Echo.leave(`online`, (user) => {
        allOnlineUsers.value = onlineUser.value.filter((u) => u.id !== user.id);
    });
});

</script>

<template>
    <AuthenticatedLayout>
        <div class="flex h-screen antialiased text-slate-800">
            <div class="flex flex-row h-full w-full overflow-x-hidden justify-center">
                <Sidebar :conversations="conversations" :allOnlineUsers="allOnlineUsers" :class="[route().current('conversations.index') ? '' : 'hidden sm:flex']"></Sidebar>
                <div class=" flex-col flex-auto h-full p-4 sm:p-6" :class="[route().current('conversations.index') ? 'hidden sm:flex' : '']">
                    <div
                        class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-2"
                    >
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
