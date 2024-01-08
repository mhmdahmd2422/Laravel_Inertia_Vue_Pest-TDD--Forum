<script setup>
import {ref} from "vue";
import Container from "@/Components/Container.vue";
import CommentForm from "@/Components/CommentForm.vue";
import Post from "@/Components/Post.vue";
import Card from "@/Components/Card.vue";
import Comment from "@/Components/Comment.vue";
import {useInfiniteScroll} from "@/Composables/useInfiniteScroll.js";

const props = defineProps({
   post: Object,
   comments: Object,
   csrf_token: String
});

const loader = ref(null);
const {items, loadMoreItems} = useInfiniteScroll('comments', loader);
</script>

<template>
    <container :title="post.title">
        <Post :post="post"/>
        <div class="mt-10">
            <h2 class="text-xl font-semibold">Comments</h2>
            <CommentForm :post="post" :csrf_token="props.csrf_token"/>
            <Card class="mt-4 rounded-t-xl bg-gray-50">
                <Comment v-for="comment in items" :comment="comment" :key="comment.id" ></Comment>
                <div ref="loader"></div>
            </Card>
        </div>
    </container>
</template>
