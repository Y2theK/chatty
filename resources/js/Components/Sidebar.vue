<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, onBeforeMount, onMounted, onUnmounted, ref } from "vue";
import { Button } from "@/components/ui/button";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { PlusSquareIcon, Search, UserPen } from "lucide-vue-next";

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { toast } from "@/components/ui/toast";
import { toTypedSchema } from "@vee-validate/zod";
import { useForm } from "vee-validate";

import { h } from "vue";
import * as z from "zod";
import { defaultDocument } from "@vueuse/core";

const formSchema = toTypedSchema(
    z.object({
        message: z.string().min(2).max(50),
        email: z.string().email(),
    })
);

const form = useForm({
    validationSchema: formSchema,
});

const props = defineProps({
    conversations: {
        required: true,
    },
    allOnlineUsers: {
        required: true,
    },
});
const conversations = computed(() => {    
    return props.conversations.sort((a,b) => new Date(b.updated_at) - new Date(a.updated_at))
})

const user = computed(() => usePage().props.auth.user)

const groupColors = [
    "bg-red-200",
    "bg-green-200",
    "bg-purple-200",
    "bg-yellow-200",
    "bg-violet-200",
];

const searchUsers = ref([]);
const search = ref(null);

const isLatestMessageSeenByAuthUser = (conversation) => {
  
    if(conversation.latest_message.user_id == user.value.id){
        return true;
    }
    if(conversation.latest_message.seen_by){        
        return conversation.latest_message.seen_by.split(',').includes(user.value.id.toString());
    }
    return false;
}

const moveConversationOnTop = (updatedConversation) => {
      // Find the updated conversation and move it to the top of the list
      const index = conversations.value.findIndex(
        (c) => c.id === updatedConversation.id
      );
      
      if (index !== -1) {
        conversations.value.splice(index, 1); // Remove it from its current position
      }
      // Add it to the top
      conversations.value.unshift(updatedConversation);
    }

onMounted(() => {

    window.Echo.private(`user.${user.value.id}`)
        .listen("ConversationUpdate", (response) => {
            moveConversationOnTop(response.conversation);
        })

});


onUnmounted(() => {
    updateLastActiveAt();
});

const onSubmit = form.handleSubmit(async (values) => {
    await createConversation(values.email, values.message);
});

const createConversation = async (email, message) => {
    try {
        const response = await axios.post(`/conversations/create`, {
            email: email,
            message: message,
        });
        window.location.href = response.data.redirect;
    } catch (error) {
        console.error("Failed to send message:", error);
    }
};

const updateLastActiveAt = async (id) => {
    try {
        const response = await axios.post(`/users/updateLastActiveAt`);
        // window.location.href = response.data.redirect;
    } catch (error) {
        console.error("Failed to send message:", error);
    }
};

const fetchUsers = async () => {
    const params = { search: search.value }; // Set params

    try {
        // Use axios.get with params as the second argument
        const response = await axios.get(route("users.index"), { params });

        searchUsers.value = response.data;

    } catch (error) {
        console.error("Failed to fetch users:", error);
    }
};
</script>

<template>
    <div
        class="flex flex-col py-8 pl-6 pr-6 flex-shrink-0 w-full sm:w-64 sm:pr-1"
    >
        <Link
            :href="route('dashboard')"
            class="flex flex-row items-center justify-center h-12 w-full"
        >
            <div
                class="flex items-center justify-center rounded-2xl text-indigo-700 bg-indigo-100 h-10 w-10"
            >
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                    ></path>
                </svg>
            </div>
            <div class="ml-2 font-bold text-2xl">Chatty</div>
        </Link>
        <div
            class="flex flex-col items-center bg-indigo-100 border border-gray-200 mt-4 w-full py-6 px-4 rounded-lg"
        >
            <div
                class="h-20 w-20 rounded-full border overflow-hidden"
                v-if="$page.props.auth.user.image"
            >
                <img
                    :src="$page.props.auth.user.image"
                    alt="Avatar"
                    class="h-full w-full"
                />
            </div>
            <div
                class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full"
                v-else
            >
                {{ $page.props.auth.user.name[0] }}
            </div>

            <div class="text-sm font-semibold mt-2">
                {{ $page.props.auth.user.name }}
            </div>
            <div class="text-xs text-gray-500">
                {{ $page.props.auth.user.email }}
            </div>
            <div class="flex flex-row items-center my-3">
                <div
                    class="flex flex-col justify-center h-4 w-8 bg-indigo-500 rounded-full"
                >
                    <div
                        class="h-3 w-3 bg-white rounded-full self-end mr-1"
                    ></div>
                </div>
                <div class="leading-none ml-1 text-xs">Active</div>
            </div>
            <div class="flex items-center justify-center">
                <ResponsiveNavLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                >
                    <Button class="w-full bg-red-600">Quit Chatting</Button>
                </ResponsiveNavLink>
                <Link :href="route('profile.edit')">
                    <Button variant="outline" size="icon" class="">
                        <UserPen class="w-4 h-4" />
                    </Button>
                </Link>
            </div>
        </div>
        <div class="flex justify-center items-center">
            <div class="relative w-full max-w-sm items-center mt-2">
                <Input
                    id="search"
                    type="text"
                    placeholder="Search..."
                    class="pl-10 focus-visible:ring-0 focus-visible:ring-offset-0"
                    v-model="search"
                    @keyup="fetchUsers"
                />
                <span
                    class="absolute start-0 inset-y-0 flex items-center justify-center px-2"
                >
                    <Search class="size-6 text-muted-foreground" />
                </span>
            </div>
            <div class="mt-2 flex justify-center">
                <!-- <Form
                    v-slot="{ submitForm }"
                    as=""
                    :validation-schema="formSchema"
                    @submit="onSubmit"
                > -->
                <Dialog>
                    <DialogTrigger as-child>
                        <Button variant="outline" size="icon" class="ml-2">
                            <PlusSquareIcon class="w-4 h-4" />
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle>Create New Group </DialogTitle>
                            <DialogDescription>
                                Invite someone you know by email to start a new
                                conversation or group.
                            </DialogDescription>
                        </DialogHeader>

                        <form @submit="onSubmit">
                            <FormField v-slot="{ componentField }" name="email">
                                <FormItem>
                                    <FormLabel>Email</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="email"
                                            placeholder="demo@gmail.com"
                                            v-bind="componentField"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                            <FormField
                                v-slot="{ componentField }"
                                name="message"
                            >
                                <FormItem class="mt-2">
                                    <FormLabel>Message</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="text"
                                            placeholder="hi wassup"
                                            v-bind="componentField"
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                            <DialogFooter>
                                <Button
                                    class="mt-2"
                                    type="submit"
                                    @click="onSubmit"
                                    form="dialogForm"
                                >
                                    Send
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
                <!-- </Form> -->
            </div>
        </div>
        <div class="flex flex-col mt-8" v-show="!search">
            <div class="flex flex-row items-center justify-between text-xs">
                <span class="font-bold">Online</span>
                <!-- online count have to exclude current user count -->
                <span
                    class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
                    >{{
                        allOnlineUsers.length > 0
                            ? allOnlineUsers.length - 1
                            : "0"
                    }}</span
                >
            </div>
            <div
                class="flex flex-col space-y-1 mt-4 -mx-2 overflow-y-auto [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-gray-400"
            >
                <Link
                    :href="route('conversations.show', conversation.id)"
                    v-for="conversation in conversations"
                    :class="[
                        route().current('conversations.show', conversation.id)
                            ? 'bg-gray-100'
                            : '',
                    ]"
                    class="rounded-xl"
                    :key="conversation.id"
                >
                    <div
                        class="flex justify-between items-center hover:bg-gray-100 rounded-xl"
                    >
                        <div>
                        
                            <div class="flex flex-row items-center p-2">
                                <div
                                    class=""
                                    v-for="(
                                        user, index
                                    ) in conversation.users.slice(0, 5)"
                                    :key="user.id"
                                >
                                    <div class="relative" v-if="user.id != $page.props.auth.user.id">
                                        <img
                                            v-if="user.image"
                                            :src="user.image"
                                            alt="Avatar"
                                            class="h-8 w-8 rounded-full border-3  -ml-1 border-indigo-200"
                                        />
                                        <div
                                            v-else
                                            
                                            :class="
                                                groupColors[
                                                    index % groupColors.length
                                                ]
                                            "
                                            class="flex items-center justify-center h-8 w-8 -ml-1 rounded-full"
                                        >
                                            {{ user.name[0] }}
                                        </div>
                                        <span
                                            :class="
                                                allOnlineUsers.find(
                                                    (u) => u.id == user.id
                                                )
                                                    ? 'bg-green-500'
                                                    : 'bg-red-400'
                                            "
                                            class="inline-block h-3 w-3 rounded-full ml-2 absolute top-5 left-3 border-2 border-white"
                                        ></span>
                                    </div>
                                </div>
                                <div class="ml-2" >
                                    <div v-if="conversation.is_group && conversation.name">
                                       <span class="text-sm font-semibold">
                                            {{ conversation.name }}
                                       </span> 
                                    </div>
                                    <div v-else>
                                        <p v-for="(
                                        user
                                        ) in conversation.users"
                                        :key="user.id">
                                            <span v-if="user.id != $page.props.auth.user.id" class="text-sm font-semibold">
                                            {{ user.name }}
                                            </span>                                        
                                        </p>
                                    </div>
                                    <small :class="[isLatestMessageSeenByAuthUser(conversation) ? '' : 'font-bold']"
                                        class="flex"
                                        v-if="conversation.latest_message"
                                        >{{
                                            conversation.latest_message.message.slice(0,20)
                                        }}</small
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
        <div class="flex flex-col mt-8" v-show="search">
            <div
                class="flex flex-col space-y-1 mt-4 -mx-2 overflow-y-auto [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-gray-400"
            >
                <div
                    v-for="searchUser in searchUsers"
                    class="rounded-xl cursor-pointer"
                    :key="searchUser.id"
                >
                    <div
                        class="flex justify-between items-center hover:bg-gray-100 rounded-xl"
                        @click="createConversation(searchUser.email)"
                    >
                        <div>
                            <div
                                class="flex flex-row items-center rounded-xl p-2"
                            >
                                <img
                                    v-if="searchUser.image"
                                    :src="searchUser.image"
                                    alt="Avatar"
                                    class="h-8 w-8 rounded-full border-3 border-indigo-200"
                                />
                                <div
                                    v-else
                                    class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full"
                                >
                                    {{ searchUser.name[0] }}
                                </div>
                                <div class="ml-2 text-sm font-semibold">
                                    {{ searchUser.name }}
                                    <span
                                        :class="
                                            allOnlineUsers.find(
                                                (u) => u.id == searchUser.id
                                            )
                                                ? 'bg-green-500'
                                                : 'bg-red-400'
                                        "
                                        class="inline-block h-2 w-2 rounded-full"
                                    ></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
