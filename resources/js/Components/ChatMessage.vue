<script setup>

const props = defineProps({
    message: {
        type: Object,
        required: true
    }
});

</script>

<template>
    <div
        class="flex flex-col items-start gap-6 overflow-hidden rounded-lg my-5 md:row-span-3"
    >

        <div
        class="w-full max-w-2xl"
        :class="{
            'self-end': message.user === 'user'
        }"
        >
            <div
                class="w-full rounded-lg border whitespace-pre-line border-black/20 bg-white px-4 py-3 text-lg text-black/70 dark:border-white/10 dark:bg-zinc-900 dark:text-white/70"
            >
                {{ message.body }}
            </div>
        </div>
    </div>
</template>
