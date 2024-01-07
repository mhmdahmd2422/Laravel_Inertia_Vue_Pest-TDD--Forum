<script setup>

import Container from "@/Components/Container.vue";
import CommentForm from "@/Components/CommentForm.vue";
import Post from "@/Components/Post.vue";
import PaginatedComments from "@/Components/PaginatedComments.vue";
import Card from "@/Components/Card.vue";

const props = defineProps({
   post: Object,
   comments: Object,
   csrf_token: String
})
</script>

<template>
    <container :title="post.title">
        <Post :post="post"/>
        <div class="mt-10">
            <h2 class="text-xl font-semibold">Comments</h2>
            <CommentForm :post="post" :csrf_token="props.csrf_token"/>
            <PaginatedComments :comments="props.comments"/>
        </div>
    </container>
</template>
