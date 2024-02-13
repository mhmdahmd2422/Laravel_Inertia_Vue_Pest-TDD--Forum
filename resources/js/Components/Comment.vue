<script setup>

import {relativeDate} from "../Utilities/date.js";
import ListItem from "@/Components/ListItem.vue";
import DeleteButton from "@/Components/DeleteButton.vue";
import EditButton from "@/Components/EditButton.vue";
import {computed} from "vue";

const props = defineProps({
    comment: Object,
});
const commentWithImages = computed(() => Object.values(props.comment.images).filter(image => image.id));

const emit = defineEmits(['delete', 'edit']);
</script>

<template>
    <ListItem>
        <div class="sm:flex flex-wrap">
            <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4 self-start">
                <img :src="comment.user.profile_photo_url" class="h-10 w-10 rounded-full" />
            </div>
            <div class="grow">
                <h4 class="first-letter:uppercase text-lg font-bold">{{ comment.user.name }}</h4>
                <p class="mt-1 max-w-4xl break-words">{{ comment.body }}</p>
            </div>
            <div class="grid grid-rows-1">
                <span v-if="relativeDate" class="text-sm text-gray-500">{{ relativeDate(comment.created_at) }} ago</span>
                <div class="flex justify-end mt-2">
                    <form v-if="comment.can?.delete" @submit.prevent="$emit('delete', comment.id)">
                        <DeleteButton  type="submit"></DeleteButton>
                    </form>
                    <form v-if="comment.can?.update" @submit.prevent="$emit('edit', comment.id)">
                        <EditButton type="submit"></EditButton>
                    </form>
                </div>
            </div>
            <div v-if="comment.images?.length" class="flex ml-7 px-6">
                <div class="grid grid-cols-6 gap-2 justify-evenly mt-4">
                    <div v-for="(image, index) in commentWithImages" :key="index">
                        <img v-if="image.id" :src="'/storage/images/comments/' + image.name" class="h-40 w-40 rounded">
                    </div>
                </div>
            </div>
        </div>
    </ListItem>
</template>
