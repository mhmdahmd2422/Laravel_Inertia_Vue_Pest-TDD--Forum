<script setup>

import {relativeDate} from "../Utilities/date.js";
import ListItem from "@/Components/ListItem.vue";

defineProps({
    comment: Object,
});

</script>

<template>
    <ListItem>
        <div class="sm:flex flex-wrap">
            <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4 flex items-center">
                <img :src="comment.user.profile_photo_url" class="h-10 w-10 rounded-full" />
            </div>
            <div class="grow">
                <h4 class="first-letter:uppercase text-lg font-bold">{{ comment.user.name }}</h4>
                <p class="mt-1">{{ comment.body }}</p>
            </div>
            <div>
                <span v-if="relativeDate" class="text-sm text-gray-500">{{ relativeDate(comment.created_at) }} ago</span>
            </div>
            <div class="flex ml-7 px-6">
                <div v-if="comment.images.length" class="grid grid-cols-6 gap-2 justify-evenly mt-4">
                    <div v-for="(image, index) in comment.images" :key="index">
                        <img :src="'/storage/images/comments/' + image.name" class="h-40 w-40 rounded">
                    </div>
                </div>
            </div>
        </div>
    </ListItem>
</template>
