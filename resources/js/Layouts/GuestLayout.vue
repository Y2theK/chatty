<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { MessageCircle } from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const appName = import.meta.env.VITE_APP_NAME || 'Chatty';
const page = usePage();

const loginMessages = [
    { avatar: 'S', text: "Hey! 😊", sent: false },
    { avatar: 'Y', text: "What's up! 🏠", sent: true },
    { avatar: 'S', text: "Wanna grab dinner? 🍕", sent: false },
    { avatar: 'Y', text: "Sure! At 7 👋", sent: true },
    { avatar: 'S', text: "❤️🎉", sent: false },
];

const registerMessages = [
    { avatar: 'J', text: "Hey! Join Chatty! 🎉", sent: false },
    { avatar: 'Y', text: "Really? 🤔", sent: true },
    { avatar: 'J', text: "Yeah! It's super easy 💬", sent: false },
    { avatar: 'Y', text: "Where do I sign up? 🙋", sent: true },
    { avatar: 'J', text: "Just click below! 🚀", sent: false },
];

const isRegister = page.url.includes('/register');
const messages = isRegister ? registerMessages : loginMessages;

const emojis = ['😊', '🎉', '🍕', '❤️', '✨', '💬', '🚀', '👋', '🎈', '💕'];
</script>

<template>
    <div class="min-h-screen flex" style="background-color: oklch(0.99 0.005 60);">
        <!-- Left Side -->
        <div class="hidden lg:flex lg:w-[45%] items-center justify-center p-8 relative overflow-hidden">
            <div class="absolute inset-0" style="background: linear-gradient(135deg, oklch(0.65 0.18 50) 0%, oklch(0.60 0.20 45) 100%);"></div>
            
            <!-- Floating Emojis Background -->
            <div class="absolute inset-0 overflow-hidden">
                <span class="absolute top-20 left-16 text-5xl opacity-10">👋</span>
                <span class="absolute top-40 right-20 text-4xl opacity-10">💬</span>
                <span class="absolute bottom-1/4 left-24 text-6xl opacity-10">❤️</span>
                <span class="absolute bottom-1/3 right-16 text-5xl opacity-10">✨</span>
            </div>

            <!-- Header (Centered) -->
            <div class="relative z-10 text-center">
                <div class="inline-flex items-center justify-center rounded-2xl h-16 w-16 bg-white/20 backdrop-blur-sm mb-4">
                    <MessageCircle class="w-8 h-8 text-white" />
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Welcome to {{ appName }}</h1>
                <p class="text-white/80 text-base">Simple messaging for people you care about.</p>
            </div>

            <!-- Floating Messages -->
            <div class="relative z-10 w-full max-w-md">
                <!-- Row 1 -->
                <div class="flex justify-start mb-4 animate-[fadeInUp_0.5s_ease-out_forwards]" style="animation-delay: 0.2s; opacity: 0;">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: oklch(0.65 0.18 50);">{{ messages[0].avatar }}</div>
                        <div class="bg-white/30 backdrop-blur-sm rounded-2xl rounded-bl-md px-4 py-2.5 text-white text-sm font-medium shadow-lg">
                            {{ messages[0].text }}
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mb-4 animate-[fadeInUp_0.5s_ease-out_forwards]" style="animation-delay: 0.4s; opacity: 0;">
                    <div class="flex items-center gap-2">
                        <div class="bg-white rounded-2xl rounded-br-md px-4 py-2.5 text-sm font-medium shadow-lg" style="color: oklch(0.15 0.01 60);">
                            {{ messages[1].text }}
                        </div>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: oklch(0.55 0.12 280);">{{ messages[1].avatar }}</div>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="flex justify-start mb-4 animate-[fadeInUp_0.5s_ease-out_forwards]" style="animation-delay: 0.6s; opacity: 0;">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: oklch(0.65 0.18 50);">{{ messages[2].avatar }}</div>
                        <div class="bg-white/30 backdrop-blur-sm rounded-2xl rounded-bl-md px-4 py-2.5 text-white text-sm font-medium shadow-lg">
                            {{ messages[2].text }}
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mb-4 animate-[fadeInUp_0.5s_ease-out_forwards]" style="animation-delay: 0.8s; opacity: 0;">
                    <div class="flex items-center gap-2">
                        <div class="bg-white rounded-2xl rounded-br-md px-4 py-2.5 text-sm font-medium shadow-lg" style="color: oklch(0.15 0.01 60);">
                            {{ messages[3].text }}
                        </div>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background-color: oklch(0.55 0.12 280);">{{ messages[3].avatar }}</div>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="flex justify-center animate-[fadeInUp_0.5s_ease-out_forwards]" style="animation-delay: 1s; opacity: 0;">
                    <div class="bg-white/30 backdrop-blur-sm rounded-2xl px-5 py-3 text-2xl shadow-lg flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs" style="background-color: oklch(0.65 0.18 50);">{{ messages[4].avatar }}</div>
                        <span class="text-white">{{ messages[4].text }}</span>
                    </div>
                </div>
            </div>

            <!-- Animated Floating Emojis -->
            <div class="absolute inset-0 pointer-events-none">
                <span class="absolute top-28 left-20 text-3xl animate-bounce" style="animation-delay: 0s;">{{ emojis[0] }}</span>
                <span class="absolute top-40 right-24 text-2xl animate-bounce" style="animation-delay: 0.3s;">{{ emojis[1] }}</span>
                <span class="absolute top-1/3 left-32 text-2xl animate-bounce" style="animation-delay: 0.6s;">{{ emojis[2] }}</span>
                <span class="absolute bottom-1/3 right-20 text-3xl animate-bounce" style="animation-delay: 0.9s;">{{ emojis[3] }}</span>
                <span class="absolute bottom-1/4 left-16 text-2xl animate-bounce" style="animation-delay: 1.2s;">{{ emojis[4] }}</span>
                <span class="absolute bottom-1/3 left-28 text-xl animate-bounce" style="animation-delay: 1.5s;">{{ emojis[5] }}</span>
                <span class="absolute top-1/2 right-32 text-2xl animate-bounce" style="animation-delay: 1.8s;">{{ emojis[6] }}</span>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-[55%] flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-sm">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center justify-center gap-2 mb-6">
                    <div class="flex items-center justify-center rounded-xl h-10 w-10" style="background-color: oklch(0.65 0.18 50);">
                        <MessageCircle class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-xl font-bold" style="color: oklch(0.15 0.01 60);">{{ appName }}</span>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
