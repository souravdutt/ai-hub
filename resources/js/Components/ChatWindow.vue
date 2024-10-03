<script setup>
import ChatMessage from '@/Components/ChatMessage.vue';
import { ArrowUpIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { marked } from 'marked'
import DOMPurify from 'dompurify'

const chatWindow = ref(null);
const errors = ref(null);
const messages = ref([]);
const disabled = ref(false);
const characterCount = ref(0);


// form
const form = useForm({
    message: '',
});

const scrollToBottom = () => {
    if (chatWindow.value) {
        chatWindow.value.scrollTop = chatWindow.value.scrollHeight;
    }
};

// methods
const sendMessage = async () => {
    if (form.message?.trim()) {
        disabled.value = true;
        errors.value = null;

        const sentMessage = DOMPurify.sanitize(form.message.replace(/\n/g, '<br>'));

        // add user message to messages
        messages.value.push({
            id: messages.value.length + 1,
            body: sentMessage,
            sender: 'user',
        });

        // add server message to messages
        messages.value.push({
            id: messages.value.length + 1,
            body: '',
            sender: 'server',
            typing: true,
        });

        const responseMessage = messages.value[messages.value.length - 1];

        scrollToBottom();

        await fetch(route('chat'), {
                method: 'POST',
                headers: {
                    'Accept': 'text/event-stream', // Expecting a text/event-stream response
                    'Content-Type': 'application/json', // Sending JSON data
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF Token for Laravel security
                },
                body: JSON.stringify(form.data()), // Send form data directly as JSON
            })
            .then(function (response) {
                if (!response.ok) {
                    responseMessage.typing = false;
                    disabled.value = false;

                    if (response.status === 422) {
                        return response.json().then(function (errorResponse) {
                            errors.value = errorResponse;
                            errors.value.message = errorResponse.message[0];
                            return Promise.reject(errors);
                        });
                    } else {
                        return response.text().then(function (text) {
                            console.error('Error:', text);
                            return Promise.reject(errors);
                        });
                    }
                }

                return response.body;
            })
            .then(response => {
                const reader = response.getReader();
                const decoder = new TextDecoder("utf-8");

                let scrolledByUser = false;
                let prevLastLine = "";

                // Detect user scroll in the chatWindow
                chatWindow.value.addEventListener("wheel", () => { scrolledByUser = true; });
                chatWindow.value.addEventListener("mousewheel", () => { scrolledByUser = true; });
                chatWindow.value.addEventListener("DOMMouseScroll", () => { scrolledByUser = true; });

                const readStream = function() {
                    return reader.read().then(({ done, value }) => {
                        if (done) {
                            // console.log('Stream finished.');

                            // Convert markdown to HTML
                            let html = DOMPurify.sanitize(marked.parse(responseMessage.body));
                            responseMessage.body = html;

                            if (!scrolledByUser) {
                                scrollToBottom();
                            }

                            responseMessage.typing = false;
                            disabled.value = false;

                            return;
                        }

                        const chunk = decoder.decode(value, { stream: true });
                        const lines = chunk.split("\n\n");

                        let firstLine = lines[0];
                        let lastLine = lines[lines.length - 1];

                        if (!firstLine.startsWith("data: ")) {
                            lines[0] = prevLastLine + firstLine;
                        }

                        try {
                            JSON.parse(lastLine.replace("data: ", ""));
                            prevLastLine = '';
                        } catch (error) {
                            prevLastLine = lastLine;
                            lines.pop();
                        }

                        lines.forEach(line => {
                            if (line.startsWith("data: ")) {
                                let buffer = line.replace("data: ", "");

                                if (buffer === "[DONE]") {
                                    console.log("Stream completed");
                                } else {
                                    try {
                                        const parsedData = JSON.parse(buffer);
                                        if (parsedData?.response) {
                                            responseMessage.body += parsedData.response;
                                        }
                                    } catch (error) {
                                        console.error("Error parsing chunk:", error);
                                    }

                                    document.querySelector(".conversation .loading-response")?.remove();

                                    if (!scrolledByUser) {
                                        scrollToBottom();
                                    }
                                }
                            }
                        });

                        return readStream();
                    });
                }

                readStream();
            })
            .catch(function (error) {
                console.error('Error:', error);
                responseMessage.typing = false;
                disabled.value = false;
            })
            .finally(() => {
                // reset form
                form.reset();
                characterCount.value = 0;
            })
    }
};
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
                    @input="characterCount = form.message.trim().length"
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
