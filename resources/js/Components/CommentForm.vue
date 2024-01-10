<script setup>

import {ref} from "vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import ImageUpload from "@/Components/ImageUpload.vue";
import ListItem from "@/Components/ListItem.vue";

const props = defineProps({
    post: Object,
    csrf_token: String
})

const commentForm = useForm({
    body: '',
    images: [],
});

let uploadComponent = ref(0);

const addComment = () => {
    return commentForm.post(route('posts.comments.store', props.post.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                commentForm.reset();
                uploadComponent +=1;
                router.get(usePage().props['comments'].meta.path, {}, {
                    preserveScroll: true,
                    replace: true,
                });
            },
        },
    );
};
</script>

<template>
    <list-item class="my-8">
        <form v-if="$page.props.auth.user" @submit.prevent="addComment"  class="mt-4">
            <div>
                <InputLabel for="body" class="sr-only">Comment</InputLabel>
                <TextArea v-model="commentForm.body" id="body" placeholder="Tell Us Something..." rows="3"/>
                <ImageUpload :images="commentForm.images" :csrf_token="props.csrf_token" :key="uploadComponent"/>
                <InputError :message="commentForm.errors.body" class="mt-1 font-bold"></InputError>
            </div>

            <PrimaryButton type="submit" class="mt-3" :disabled="commentForm.processing">Add Comment</PrimaryButton>
        </form>
    </list-item>
</template>

