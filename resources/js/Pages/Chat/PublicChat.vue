<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head,useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted,ref } from 'vue';

const props = defineProps({
    'chats' : {
        required: true
    }
});

const form = useForm({
    'message': ''
});

const messages = ref([...props.chats.reverse()]);

const submit = () => {
    form.post(route('chat.public'), {
        preserveScroll: true,
        onFinish: () => form.message = ''
    });
} 

onMounted(() => {

    window.Echo.channel('publicChat')
        .listen("SendPublicMessage", (response) => {
            messages.value.push(response.chat);
        })
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Chat Room
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 mb-4" v-for="chat in messages" :key="chat.id">                    
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                    <span class="text-gray-500">{{ chat.user.name }}  </span>
                      {{ chat.message }} 
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">                    
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <form @submit.prevent="submit">
                        <div class="p-6 text-gray-900 dark:text-gray-100 flex">
                            <TextInput
                                id="message"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.message"
                                required
                                autofocus
                            />
                            <PrimaryButton
                                class="ms-4"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                Send
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
