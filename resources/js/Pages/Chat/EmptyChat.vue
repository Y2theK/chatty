<script setup>
import Dashboard from "@/Pages/Dashboard.vue";

const props = defineProps({
    conversations: {
        required: true,
    },
});

</script>

<template>
    <Dashboard :conversations="conversations">
        <div
            class="flex flex-col h-full overflow-x-auto mb-4 justify-center items-center"
        >
            <p class="text-3xl font-bold text-center">
                Select a chat or start a new conversation
            </p>
        </div>       
    </Dashboard>
</template>
