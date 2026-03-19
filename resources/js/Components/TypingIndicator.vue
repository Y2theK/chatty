<script setup>
defineProps({
    userName: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="flex items-center gap-2 px-4 py-2 text-text-muted text-xs animate-slide-up">
        <span class="font-medium text-text-secondary">{{ userName }}</span>
        <span>is typing</span>
        <div class="flex items-center gap-0.5">
            <span
                class="w-1.5 h-1.5 rounded-full bg-text-muted animate-bounce-dot"
                style="animation-delay: 0ms"
            ></span>
            <span
                class="w-1.5 h-1.5 rounded-full bg-text-muted animate-bounce-dot"
                style="animation-delay: 200ms"
            ></span>
            <span
                class="w-1.5 h-1.5 rounded-full bg-text-muted animate-bounce-dot"
                style="animation-delay: 400ms"
            ></span>
        </div>
    </div>
</template>
