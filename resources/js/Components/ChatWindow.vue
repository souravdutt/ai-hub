<script setup>
import ChatMessage from '@/Components/ChatMessage.vue';
import { computed } from 'vue';

const props = defineProps({
    messages: {
        type: Array,
        required: true
    }
});

const messages = computed(() => props.messages);

console.log("messages:", messages.value);
</script>

<template>
    <div class="grid w-full gap-6 lg:gap-8 max-h-[calc(100vh-11rem)] overflow-auto">

        <div
            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg p-6 md:row-span-3 lg:p-10 lg:pb-10"
        >

            <div class="w-full">
                <ChatMessage v-for="message in messages" :key="message.id" :message="message" />
            </div>

        </div>

        <textarea name="message" rows="1"
            class="absolute bottom-0 w-full mt-6 resize-none rounded-full border-2 border-black/40 bg-white px-4 py-3 text-lg text-black/70 placeholder:text-black/40 dark:border-white/40 dark:bg-zinc-900 dark:text-white/70 dark:placeholder:text-white/40"
            placeholder="Type a message here..."
        ></textarea>

    </div>
</template>
