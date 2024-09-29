<script setup>
import { ArrowLeftIcon, ArrowRightIcon } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';

// Props
const props = defineProps({
    sidebarVisible: Boolean
});

// Emit function to notify the parent component
const emit = defineEmits(['toggle-sidebar']);

const toggleSidebar = () => {
    emit('toggle-sidebar');  // Emit event to parent
};
</script>

<template>
    <aside
        @blur="toggleSidebar"
        :class="{ '-ms-64': sidebarVisible, 'ms-0': !sidebarVisible }"
        class="flex fixed flex-col z-50 w-64 h-screen px-5 py-8 overflow-y-auto transition-all duration-300 bg-white border-r rtl:border-r-0 rtl:border-l dark:bg-gray-900 dark:border-gray-700"
    >
        <button
            @click="toggleSidebar"
            :class="{ 'start-64 -ms-6 rounded-s': !sidebarVisible, 'start-0 rounded-e': sidebarVisible }"
            class="fixed top-1/2 z-10 h-12 px-1 opacity-50 hover:opacity-100 transition-all duration-300 bg-black text-white dark:bg-white dark:text-black"
            :title="{ true: 'Hide Sidebar', false: 'Show Sidebar' }[sidebarVisible]"
        >
            <ArrowLeftIcon v-if="!sidebarVisible" class="w-4 h-4" />
            <ArrowRightIcon v-else class="w-4 h-4" />
        </button>

        <Link
            :href="route('welcome')"
            class="text-xl font-extrabold"
        >
            {{ $page.props.app.name }}
        </Link>

        <div class="flex flex-col justify-between flex-1 mt-6">
            <nav class="-mx-3 space-y-6 ">
                <div class="space-y-3 ">
                    <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">
                        NLP Models
                    </label>

                    <Link
                        class="flex items-center px-3 py-2 text-gray-600 transition-colors duration-300 transform rounded-lg dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700"
                        :href="route('welcome')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                        </svg>

                        <span class="mx-2 text-sm font-medium">EveryDay AI <span class="text-xs bg-red-500 ms-1 text-white p-1 rounded">New</span></span>
                    </Link>

                </div>
            </nav>
        </div>
    </aside>
</template>
