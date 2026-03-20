<script setup>
defineProps({
    isRead: {
        type: Boolean,
        default: false,
    },
    isSent: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <span 
        class="inline-flex items-center"
        :class="isRead ? 'text-white' : 'text-white/70'"
    >
        <!-- Single tick - sent -->
        <svg v-if="isSent && !isRead" class="w-4 h-4" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 5.5L2.5 7L5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        
        <!-- Double tick - delivered -->
        <svg v-else-if="isSent && isRead" class="w-5 h-5" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 6L3 8L7 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7 6L9 8L13 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        
        <!-- Single tick for non-sent messages -->
        <svg v-else class="w-4 h-4" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 5.5L2.5 7L5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
</template>
