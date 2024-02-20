<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class=" inline-flex items-center pl-2 size-0.5 text-white bg-midnight-50 hover:bg-midnight-100 focus:outline-none font-medium rounded text-sm px-5 py-2.5 text-center me-2 mb-2">
        <v-icon name="fa-edit" scale="0.6"/>
    </button>
</template>
