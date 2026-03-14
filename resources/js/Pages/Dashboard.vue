<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chat from "@/Pages/Chat/Chat.vue";
import Sidebar from "@/Components/Sidebar.vue";
import IncomingCall from "@/Components/IncomingCall.vue";
import VideoCall from "@/Components/VideoCall.vue";
import { Toaster } from "@/components/ui/toast";
import { onBeforeUnmount, onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    conversations: {
        required: true,
    },
});

const page = usePage();
const allOnlineUsers = ref([]);

const incomingCall = ref(null);
const activeCall = ref(null);
const activeRoom = ref(null);

const onCallAccepted = ({ token, livekitUrl }) => {
    const conversationId = incomingCall.value?.conversationId;
    const roomName = incomingCall.value?.roomName;
    incomingCall.value = null;
    activeCall.value = { token, livekitUrl, roomName, conversationId };
    activeRoom.value = { roomName, conversationId };
};

const onCallDeclined = () => {
    incomingCall.value = null;
};

const onCallLeft = ({ conversationId, roomName }) => {
    activeCall.value = null;
    activeRoom.value = { roomName, conversationId };
    window.dispatchEvent(new CustomEvent('call:left', { detail: { roomName, conversationId } }));
};

const onCallEnded = () => {
    activeCall.value = null;
    activeRoom.value = null;
    window.dispatchEvent(new CustomEvent('call:ended'));
};

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

    window.Echo.private(`user.${page.props.auth.user.id}`)
        .listen('CallInitiated', (data) => {
            incomingCall.value = {
                callerName: data.initiated_by.name,
                callerImage: data.initiated_by.image,
                roomName: data.room_name,
                conversationId: data.conversation_id,
            };
        });

    window.addEventListener('call:started', (e) => {
        activeCall.value = {
            token: e.detail.token,
            livekitUrl: e.detail.livekitUrl,
            roomName: e.detail.roomName,
            conversationId: e.detail.conversationId,
        };
        activeRoom.value = {
            roomName: e.detail.roomName,
            conversationId: e.detail.conversationId,
        };
    });

    window.addEventListener('call:ended', () => {
        activeCall.value = null;
        incomingCall.value = null;
        activeRoom.value = null;
    });
});

onBeforeUnmount(() => {
    window.Echo.leave(`online`);
    window.Echo.leave(`user.${page.props.auth.user.id}`);
});

</script>

<template>
    <IncomingCall
        v-if="incomingCall"
        :caller-name="incomingCall.callerName"
        :caller-image="incomingCall.callerImage"
        :conversation-id="incomingCall.conversationId"
        :room-name="incomingCall.roomName"
        @accepted="onCallAccepted"
        @declined="onCallDeclined"
    />

    <VideoCall
        v-if="activeCall"
        :token="activeCall.token"
        :livekit-url="activeCall.livekitUrl"
        :room-name="activeCall.roomName"
        :conversation-id="activeCall.conversationId"
        :current-user="page.props.auth.user"
        @call-left="onCallLeft"
        @call-ended="onCallEnded"
    />

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
        <Toaster />
    </AuthenticatedLayout>
</template>
