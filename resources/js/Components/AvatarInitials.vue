<script setup>
import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    showOnline: {
        type: Boolean,
        default: false,
    },
    isOnline: {
        type: Boolean,
        default: false,
    },
});

const avatarColors = [
    'oklch(0.65 0.18 50)',
    'oklch(0.70 0.12 160)',
    'oklch(0.65 0.18 320)',
    'oklch(0.65 0.18 30)',
    'oklch(0.65 0.18 90)',
    'oklch(0.65 0.18 200)',
    'oklch(0.65 0.18 280)',
    'oklch(0.65 0.18 350)',
];

const getAvatarColor = (userId) => {
    const hash = String(userId).split('').reduce((acc, char) => {
        return acc + char.charCodeAt(0);
    }, 0);
    return avatarColors[hash % avatarColors.length];
};

const avatarColor = computed(() => {
    if (props.user && props.user.id) {
        return getAvatarColor(props.user.id);
    }
    return avatarColors[0];
});

const initials = computed(() => {
    if (!props.user || !props.user.name) return '?';
    const names = props.user.name.trim().split(' ');
    if (names.length >= 2) {
        return (names[0][0] + names[names.length - 1][0]).toUpperCase();
    }
    return names[0][0].toUpperCase();
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'h-6 w-6 text-[10px]';
        case 'lg':
            return 'h-12 w-12 text-lg';
        default:
            return 'h-10 w-10 text-sm';
    }
});

const onlineDotSize = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'h-2 w-2 border';
        case 'lg':
            return 'h-3 w-3 border-2';
        default:
            return 'h-2.5 w-2.5 border-2';
    }
});
</script>

<template>
    <div class="relative inline-block">
        <img
            v-if="user && user.image"
            :src="user.image"
            alt="Avatar"
            :class="[sizeClasses, 'rounded-full object-cover']"
        />
        <div
            v-else
            :class="sizeClasses"
            class="rounded-full flex items-center justify-center font-semibold text-white"
            :style="{ backgroundColor: avatarColor }"
        >
            {{ initials }}
        </div>
        <span
            v-if="showOnline"
            :class="[
                onlineDotSize,
                'absolute rounded-full border-white',
                isOnline ? 'bg-status-online' : 'bg-gray-400',
            ]"
            class="-bottom-0.5 -right-0.5"
        ></span>
    </div>
</template>
