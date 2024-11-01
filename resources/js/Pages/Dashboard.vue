<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Chat from "@/Pages/Chat/Chat.vue";
import Sidebar from "@/Components/Sidebar.vue";

const props = defineProps({
    users: {
        required: true,
    },
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex h-screen antialiased text-slate-800">
            <div class="flex flex-row h-full w-full overflow-x-hidden">
                <Sidebar :users="users"></Sidebar>
                <div class="flex flex-col flex-auto h-full p-6">
                    <div
                        class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-4"
                    >
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
