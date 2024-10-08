<script setup>

const props = defineProps({
    message: {
        type: Object,
        required: true
    },
});

</script>

<template>
    <div
        class="flex flex-col items-start gap-6 overflow-hidden my-8 md:row-span-3"
    >

        <div
            class="w-full max-w-[88%]"
            :class="{
                'self-end': message.sender === 'user'
            }"
        >
            <div
                class="w-full text-lg rounded-lg border border-black/20 bg-white px-4 py-4 dark:border-white/10 dark:bg-zinc-900 text-black/70 dark:text-white/70"
            >
                <span v-html="message.body" />
                <span
                    v-if="message.typing"
                    class="inline-flex ms-1 relative h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-600 opacity-75"></span>
                </span>
            </div>
        </div>
    </div>
</template>
