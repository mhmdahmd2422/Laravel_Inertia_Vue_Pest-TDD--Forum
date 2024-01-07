<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {router, useForm} from "@inertiajs/vue3";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import vueFilePond from 'vue-filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
// import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';
import 'filepond/dist/filepond.min.css';
import Post from "@/Components/Post.vue";
// import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';


let FilePond = vueFilePond(FilePondPluginFileValidateType);

const props = defineProps({
    post: Object,
    csrf_token: String
})

const commentForm = useForm({
    body: '',
    images: [],
});

const addComment = () => {
    return commentForm.post(route('posts.comments.store', props.post.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                commentForm.reset();
                FilePond = vueFilePond(FilePondPluginFileValidateType);
            },
        },
    );
};

function handleFilePondLoad(response) {
    commentForm.images.push(response);
    return response;
}
const handleFilePondRevert = (uniqueId, load, error) => {
    uniqueId = uniqueId.substring(0, uniqueId.indexOf("<"));
    commentForm.images = commentForm.images.filter((image) => image !== uniqueId);
    router.delete('/revert/' + uniqueId);
    load();
}
</script>

<template>
    <form v-if="$page.props.auth.user" @submit.prevent="addComment"  class="mt-4">
        <div>
            <InputLabel for="body" class="sr-only">Comment</InputLabel>
            <TextArea v-model="commentForm.body" id="body" placeholder="Tell Us Something..." rows="4"/>
            <file-pond
                name="image"
                ref="pond"
                class-name="my-pond"
                label-idle="Click to choose image, or drag here..."
                accepted-file-types= 'image/*'
                allow-multiple="true"
                :server="{
                                url: '',
                                process: {
                                    url: '/upload',
                                    method: 'POST',
                                    onload: handleFilePondLoad
                                },
                                revert: handleFilePondRevert,
                                headers: {
                                    'X-CSRF-TOKEN': props.csrf_token
                                }
                            }"
            />
            <InputError :message="commentForm.errors.body" class="mt-1 font-bold"></InputError>
        </div>

        <PrimaryButton type="submit" class="mt-3" :disabled="commentForm.processing">Add Comment</PrimaryButton>
    </form>
</template>

