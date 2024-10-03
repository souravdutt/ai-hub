<script setup>
import NavBar from '@/Components/NavBar.vue';
import SideBar from '@/Components/SideBar.vue';
import ChatWindow from '@/Components/ChatWindow.vue';
import { onMounted, ref }  from 'vue';

const sidebarHidden = ref(localStorage.sidebar == 'true');

const toggleSidebar = () => {
    sidebarHidden.value = localStorage.sidebar = !sidebarHidden.value;
}

onMounted(() => {
    //check window size
    if (window.innerWidth < 768) {
        localStorage.sidebar = 'true';
    }
    sidebarHidden.value = localStorage.sidebar == 'true';
});

</script>

<template>
    <Head title="Welcome" />

    <div class="bg-gray-50 flex text-black/50 dark:bg-black dark:text-white/50">

        <SideBar :sidebar-hidden="sidebarHidden" @toggle-sidebar="toggleSidebar" />

        <div
            :class="{ 'lg:ms-64': !sidebarHidden }"
            class="w-full relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white transition-all duration-300"
        >
            <div class="relative w-full px-6 ">
                <NavBar />

                <main class="relative mt-2 max-w-4xl mx-auto min-h-[calc(100vh-7rem)]">

                    <ChatWindow />

                </main>
            </div>
        </div>
    </div>
</template>
