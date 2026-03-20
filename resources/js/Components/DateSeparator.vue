<script setup>
import { computed } from 'vue';
import moment from 'moment';

const props = defineProps({
    date: {
        type: String,
        required: true,
    },
});

const formattedDate = computed(() => {
    const d = moment(props.date);
    const today = moment().startOf('day');
    const yesterday = moment().subtract(1, 'day').startOf('day');

    if (d.isSame(today, 'day')) {
        return 'Today';
    } else if (d.isSame(yesterday, 'day')) {
        return 'Yesterday';
    } else if (d.isSame(moment(), 'week')) {
        return d.format('dddd');
    }
    return d.format('MMMM D, YYYY');
});
</script>

<template>
    <div class="flex items-center justify-center py-4">
        <div class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-bg-surface-raised border border-border-subtle">
            <span class="text-[11px] text-text-muted font-medium">{{ formattedDate }}</span>
        </div>
    </div>
</template>
