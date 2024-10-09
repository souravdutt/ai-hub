<script setup>
import ChatWindow from '@/Components/ChatWindow.vue';
import ChatLayout from '@/Layouts/ChatLayout.vue';
import { ref, reactive } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import DOMPurify from 'dompurify'

const chatWindow = ref(null);
const errors = ref(null);
const messages = ref([]);
const disabled = ref(false);
const characterCount = ref(0);
const hintMessages = reactive([
    'A cat lounges on the sand, eyes dreamy, tail swishing, with a frosty drink in hand and a straw in its mouth.',
    'A bear sits by a river, nose buried in a large book, wearing reading glasses perched on the end of its nose.',
    'A young woman works intently on her laptop, fingers flying, with coffee and books beside her.',
]);

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

        // reset form
        form.reset();
        characterCount.value = 0;

        setTimeout(() => { // slight delay to wait for the messages to be rendered
            scrollToBottom(chatWindow.value.$el);
        }, 50);

        await fetch(route('text-to-image'), {
                method: 'POST',
                headers: {
                    'Accept': 'image/png', // Expecting a text/event-stream response
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

                // Return the response as a Blob (binary data for the image)
                return response.blob();
            })
            .then(blob => {
                responseMessage.typing = false;
                disabled.value = false;

                // render image from response to responseMessage.body
                const imageUrl = URL.createObjectURL(blob);
                responseMessage.body = `<a href="${imageUrl}" target="_blank"><img src="${imageUrl}" class="w-full max-w-[320px] h-auto rounded-lg" alt="Generated Image" /></a>`;

                setTimeout(() => { // slight delay to wait for the messages to be rendered
                    scrollToBottom(chatWindow.value.$el);
                }, 50);
            })
            .catch(function (error) {
                console.error('Error:', error);
                errors.value = { message: 'Something went wrong, please try again.' };
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
            type="text-to-image"
            :messages="messages"
            :hint-messages="hintMessages"
            :errors="errors"
            :disabled="disabled"
            v-model:character-count="characterCount"
            v-model:form="form"
            v-model:send-message="sendMessage"
        />

    </ChatLayout>
</template>
