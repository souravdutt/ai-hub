<script setup>
import ChatWindow from '@/Components/ChatWindow.vue';
import ChatLayout from '@/Layouts/ChatLayout.vue';
import { onMounted, ref } from 'vue';
import { Marked } from 'marked'
import DOMPurify from 'dompurify'
import { useForm } from '@inertiajs/vue3';
import { markedHighlight } from "marked-highlight";
import hljs from 'highlight.js';

import 'highlight.js/styles/atom-one-dark.min.css';

const marked = new Marked(
    markedHighlight({
        langPrefix: 'hljs language-',
        highlight(code, lang, info) {
            const language = hljs.getLanguage(lang) ? lang : 'plaintext';
            return hljs.highlight(code, { language }).value;
        }
    })
);

const chatWindow = ref(null);
const errors = ref(null);
const messages = ref([]);
const disabled = ref(false);
const characterCount = ref(0);

// form
const form = useForm({
    message: '',
});

// methods
const scrollToBottom = (container) => {
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
};

const sendMessage = async (e, message = null) => {
    if (message !== null) {
        form.message = message;
    }

    const sentMessage = DOMPurify.sanitize(form.message.replace(/\n/g, '<br>'));

    if (sentMessage?.trim()) {
        const history = messages.value.map((message) => {
            return {
                sender: message.sender,
                message: message.body,
            }
        });

        disabled.value = true;
        errors.value = null;

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

        const formData = form.data();
        formData.history = history;

        // reset form
        form.reset();
        characterCount.value = 0;

        setTimeout(() => { // slight delay to wait for the messages to be rendered
            scrollToBottom(chatWindow.value.$el);
        }, 50);

        await fetch(route('chat'), {
                method: 'POST',
                headers: {
                    'Accept': 'text/event-stream', // Expecting a text/event-stream response
                    'Content-Type': 'application/json', // Sending JSON data
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF Token for Laravel security
                },
                body: JSON.stringify(formData), // Send form data directly as JSON
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
                            errors.value = { message: 'Something went wrong, please try again.', text: text }
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
                let prevLastLine, responseText = "";

                // Detect user scroll in the chatWindow
                chatWindow.value.$el.addEventListener("wheel", () => { scrolledByUser = true; });
                chatWindow.value.$el.addEventListener("mousewheel", () => { scrolledByUser = true; });
                chatWindow.value.$el.addEventListener("DOMMouseScroll", () => { scrolledByUser = true; });

                const readStream = function() {
                    return reader.read().then(({ done, value }) => {
                        if (done) {
                            // console.log('Stream finished.');

                            // Convert markdown to HTML
                            let html = DOMPurify.sanitize(marked.parse(responseMessage.body));
                            responseMessage.body = html;

                            if (!scrolledByUser) {
                                setTimeout(() => { // slight delay to wait for the html to be rendered
                                    scrollToBottom(chatWindow.value.$el);
                                }, 50);
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
                                            responseText += parsedData.response;
                                            responseMessage.body = marked.parse(responseText);
                                        }
                                    } catch (error) {
                                        console.error("Error parsing chunk:", error);
                                    }

                                    document.querySelector(".conversation .loading-response")?.remove();

                                    if (!scrolledByUser) {
                                        scrollToBottom(chatWindow.value.$el);
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
    }
};

</script>

<template>
    <Head title="Welcome" />

    <ChatLayout>

        <ChatWindow
            ref="chatWindow"
            :messages="messages"
            :errors="errors"
            :disabled="disabled"
            v-model:character-count="characterCount"
            v-model:form="form"
            v-model:send-message="sendMessage"
        />

    </ChatLayout>
</template>
