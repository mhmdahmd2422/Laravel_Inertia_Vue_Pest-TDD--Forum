<script setup>

import {relativeDate} from "@/Utilities/date.js";
import ListItem from "@/Components/ListItem.vue";
const props = defineProps({
    post: Object,
})
</script>

<template>
    <list-item class="hover:bg-white">
        <div class="sm:flex flex-wrap mb-2">
            <div>
                <div class="flex row">
                    <img :src="post.user.profile_photo_url" alt="User avatar" class="h-16 w-16 text-gray-300 rounded-full">
                    <h4 class="grow pt-4 ml-4 text-lg font-bold">{{post.user.name}}</h4>
                    <span class="text-sm text-gray-500">Posted {{relativeDate(post.created_at)}} ago</span>
                </div>
                <p class="mt-5 text-2xl font-bold">{{post.title}}</p>
                <p class="mt-4">{{post.body}}</p>
            </div>
            <div v-if="post.images?.length" class="flex px-3 mt-3">
                <div class="grid grid-cols-6 gap-2 justify-evenly mt-4">
                    <div v-for="(image, index) in post.images" :key="index">
                        <img v-if="image.id" :src="'/storage/images/posts/' + image.name" class="h-40 w-40 rounded">
                    </div>
                </div>
            </div>
        </div>
    </list-item>
</template>
