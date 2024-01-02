<template>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="grid place-content-start mb-2">
            <h2 class="font-extrabold bg-white text-xl text-gray-800 border border-gray-500 rounded p-3 leading-tight">
                <slot name="header"></slot>
            </h2>
        </div>
        <slot/>
    </div>
</template>
<script setup>

</script>
