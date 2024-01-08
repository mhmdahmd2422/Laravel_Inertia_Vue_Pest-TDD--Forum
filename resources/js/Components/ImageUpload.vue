<script setup>

import {router} from "@inertiajs/vue3";
import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import {setOptions} from "filepond";
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

let FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImagePreview
);

let props = defineProps({
   images: Array,
   csrf_token: String,
});

const handleFilePondLoad = (response) => {
    props.images.push(response);
    return response;
}
const handleFilePondRevert = (uniqueId, load, error) => {
    props.images = props.images.filter((image) => image !== uniqueId);
    router.delete('/revert/' + uniqueId, {
        preserveScroll: true,
        preserveState: true,
    });
    load();
}

let serverMessage = {};
setOptions({
    server: {
        url: '',
        process: {
            url: '/upload',
            method: 'POST',
            onload: handleFilePondLoad
        },
        onerror: (response) => {
            serverMessage = JSON.parse(response);
        },
        revert: handleFilePondRevert,
        headers: {
            'X-CSRF-TOKEN': props.csrf_token
        }
    },
    labelFileProcessingError: () => {
        return serverMessage.error;
    }
})
</script>

<template>
    <file-pond
        name="image"
        ref="pond"
        class-name="my-pond"
        label-idle="Click to choose image, or drag here..."
        accepted-file-types= 'image/jpg, image/jpeg, image/png'
        allow-multiple="true"
        max-file-size="2MB"
        allowImagePreview="false"
    />
</template>

<style scoped>

</style>
