<script setup>
import ChatWindow from '@/Components/ChatWindow.vue';
import ChatLayout from '@/Layouts/ChatLayout.vue';
import { onMounted, ref } from 'vue';
import { marked } from 'marked'
import DOMPurify from 'dompurify'
import { useForm } from '@inertiajs/vue3';

const chatWindowRef = ref(null);
const errors = ref(null);
const messages = ref([]);
const disabled = ref(false);
const characterCount = ref(0);

// form
const form = useForm({
    message: '',
});

// methods
const scrollToBottom = () => {
    if (chatWindowRef.value) {
        chatWindowRef.value.scrollTop = chatWindowRef.value.scrollHeight;
    }
};

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

onMounted(() => {
    console.log(chatWindowRef.value.scrollHeight);
})

</script>

<template>
    <Head title="Welcome" />

    <ChatLayout>

        <ChatWindow
            ref="chatWindowRef"
            :messages="messages"
            :errors="errors"
            :disabled="disabled"
            v-model:character-count="characterCount"
            v-model:form="form"
            v-model:send-message="sendMessage"
        />

    </ChatLayout>
</template>
