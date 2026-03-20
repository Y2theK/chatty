<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { MessageCircle, Zap, Users, Image, Shield, Check, Heart, Clock, Phone, MessageSquare, Bell, Lock, Smile, FlashlightIcon, LightbulbIcon } from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const year = new Date().getFullYear();
const appName = import.meta.env.VITE_APP_NAME || 'Chatty';

const features = [
    { icon: MessageSquare, title: 'Private & Group Chats', desc: 'Talk one-on-one with friends or create groups for everyone.' },
    { icon: LightbulbIcon, title: 'Instant Messaging', desc: 'Messages arrive immediately with read receipts.' },
    { icon: Image, title: 'Share Photos', desc: 'Send photos and files easily in your chats.' },
];

const stats = [
    { value: '10K+', label: 'Active Users' },
    { value: '1M+', label: 'Messages Sent' },
    { value: '50+', label: 'Countries' },
    { value: '99.9%', label: 'Uptime' },
];

const steps = [
    { num: '1', title: 'Create your account', desc: 'Sign up with just your email and a password. Takes less than a minute.' },
    { num: '2', title: 'Find your friends', desc: 'Search for contacts or invite new people to join you on Chatty.' },
    { num: '3', title: 'Start chatting', desc: 'Send your first message and keep the conversation going.' },
];

const testimonials = [
    { text: "I use Chatty every day to stay in touch with my family overseas. It's so simple and works perfectly.", author: 'Maria G.', role: 'Remote Worker' },
    { text: "Finally a chat app that doesn't try to do everything. Just messaging, done right. So good!", author: 'Tom R.', role: 'University Student' },
    { text: "My parents struggled with complicated apps. Chatty was easy enough for them to use right away.", author: 'Sarah K.', role: 'Teacher' },
];

const whyChatty = [
    'No ads, ever',
    'No data selling',
    'Simple and fast',
    'Works on all devices',
    'Free forever',
    'Your data stays private',
];
</script>

<template>
    <Head title="Welcome" />
    
    <div class="min-h-screen" style="background-color: oklch(0.99 0.005 60);">
        <!-- Navigation -->
        <nav class="px-6 py-5">
            <div class="max-w-5xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center rounded-xl h-10 w-10" style="background-color: oklch(0.65 0.18 50);">
                        <MessageCircle class="w-5 h-5 text-white" />
                    </div>
                    <span class="font-bold text-xl" style="color: oklch(0.15 0.01 60);">{{ appName }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('conversations.index')" class="px-5 py-2 rounded-xl font-medium text-sm text-white" style="background-color: oklch(0.65 0.18 50);">
                            Go to App
                        </Link>
                    </template>
                    <template v-else>
                        <Link v-if="canLogin" :href="route('login')" class="px-4 py-2 rounded-lg font-medium text-sm" style="color: oklch(0.45 0.01 60);">
                            Sign in
                        </Link>
                        <Link v-if="canRegister" :href="route('register')" class="px-5 py-2 rounded-xl font-medium text-sm text-white" style="background-color: oklch(0.65 0.18 50);">
                            Get Started
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="px-6 py-16">
            <div class="max-w-5xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-4" style="background-color: oklch(0.96 0.04 50);">
                            <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: oklch(0.65 0.18 50);"></span>
                            <span class="text-xs font-medium" style="color: oklch(0.65 0.18 50);">Messaging made simple</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight" style="color: oklch(0.15 0.01 60);">
                            Stay close to the<br />
                            <span style="color: oklch(0.65 0.18 50);">people who matter</span>
                        </h1>
                        <p class="text-lg mb-8" style="color: oklch(0.45 0.01 60);">
                            Simple, honest messaging for friends and family. No clutter, no complications. Just conversations.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <template v-if="$page.props.auth.user">
                                <Link :href="route('conversations.index')" class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-white" style="background-color: oklch(0.65 0.18 50);">
                                    Go to Messages
                                </Link>
                            </template>
                            <template v-else>
                                <Link v-if="canRegister" :href="route('register')" class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-white" style="background-color: oklch(0.65 0.18 50);">
                                    Start Chatting Free
                                </Link>
                                <Link v-if="canLogin" :href="route('login')" class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-medium border" style="color: oklch(0.15 0.01 60); border-color: oklch(0.91 0.005 60);">
                                    I Have an Account
                                </Link>
                            </template>
                        </div>
                    </div>

                    <!-- Chat Preview -->
                    <div class="relative">
                        <!-- Floating Emojis -->
                        <div class="absolute -top-4 -left-4 text-4xl animate-bounce" style="animation-delay: 0s;">👋</div>
                        <div class="absolute -top-2 -right-4 text-3xl animate-bounce" style="animation-delay: 0.3s;">❤️</div>
                        <div class="absolute top-1/2 -left-6 text-3xl animate-bounce" style="animation-delay: 0.6s;">😊</div>
                        <div class="absolute -bottom-4 -right-4 text-4xl animate-bounce" style="animation-delay: 0.9s;">🎉</div>
                        <div class="absolute bottom-0 -left-4 text-2xl animate-bounce" style="animation-delay: 1.2s;">✨</div>

                        <div class="rounded-2xl border shadow-2xl overflow-hidden" style="background-color: oklch(1 0 0); border-color: oklch(0.91 0.005 60);">
                            <!-- Chat Header -->
                            <div class="px-4 py-3 border-b flex items-center gap-3" style="border-color: oklch(0.93 0.005 60);">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: oklch(0.65 0.18 50);">A</div>
                                <div>
                                    <p class="font-semibold text-sm" style="color: oklch(0.15 0.01 60);">Alex & Sarah</p>
                                    <p class="text-xs" style="color: oklch(0.55 0.008 60);">Active now</p>
                                </div>
                            </div>
                            <!-- Chat Messages -->
                            <div class="p-4 space-y-3" style="min-height: 280px; background-color: oklch(0.99 0.005 60);">
                                <!-- Message 1 -->
                                <div class="flex justify-end">
                                    <div class="max-w-[70%] px-4 py-2 rounded-2xl rounded-br-md text-sm text-white" style="background-color: oklch(0.65 0.18 50);">
                                        Hey! Are you coming to dinner tonight? 🍕
                                    </div>
                                </div>
                                <!-- Message 2 -->
                                <div class="flex justify-start">
                                    <div class="max-w-[70%] px-4 py-2 rounded-2xl rounded-bl-md text-sm" style="background-color: oklch(0.96 0.04 50); color: oklch(0.15 0.01 60);">
                                        Yes! Can't wait! Should I bring wine? 🍷
                                    </div>
                                </div>
                                <!-- Message 3 -->
                                <div class="flex justify-end">
                                    <div class="max-w-[70%] px-4 py-2 rounded-2xl rounded-br-md text-sm text-white" style="background-color: oklch(0.65 0.18 50);">
                                        That would be perfect! See you at 7 👋
                                    </div>
                                </div>
                                <!-- Message 4 -->
                                <div class="flex justify-start">
                                    <div class="max-w-[70%] px-4 py-2 rounded-2xl rounded-bl-md text-sm" style="background-color: oklch(0.96 0.04 50); color: oklch(0.15 0.01 60);">
                                        See you there! 💕
                                    </div>
                                </div>
                                <!-- Typing Indicator -->
                                <div class="flex justify-start">
                                    <div class="px-4 py-3 rounded-2xl rounded-bl-md" style="background-color: oklch(0.96 0.04 50);">
                                        <div class="flex gap-1">
                                            <span class="w-2 h-2 rounded-full animate-bounce" style="background-color: oklch(0.65 0.18 50); animation-delay: 0s;"></span>
                                            <span class="w-2 h-2 rounded-full animate-bounce" style="background-color: oklch(0.65 0.18 50); animation-delay: 0.15s;"></span>
                                            <span class="w-2 h-2 rounded-full animate-bounce" style="background-color: oklch(0.65 0.18 50); animation-delay: 0.3s;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="px-6 py-10" style="background-color: oklch(0.96 0.04 50);">
            <div class="max-w-5xl mx-auto">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div v-for="(stat, i) in stats" :key="i">
                        <div class="text-2xl md:text-3xl font-bold" style="color: oklch(0.65 0.18 50);">{{ stat.value }}</div>
                        <div class="text-sm font-medium" style="color: oklch(0.45 0.01 60);">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="px-6 py-16">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <h2 class="text-2xl font-bold mb-2" style="color: oklch(0.15 0.01 60);">Everything you need to stay connected</h2>
                    <p class="text-base" style="color: oklch(0.45 0.01 60);">Simple features that actually matter.</p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div
                        v-for="(feature, i) in features"
                        :key="i"
                        class="text-center"
                    >
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: oklch(0.96 0.04 50);">
                            <component :is="feature.icon" class="w-7 h-7" style="color: oklch(0.65 0.18 50);" />
                        </div>
                        <h3 class="font-semibold mb-2" style="color: oklch(0.15 0.01 60);">{{ feature.title }}</h3>
                        <p class="text-sm" style="color: oklch(0.45 0.01 60);">{{ feature.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="px-6 py-20" style="background-color: oklch(0.98 0.005 60);">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-3" style="color: oklch(0.15 0.01 60);">Getting started is easy</h2>
                    <p class="text-lg" style="color: oklch(0.45 0.01 60);">Three simple steps to start chatting.</p>
                </div>
                <div class="grid md:grid-cols-3 gap-6">
                    <div v-for="(step, i) in steps" :key="i" class="relative p-6 rounded-2xl border" style="border-color: oklch(0.91 0.005 60); background-color: oklch(1 0 0);">
                        <div class="absolute -top-4 left-6 px-3 py-1 rounded-full text-sm font-bold text-white" style="background-color: oklch(0.65 0.18 50);">
                            Step {{ step.num }}
                        </div>
                        <div class="pt-4">
                            <h3 class="font-semibold text-lg mb-2" style="color: oklch(0.15 0.01 60);">{{ step.title }}</h3>
                            <p class="text-sm" style="color: oklch(0.45 0.01 60);">{{ step.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Chatty Section -->
        <section class="px-6 py-20">
            <div class="max-w-5xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-bold mb-4" style="color: oklch(0.15 0.01 60);">Why choose Chatty?</h2>
                        <p class="text-lg mb-6" style="color: oklch(0.45 0.01 60);">
                            We believe messaging should be simple. No gimmicks, no data collection, no monthly fees. Just you and the people you care about.
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div v-for="(item, i) in whyChatty" :key="i" class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded-full flex items-center justify-center" style="background-color: oklch(0.65 0.18 50);">
                                    <Check class="w-3 h-3 text-white" />
                                </div>
                                <span class="text-sm font-medium" style="color: oklch(0.15 0.01 60);">{{ item }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl p-8 border" style="border-color: oklch(0.91 0.005 60); background-color: oklch(0.96 0.04 50);">
                        <div class="flex items-start gap-4 mb-6">
                            <Clock class="w-6 h-6 shrink-0 mt-1" style="color: oklch(0.65 0.18 50);" />
                            <div>
                                <h3 class="font-semibold mb-1" style="color: oklch(0.15 0.01 60);">Always Free</h3>
                                <p class="text-sm" style="color: oklch(0.45 0.01 60);">No premium tiers, no locked features. Everything is free, forever.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <Heart class="w-6 h-6 shrink-0 mt-1" style="color: oklch(0.65 0.18 50);" />
                            <div>
                                <h3 class="font-semibold mb-1" style="color: oklch(0.15 0.01 60);">Made with Care</h3>
                                <p class="text-sm" style="color: oklch(0.45 0.01 60);">Built by people who believe in simple, honest communication.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="px-6 py-20" style="background-color: oklch(0.98 0.005 60);">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-3" style="color: oklch(0.15 0.01 60);">What people are saying</h2>
                </div>
                <div class="grid md:grid-cols-3 gap-6">
                    <div
                        v-for="(testimonial, i) in testimonials"
                        :key="i"
                        class="p-6 rounded-2xl border transition-all hover:shadow-lg"
                        style="border-color: oklch(0.91 0.005 60); background-color: oklch(1 0 0);"
                    >
                        <div class="flex gap-1 mb-4">
                            <span v-for="n in 5" :key="n" style="color: oklch(0.65 0.18 50);">★</span>
                        </div>
                        <p class="text-sm mb-4 leading-relaxed" style="color: oklch(0.45 0.01 60);">"{{ testimonial.text }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white" style="background-color: oklch(0.65 0.18 50);">
                                {{ testimonial.author[0] }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm" style="color: oklch(0.15 0.01 60);">{{ testimonial.author }}</p>
                                <p class="text-xs" style="color: oklch(0.55 0.008 60);">{{ testimonial.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="px-6 py-20">
            <div class="max-w-3xl mx-auto text-center">
                <template v-if="$page.props.auth.user">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: oklch(0.15 0.01 60);">
                        Welcome back!
                    </h2>
                    <p class="text-lg mb-8" style="color: oklch(0.45 0.01 60);">
                        Ready to catch up with your friends and family?
                    </p>
                    <Link :href="route('conversations.index')" class="inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold text-white" style="background-color: oklch(0.65 0.18 50);">
                        Open Chatty
                    </Link>
                </template>
                <template v-else>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: oklch(0.15 0.01 60);">
                        Ready to start chatting?
                    </h2>
                    <p class="text-lg mb-8" style="color: oklch(0.45 0.01 60);">
                        Join thousands of people staying connected with friends and family.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link v-if="canRegister" :href="route('register')" class="inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold text-white" style="background-color: oklch(0.65 0.18 50);">
                            Create Your Free Account
                        </Link>
                        <Link v-if="canLogin" :href="route('login')" class="inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold border-2" style="color: oklch(0.65 0.18 50); border-color: oklch(0.65 0.18 50);">
                            Sign In Instead
                        </Link>
                    </div>
                    <p class="mt-6 text-sm" style="color: oklch(0.45 0.01 60);">
                        No credit card required · Free forever
                    </p>
                </template>
            </div>
        </section>

        <!-- Footer -->
        <footer class="px-6 py-8 border-t" style="border-color: oklch(0.91 0.005 60);">
            <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center rounded-lg h-7 w-7" style="background-color: oklch(0.65 0.18 50);">
                        <MessageCircle class="w-3.5 h-3.5 text-white" />
                    </div>
                    <span class="font-medium" style="color: oklch(0.15 0.01 60);">{{ appName }}</span>
                </div>
                <p class="text-sm flex items-center gap-1" style="color: oklch(0.45 0.01 60);">
                    Made with <Heart class="w-3.5 h-3.5 fill-current" style="color: oklch(0.65 0.18 50);" /> · {{ appName }} © {{ year }}
                </p>
            </div>
        </footer>
    </div>
</template>
