<script setup>

defineProps({
    href: {
        type: String,
    },
    class: {
        type: String,
    }
})

</script>

<template>
    <Link class="text-xl font-extrabold text-primary-600"
        :href="href ?? route('welcome')"
        :class="class"
    >
    {{ $page.props.app.name ?? 'AI Hub' }}
    </Link>
</template>
