<script setup>

defineProps({
    title: String,
})
</script>
<template>
    <Head :title="title" />
    <!-- Page Heading -->
    <header v-if="$slots.header" class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot name="header" />
        </div>
    </header>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <slot/>
    </div>
</template>
