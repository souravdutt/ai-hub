<script setup>
import ChatMessage from '@/Components/ChatMessage.vue';
import { ArrowUpIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import { defineModel } from 'vue';

const form = defineModel('form', {
    type: Object,
    required: true,
});

const sendMessage = defineModel('sendMessage', {
    type: Function,
    required: true,
});

const characterCount = defineModel('characterCount', {
    type: Number,
    required: true,
});

// props
defineProps({
    messages: Object,
    errors: Object,
    disabled: Boolean,
});

</script>

<template>
    <div id="chatWindow" ref="chatWindow" class="grid w-full gap-6 lg:gap-8 max-h-[calc(100vh-10.5rem)] overflow-auto">

        <div
            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg p-6 md:row-span-3 lg:p-10 lg:pb-10"
        >

            <div class="w-full">
                <ChatMessage v-for="message in messages" :key="message.id" :message="message" />
            </div>

        </div>

        <form
            @submit.prevent="sendMessage"
            class="flex flex-col absolute bottom-0 w-full mt-6"
        >
            <div v-if="errors" class="flex items-center justify-between gap-2 bg-red-200 text-red-600 p-3 rounded-lg my-2">
                {{ errors.message }}
                <button type="button" class="rounded border border-red-600" @click="errors = null">
                    <XMarkIcon class="h-5 w-5" />
                </button>
            </div>

            <div
                class="w-full text-end text-xs px-4 mb-1"
                :class="{
                    'text-yellow-600': characterCount > 2000 && characterCount <= 3000,
                    'text-red-600': characterCount > 3000
                }"
            >
                {{ characterCount }} / 3000
            </div>

            <div class="relative flex items-center bottom-0 w-full">
                <textarea name="message" rows="1"
                    autofocus
                    class="w-full resize-none rounded-full border-2 border-black bg-white/70 px-4 py-3 text-lg text-black/70 placeholder:text-black/40 dark:border-white/70 dark:bg-zinc-900/70 dark:text-white/70 dark:placeholder:text-white/40"
                    placeholder="Type a message here..."
                    v-model="form.message"
                    @keydown.enter.exact.prevent="sendMessage"
                    @input="characterCount = $event.target.value.length"
                    :disabled="disabled"
                    :class="{ 'cursor-not-allowed': disabled }"
                ></textarea>

                <button
                    type="submit"
                    class="absolute end-0 top-0 my-1 me-1 rounded-full bg-primary-500 px-6 py-3 text-white hover:bg-primary-600 focus:bg-primary-600"
                    title="Send Message"
                    :disabled="disabled"
                    :class="{
                        'cursor-not-allowed': disabled
                    }"
                >
                    <ArrowUpIcon class="h-6 w-6" />
                </button>
            </div>
        </form>

    </div>
</template>
