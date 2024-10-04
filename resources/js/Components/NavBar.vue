<script setup>
import { Link } from '@inertiajs/vue3';
import { SunIcon, MoonIcon, Bars3Icon } from '@heroicons/vue/24/solid';
import { toggleTheme as toggleAppTheme } from '@/theme';
import { ref, onMounted  } from 'vue';

const theme = ref('light');

const toggleTheme = () => {
    toggleAppTheme();
    theme.value = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
}

onMounted(() => {
    theme.value = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
});

</script>

<template>
    <header
        class="w-full grid items-center gap-2 py-2"
    >
        <nav class="-mx-3 flex flex-1">
            <!-- Start -->

            <!-- End -->
            <button
                class="rounded-full ms-auto h-7 w-7 mx-2 self-center text-center text-black ring-1 ring-black dark:ring-white transition hover:text-black/70 dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                @click="toggleTheme()"
            >
                <SunIcon v-if="theme == 'dark'" class="h-5 w-5 mx-auto" />
                <MoonIcon v-else class="h-5 w-5 mx-auto" />
            </button>

            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Dashboard
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Log in
                </Link>

                <Link
                    :href="route('register')"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Register
                </Link>
            </template>
        </nav>
    </header>
</template>
