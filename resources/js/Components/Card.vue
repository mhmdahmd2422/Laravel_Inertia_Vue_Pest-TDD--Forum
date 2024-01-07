<script setup>

</script>

<template>
    <div class="border border-gray-400 rounded-xl rounded-t-none">
        <div v-if="$slots.header" class="bg-gray-300 p-2">
            <slot name="header"/>
        </div>
        <div class="px-4 pb-4">
            <slot/>
        </div>
    </div>
</template>

<style scoped>

</style>
