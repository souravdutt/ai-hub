<script setup>

const props = defineProps({
    message: {
        type: Object,
        required: true
    },
    type: {
        type: String,
        required: true
    }
});

</script>

<template>
    <div
        class="flex flex-col items-start gap-6 overflow-hidden my-8 md:row-span-3"
    >

        <div
            class="w-full max-w-[88%]"
            :class="{
                'self-end rounded-lg border border-black/20 bg-white px-4 py-4 dark:border-white/10 dark:bg-zinc-900 text-black/70 dark:text-white/70': message.sender === 'user'
            }"
        >
            <div
                class="w-full text-lg rounded-lg"
            >

                <div v-if="message.typing" role="status" class="py-4 animate-pulse md:flex md:items-center">
                    <div class="flex items-center justify-center w-[320px] h-[320px] bg-gray-300 rounded-lg dark:bg-gray-700">
                        <svg class="w-10 h-10 text-gray-200 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                            <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                        </svg>
                    </div>
                    <span class="sr-only">Loading...</span>
                </div>

                <div v-else v-html="message.body" />

            </div>
        </div>
    </div>
</template>
