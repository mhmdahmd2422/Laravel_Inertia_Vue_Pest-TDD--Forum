<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import ImageUpload from "@/Components/ImageUpload.vue";
import ListItem from "@/Components/ListItem.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {ref} from "vue";

const props = defineProps({
    post: Object,
    commentForm : Object,
    inEditMode: Boolean,
    csrf_token: String
})


const emit = defineEmits(['add', 'update', 'cancelEdit', 'deleteImage']);
const commentTextAreaRef = ref(null);

defineExpose({
    commentTextAreaRef,
});
</script>

<template>
    <list-item class="my-8">
        <form v-if="$page.props.auth.user" @submit.prevent="() => inEditMode ? $emit('update') : $emit('add')"  class="mt-4">
            <div>
                <InputLabel for="body" class="sr-only">Comment</InputLabel>
                <TextArea ref="commentTextAreaRef" v-model="commentForm.body" id="body" placeholder="Tell Us Something..." rows="3"/>
                <div v-if="commentForm.images?.length" class="flex px-6">
                    <div class="grid grid-cols-6 gap-2 justify-evenly my-4 relative">
                        <div v-for="(image, index) in commentForm.images" :key="index">
                            <img v-if="image.id" :src="'/storage/images/comments/' + image.name" class="h-40 w-40 rounded">
                            <button v-if="image.id" @click.prevent="$emit('deleteImage', index)" class="text-red-100 absolute top-0.5"><v-icon class="text-red-500" name="fa-minus-circle"/></button>
                        </div>
                    </div>
                </div>
                <ImageUpload :images="commentForm.images" :csrf_token="props.csrf_token"/>
                <InputError :message="commentForm.errors.body" class="mt-1 font-bold"></InputError>
            </div>
            <PrimaryButton type="submit" class="mt-3" :disabled="commentForm.processing" v-text="inEditMode ? 'Update Comment' : 'Add Comment'"></PrimaryButton>
            <v-icon v-if="commentForm.processing" class="ml-3" name="fa-spinner" scale="1.2" animation="spin"/>
            <SecondaryButton v-if="inEditMode" @click="$emit('cancelEdit')" class="ml-2">Cancel</SecondaryButton>
        </form>
    </list-item>
</template>

