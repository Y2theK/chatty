<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Room, RoomEvent, Track, createLocalVideoTrack, createLocalAudioTrack } from 'livekit-client';
import axios from 'axios';
import { Mic, MicOff, Video, VideoOff, PhoneOff, LogOut } from 'lucide-vue-next';

const props = defineProps({
    token: { type: String, required: true },
    livekitUrl: { type: String, required: true },
    roomName: { type: String, required: true },
    conversationId: { type: Number, required: true },
    currentUser: { type: Object, required: true },
});

const emit = defineEmits(['call-ended', 'call-left']);

const room = ref(null);
const participants = ref(new Map()); // identity -> { participant, videoEl, audioEl }
const localVideoEl = ref(null);
const isMicMuted = ref(false);
const isCamOff = ref(false);
const isConnecting = ref(true);
const error = ref(null);

const participantList = computed(() => [...participants.value.values()]);

const gridClass = computed(() => {
    const count = participantList.value.length + 1; // +1 for local
    if (count <= 2) return 'grid-cols-1 md:grid-cols-2';
    if (count <= 4) return 'grid-cols-2';
    return 'grid-cols-2 md:grid-cols-3';
});

const attachTrack = (el, track) => {
    if (!el || !track) return;
    const mediaStream = new MediaStream([track.mediaStreamTrack]);
    el.srcObject = mediaStream;
};

const detachTrack = (el) => {
    if (el) el.srcObject = null;
};

const updateParticipantTrack = (participant) => {
    const entry = participants.value.get(participant.identity);
    if (!entry) return;

    const videoPublication = participant.getTrackPublication(Track.Source.Camera);
    const audioPublication = participant.getTrackPublication(Track.Source.Microphone);

    if (videoPublication?.track && entry.videoEl) {
        attachTrack(entry.videoEl, videoPublication.track);
    }
    if (audioPublication?.track && entry.audioEl) {
        attachTrack(entry.audioEl, audioPublication.track);
    }
};

onMounted(async () => {
    room.value = new Room();

    room.value
        .on(RoomEvent.ParticipantConnected, (participant) => {
            participants.value.set(participant.identity, {
                participant,
                name: participant.name ?? participant.identity,
                videoEl: null,
                audioEl: null,
            });
        })
        .on(RoomEvent.ParticipantDisconnected, (participant) => {
            participants.value.delete(participant.identity);
        })
        .on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
            updateParticipantTrack(participant);
        })
        .on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
            const entry = participants.value.get(participant.identity);
            if (!entry) return;
            if (track.kind === Track.Kind.Video && entry.videoEl) {
                detachTrack(entry.videoEl);
            }
            if (track.kind === Track.Kind.Audio && entry.audioEl) {
                detachTrack(entry.audioEl);
            }
        })
        .on(RoomEvent.Disconnected, () => {
            emit('call-ended');
        });

    try {
        await room.value.connect(props.livekitUrl, props.token);
    } catch (e) {
        console.error('[LiveKit] Connection failed:', e);
        error.value = `Connection failed: ${e?.message ?? e}`;
        isConnecting.value = false;
        return;
    }

    // Add already connected remote participants (e.g. when joining mid-call)
    room.value.remoteParticipants.forEach((participant) => {
        participants.value.set(participant.identity, {
            participant,
            name: participant.name ?? participant.identity,
            videoEl: null,
            audioEl: null,
        });
    });

    try {
        const [videoTrack, audioTrack] = await Promise.all([
            createLocalVideoTrack(),
            createLocalAudioTrack(),
        ]);

        await Promise.all([
            room.value.localParticipant.publishTrack(videoTrack),
            room.value.localParticipant.publishTrack(audioTrack),
        ]);

        if (localVideoEl.value) {
            attachTrack(localVideoEl.value, videoTrack);
        }
    } catch (e) {
        console.error('[LiveKit] Camera/mic failed:', e);
        error.value = `Camera/microphone error: ${e?.message ?? e}`;
        isConnecting.value = false;
        return;
    }

    isConnecting.value = false;
});

onBeforeUnmount(() => {
    room.value?.disconnect();
});

const setVideoRef = (identity, el) => {
    const entry = participants.value.get(identity);
    if (!entry) return;
    entry.videoEl = el;
    updateParticipantTrack(entry.participant);
};

const setAudioRef = (identity, el) => {
    const entry = participants.value.get(identity);
    if (!entry) return;
    entry.audioEl = el;
    updateParticipantTrack(entry.participant);
};

const toggleMic = () => {
    isMicMuted.value = !isMicMuted.value;
    room.value?.localParticipant.setMicrophoneEnabled(!isMicMuted.value);
};

const toggleCam = () => {
    isCamOff.value = !isCamOff.value;
    room.value?.localParticipant.setCameraEnabled(!isCamOff.value);
};

// Leave silently — room stays alive, you can rejoin
const leaveCall = () => {
    room.value?.disconnect();
    emit('call-left', { conversationId: props.conversationId, roomName: props.roomName });
};

// End for everyone — broadcasts CallEnded to all members
const endCall = async () => {
    room.value?.disconnect();
    await axios.post(`/conversations/${props.conversationId}/call/end`);
    emit('call-ended');
};
</script>

<template>
    <div class="fixed inset-0 z-50 bg-gray-900 flex flex-col">

        <!-- Connecting state -->
        <div v-if="isConnecting" class="flex-1 flex items-center justify-center">
            <p class="text-white text-lg animate-pulse">Connecting to call...</p>
        </div>

        <!-- Error state -->
        <div v-else-if="error" class="flex-1 flex flex-col items-center justify-center gap-4">
            <p class="text-red-400 text-center px-6">{{ error }}</p>
            <button @click="leaveCall" class="bg-red-500 text-white px-6 py-2 rounded-lg">
                Leave
            </button>
        </div>

        <!-- Call UI -->
        <template v-else>
            <!-- Video grid -->
            <div class="flex-1 grid gap-2 p-4 overflow-hidden" :class="gridClass">

                <!-- Local participant -->
                <div class="relative bg-gray-800 rounded-xl overflow-hidden flex items-center justify-center">
                    <video
                        ref="localVideoEl"
                        autoplay
                        muted
                        playsinline
                        class="w-full h-full object-cover"
                        :class="{ 'opacity-0': isCamOff }"
                    />
                    <div v-if="isCamOff" class="absolute inset-0 flex items-center justify-center">
                        <div class="w-16 h-16 rounded-full bg-indigo-500 flex items-center justify-center text-white text-2xl font-bold">
                            {{ currentUser.name[0] }}
                        </div>
                    </div>
                    <span class="absolute bottom-2 left-2 text-xs text-white bg-black/50 px-2 py-1 rounded">
                        {{ currentUser.name }} (you)
                    </span>
                </div>

                <!-- Remote participants -->
                <div
                    v-for="entry in participantList"
                    :key="entry.participant.identity"
                    class="relative bg-gray-800 rounded-xl overflow-hidden flex items-center justify-center"
                >
                    <video
                        :ref="(el) => setVideoRef(entry.participant.identity, el)"
                        autoplay
                        playsinline
                        class="w-full h-full object-cover"
                    />
                    <audio
                        :ref="(el) => setAudioRef(entry.participant.identity, el)"
                        autoplay
                    />
                    <span class="absolute bottom-2 left-2 text-xs text-white bg-black/50 px-2 py-1 rounded">
                        {{ entry.name }}
                    </span>
                </div>

                <!-- Empty state when alone -->
                <div
                    v-if="participantList.length === 0"
                    class="bg-gray-800 rounded-xl flex items-center justify-center"
                >
                    <p class="text-gray-400 text-sm">Waiting for others to join...</p>
                </div>

            </div>

            <!-- Controls -->
            <div class="flex items-center justify-center gap-4 py-5 bg-gray-900">
                <button
                    @click="toggleMic"
                    class="rounded-full p-4 transition-colors"
                    :class="isMicMuted ? 'bg-gray-600 hover:bg-gray-500' : 'bg-gray-700 hover:bg-gray-600'"
                    :title="isMicMuted ? 'Unmute' : 'Mute'"
                >
                    <MicOff v-if="isMicMuted" class="w-5 h-5 text-white" />
                    <Mic v-else class="w-5 h-5 text-white" />
                </button>

                <button
                    @click="toggleCam"
                    class="rounded-full p-4 transition-colors"
                    :class="isCamOff ? 'bg-gray-600 hover:bg-gray-500' : 'bg-gray-700 hover:bg-gray-600'"
                    :title="isCamOff ? 'Turn camera on' : 'Turn camera off'"
                >
                    <VideoOff v-if="isCamOff" class="w-5 h-5 text-white" />
                    <Video v-else class="w-5 h-5 text-white" />
                </button>

                <button
                    @click="leaveCall"
                    class="bg-gray-700 hover:bg-gray-600 rounded-full p-4 transition-colors"
                    title="Leave call (others stay)"
                >
                    <LogOut class="w-5 h-5 text-white" />
                </button>

                <button
                    @click="endCall"
                    class="bg-red-500 hover:bg-red-600 rounded-full p-4 transition-colors"
                    title="End call for everyone"
                >
                    <PhoneOff class="w-5 h-5 text-white" />
                </button>
            </div>
        </template>

    </div>
</template>
