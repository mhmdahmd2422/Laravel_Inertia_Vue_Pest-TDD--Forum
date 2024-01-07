<script setup>

import {relativeDate} from "../Utilities/date.js";

defineProps({
    comment: Object,
})

</script>

<template>
    <div class="sm:flex">
        <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
            <img :src="comment.user.profile_photo_url" class="h-10 w-10 rounded-full" />
        </div>
        <div>
            <p class="mt-1 break-all">{{ comment.body }}</p>
            <div v-if="comment.images.length" class="grid grid-cols-6 gap-2 justify-evenly mt-4">
                <div v-for="(image, index) in comment.images" :key="index">
                    <img :src="'/storage/images/comments/' + image.name" class="h-40 w-40 rounded">
                </div>
            </div>
            <span class="first-letter:uppercase block pt-1 text-xs text-gray-600">By {{ comment.user.name }} {{ relativeDate(comment.created_at) }} ago</span>
        </div>
    </div>
</template>
