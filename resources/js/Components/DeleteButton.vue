<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class=" inline-flex items-center pl-2 size-0.5 text-white bg-red-500 hover:bg-red-800 focus:outline-none font-medium rounded text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-500">
        <v-icon name="fa-trash" scale="0.6"/>
    </button>
</template>
