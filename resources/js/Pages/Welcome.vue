<script setup>
import NavBar from '@/Components/NavBar.vue';
import SideBar from '@/Components/SideBar.vue';
import ChatWindow from '@/Components/ChatWindow.vue';
import { onMounted, ref }  from 'vue';

const sidebarVisible = ref(localStorage.sidebar == 'true');

const toggleSidebar = () => {
    sidebarVisible.value = localStorage.sidebar = !sidebarVisible.value;
}

onMounted(() => {
    sidebarVisible.value = localStorage.sidebar == 'true';
});

// dummy messages
const messages = ref([
    {
        id: 1,
        body: 'Hello, What can you do for me?',
        user: 'user',
    },
    {
        id: 2,
        body: 'Hello, I can help you with any of the following:\n-Everyday task: I can help you with your everyday tasks.\n-Chatbot: I can help you with any of the chatbot.\n-Anything else: I can help you with anything.',
        user: 'bot',
    },
    {
        id: 3,
        body: 'I need you to make a plan creating an AI chat bot.',
        user: 'user',
    },
    {
        id: 4,
        body: 'That\'s great.\n\nHere is the plan:\n\n-Explore the latest tech stack.\n-Decide the API to use for AI chat bot.\n-Go through the API documentation.\n-Create an AI chat bot\n-Deploy the AI chat bot.\n\nI hope that helps, wish you all the best!',
        user: 'bot',
    },
    {
        id: 5,
        body: 'Thank you!',
        user: 'user',
    },
    {
        id: 6,
        body: 'I am happy to help!',
        user: 'bot',
    },
    {
        id: 7,
        body: 'Will get back if I need help.',
        user: 'user',
    },
    {
        id: 8,
        body: 'Sure, anytime!',
        user: 'bot',
    },
])
</script>

<template>
    <Head title="Welcome" />

    <div class="bg-gray-50 flex text-black/50 dark:bg-black dark:text-white/50">

        <SideBar :sidebar-visible="sidebarVisible" @toggle-sidebar="toggleSidebar" />

        <div
            :class="{ 'lg:ms-64': !sidebarVisible }"
            class="w-full relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white transition-all duration-300"
        >
            <div class="relative w-full px-6 ">
                <NavBar />

                <main class="relative mt-2 max-w-4xl mx-auto min-h-[calc(100vh-7rem)]">

                    <ChatWindow :messages="messages" />

                </main>

                <!-- <footer
                    class="py-16 text-center text-sm text-black dark:text-white/70"
                >
                    AI Hub v1.0.0
                </footer> -->
            </div>
        </div>
    </div>
</template>
