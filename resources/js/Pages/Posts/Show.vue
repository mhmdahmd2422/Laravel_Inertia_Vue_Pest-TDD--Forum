<script setup>
import {ref} from "vue";
import CommentForm from "@/Components/CommentForm.vue";
import Post from "@/Components/Post.vue";
import Comment from "@/Components/Comment.vue";
import {useInfiniteScroll} from "@/Composables/useInfiniteScroll.js";
import Shell from "@/Components/Shell.vue";

const props = defineProps({
   post: Object,
   comments: Object,
   csrf_token: String
});

const loader = ref(null);
const {items, loadMoreItems} = useInfiniteScroll('comments', loader);
</script>

<template>
    <Shell :title="post.title">
        <Post :post="post"/>
        <div class="mt-5">
            <CommentForm v-if="$page.props.auth.user" :post="post" :csrf_token="props.csrf_token"/>
            <p class="mt-11 text-2xl font-bold">Comments</p>
            <Comment v-for="comment in items" :comment="comment" :key="comment.id" ></Comment>
            <div ref="loader"></div>
        </div>
    </Shell>
</template>
