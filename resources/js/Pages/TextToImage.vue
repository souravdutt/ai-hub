<script setup>
import ChatWindow from '@/Components/ChatWindow.vue';
import ChatLayout from '@/Layouts/ChatLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

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

const sendMessage = async () => {

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
