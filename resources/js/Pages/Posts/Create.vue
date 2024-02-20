<script setup>
import Shell from "@/Components/Shell.vue";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ListItem from "@/Components/ListItem.vue";
import TextArea from "@/Components/TextArea.vue";
import ImageUpload from "@/Components/ImageUpload.vue";
import MarkdownEditor from "@/Components/MarkdownEditor.vue";

const props = defineProps({
    csrf_token: String
});

const form = useForm({
    title: '',
    body: '',
    images: [],
});

const createPost = () => {
    return form.post(route('posts.store'));
}

</script>

<template>
    <Shell title="Create Post">
        <list-item class="my-5">
            <h1 class="text-2xl font-bold">Create a Post</h1>
            <form @submit.prevent="createPost" class="mt-4">
                <div class="my-2">
                    <InputLabel for="title" class="sr-only">Title</InputLabel>
                    <TextInput id="title" class="w-full" v-model="form.title" placeholder="Here goes the title..."/>
                    <InputError :message="form.errors.title" class="mt-1"/>
                </div>
                <div>
                    <InputLabel for="body" class="sr-only">Body</InputLabel>
                    <MarkdownEditor v-model="form.body"/>
                    <TextArea id="body" class="w-full rounded" v-model="form.body" rows="11"/>
                    <InputError :message="form.errors.body" class="mt-1"/>
                </div>
                <div class="my-2">
                    <ImageUpload :images="form.images" :csrf_token="props.csrf_token"/>
                </div>
                <div class="flex justify-end mt-4">
                    <PrimaryButton type="submit">Create Post</PrimaryButton>
                </div>
            </form>
        </list-item>
    </Shell>
</template>
