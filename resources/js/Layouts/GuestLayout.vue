<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavBar from '@/Components/NavBar.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">

        <div class="relative selection:bg-primary-500 selection:text-primary-50">

            <div class="flex min-h-screen flex-col items-center w-full max-w-2xl mx-auto px-6 lg:max-w-7xl">

                <NavBar />

                <div class="relative flex flex-grow flex-col justify-center w-full">


                    <div class="flex flex-col items-center pt-6 sm:justify-center sm:pt-0">

                        <div>
                            <Link href="/">
                                <ApplicationLogo />
                            </Link>
                        </div>

                        <div
                            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg dark:bg-gray-800">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</template>
