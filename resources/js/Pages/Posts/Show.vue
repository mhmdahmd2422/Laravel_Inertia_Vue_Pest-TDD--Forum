<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";
import {relativeDate} from "../../Utilities/date.js";
import Comment from "@/Components/Comment.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {router, useForm} from "@inertiajs/vue3";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
// Import FilePond
import vueFilePond from 'vue-filepond';

// Import plugins
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
// import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';

// Import styles
import 'filepond/dist/filepond.min.css';
// import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType);


const props = defineProps({
   post: Object,
   comments: Object,
    csrf_token: String
})

const commentForm = useForm({
   body: '',
   images: [],
});

const addComment = () => {
    console.log(commentForm);
    return commentForm.post(route('posts.comments.store', props.post.id),
        {
            preserveScroll: true,
            onSuccess: () => commentForm.reset(),
        },
    );
};

const handleFilePondInit = () => {
    console.log('filepond in ready');
    // console.log('filePond object:', this.$refs.pond);
};

function handleFilePondLoad(response) {
    commentForm.images.push(response);
    console.log(commentForm.images);
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
    <AppLayout :title="post.title">
        <container>
            <h1 class="text-2xl font-extrabold">{{post.title}}</h1>
            <span class="block mt-1 text-sm text-gray-600">{{relativeDate(post.created_at)}} ago by {{post.user.name}}</span>
            <article class="mt-6">
                <pre class="whitespace-pre-wrap font-sans">{{post.body}}</pre>
            </article>

            <div class="mt-12">
                <h2 class="text-xl font-semibold">Comments</h2>

                <form v-if="$page.props.auth.user" @submit.prevent="addComment"  class="mt-4">
                    <div>
                        <InputLabel for="body" class="sr-only">Comment</InputLabel>
                        <TextArea v-model="commentForm.body" id="body" placeholder="Tell Us Something..." rows="4"/>
                        <file-pond
                            name="image"
                            ref="pond"
                            class-name="my-pond"
                            label-idle="Click to choose image, or drag here..."
                            accepted-file-types="image/jpeg, image/png"
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

                <ul class="divide-y  mt-4">
                    <li v-for="comment in comments.data"
                        :key="comment.id"
                        class="px-2 py-4"
                    >
                        <Comment :comment="comment" :key="comment.id" ></Comment>
                    </li>
                </ul>
                <Pagination :meta="comments.meta" :links="comments.links" :only="['comments']"/>
            </div>
        </container>
    </AppLayout>
</template>
