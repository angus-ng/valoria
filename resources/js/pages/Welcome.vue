<script setup lang="ts">
import CrownIcon from '@/components/icons/CrownIcon.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Valoria - Track Your Collection" />

    <div class="min-h-screen flex flex-col justify-between bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] p-6">
        <header class="flex justify-between items-center max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-1 text-2xl font-bold tracking-tight">
                <CrownIcon class="size-5 fill-current text-black dark:text-white"/>
                <span>Valoria</span>
            </div>

            <nav class="flex gap-4 text-sm">
                <Link v-if="$page.props.auth?.user" :href="route('dashboard')" class="hover:underline">
                Dashboard
                </Link>
                <template v-else>
                <Link :href="route('login')" class="hover:underline">Log in</Link>
                <Link :href="route('register')" class="hover:underline">Register</Link>
                </template>
            </nav>
        </header>

        <main class="flex-grow flex flex-col items-center justify-center text-center px-4">
            <h2 class="text-3xl font-bold mb-4">Master the Hunt. Build Your Legacy.</h2>
            <p class="text-lg max-w-xl text-[#6B6B66] dark:text-[#A1A09A] mb-6">
                Welcome to <strong>Valoria</strong> — your companion app for tracking crown progress,
                layered armor sets, and (soon) layered weapons. Every hunt, every build, all in one place.
            </p>
            <div class="flex gap-4">
                <Link
                    :href="route('register')"
                    class="bg-[#1b1b18] text-white dark:bg-white dark:text-black px-5 py-2 rounded-sm hover:opacity-90"
                >
                    Start Tracking
                </Link>
                <Link
                    :href="route('login')"
                    class="border border-[#1b1b18] dark:border-white px-5 py-2 rounded-sm hover:bg-[#f0f0ee] dark:hover:bg-[#1f1f1f]"
                >
                    Log In
                </Link>
            </div>
        </main>

        <footer class="text-xs text-center text-[#8e8e8a] dark:text-[#A1A09A] mt-8">
            &copy; {{ new Date().getFullYear() }} Valoria – For Hunters, By Hunters.
        </footer>
    </div>
</template>
