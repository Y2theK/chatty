<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { Phone, PhoneOff } from 'lucide-vue-next';

const props = defineProps({
    callerName: { type: String, required: true },
    callerImage: { type: String, default: null },
    conversationId: { type: Number, required: true },
    roomName: { type: String, required: true },
});

const emit = defineEmits(['accepted', 'declined']);

const loading = ref(false);

const accept = async () => {
    loading.value = true;
    try {
        const res = await axios.post(`/conversations/${props.conversationId}/call/join`, {
            room_name: props.roomName,
        });
        emit('accepted', {
            token: res.data.token,
            livekitUrl: res.data.livekit_url,
        });
    } finally {
        loading.value = false;
    }
};

const decline = () => {
    emit('declined');
};
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center gap-5 w-72">

            <!-- Avatar -->
            <div class="relative">
                <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-indigo-200">
                    <img
                        v-if="callerImage"
                        :src="callerImage"
                        :alt="callerName"
                        class="w-full h-full object-cover"
                    />
                    <div
                        v-else
                        class="w-full h-full bg-indigo-200 flex items-center justify-center text-2xl font-bold text-indigo-700"
                    >
                        {{ callerName[0] }}
                    </div>
                </div>
                <!-- Pulse ring -->
                <span class="absolute inset-0 rounded-full animate-ping bg-green-400 opacity-30"></span>
            </div>

            <!-- Info -->
            <div class="text-center">
                <p class="font-semibold text-lg text-gray-800">{{ callerName }}</p>
                <p class="text-sm text-gray-500">Incoming video call...</p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-6">
                <button
                    @click="decline"
                    class="flex flex-col items-center gap-1 group"
                >
                    <span class="bg-red-500 hover:bg-red-600 text-white rounded-full p-4 transition-colors">
                        <PhoneOff class="w-6 h-6" />
                    </span>
                    <small class="text-gray-500">Decline</small>
                </button>

                <button
                    @click="accept"
                    :disabled="loading"
                    class="flex flex-col items-center gap-1 group"
                >
                    <span class="bg-green-500 hover:bg-green-600 text-white rounded-full p-4 transition-colors">
                        <Phone class="w-6 h-6" />
                    </span>
                    <small class="text-gray-500">Accept</small>
                </button>
            </div>

        </div>
    </div>
</template>
