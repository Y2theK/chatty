<script setup>
import Dashboard from "@/Pages/Dashboard.vue";
import { MessageCircle } from "lucide-vue-next";

const props = defineProps({
    conversations: {
        required: true,
    },
});
</script>

<template>
    <Dashboard :conversations="conversations">
        <div class="flex flex-col h-full items-center justify-center text-center p-8">
            <div class="w-24 h-24 rounded-full bg-bg-surface-raised flex items-center justify-center mb-6 animate-fade-in">
                <MessageCircle class="w-12 h-12" style="color: oklch(0.55 0.15 250);" />
            </div>
            <h2 class="text-xl font-semibold mb-2" style="color: oklch(0.15 0.01 60);">
                Welcome to Chatty
            </h2>
            <p class="text-sm max-w-sm" style="color: oklch(0.45 0.01 60);">
                Select a conversation from the sidebar or start a new one to begin chatting with friends and family.
            </p>
        </div>
    </Dashboard>
</template>
