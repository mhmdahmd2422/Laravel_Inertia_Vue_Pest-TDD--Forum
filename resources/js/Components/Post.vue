<script setup>

import {relativeDate} from "@/Utilities/date.js";
import ListItem from "@/Components/ListItem.vue";
const props = defineProps({
    post: Object,
})
</script>

<template>
    <list-item>
        <div class="sm:flex">
            <div>
                <div class="flex row">
                    <img :src="post.user.profile_photo_url" alt="User avatar" class="h-16 w-16 text-gray-300 rounded-full">
                    <h4 class="grow pt-4 ml-4 text-lg font-bold">{{post.user.name}}</h4>
                    <span class="text-sm text-gray-500">Posted {{relativeDate(post.created_at)}} ago</span>
                </div>
                <p class="mt-5 text-2xl font-bold">{{post.title}}</p>
                <p class="mt-4">{{post.body}}</p>
            </div>
        </div>
    </list-item>
</template>
