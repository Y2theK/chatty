<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chat from "@/Pages/Chat/Chat.vue";
import Sidebar from "@/Components/Sidebar.vue";

const props = defineProps({
    conversations: {
        required: true,
    },
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex h-screen antialiased text-slate-800">
            <div class="flex flex-row h-full w-full overflow-x-hidden justify-center">
                <Sidebar :conversations="conversations" :class="[route().current('conversations.index') ? '' : 'hidden sm:flex']"></Sidebar>
                <div class=" flex-col flex-auto h-full p-6" :class="[route().current('conversations.index') ? 'hidden sm:flex' : '']">
                    <div
                        class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-2"
                    >
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
