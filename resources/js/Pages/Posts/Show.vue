<script setup>
import {ref} from "vue";
import CommentForm from "@/Components/CommentForm.vue";
import Post from "@/Components/Post.vue";
import Comment from "@/Components/Comment.vue";
import {useInfiniteScroll} from "@/Composables/useInfiniteScroll.js";
import Shell from "@/Components/Shell.vue";
import {router, usePage} from "@inertiajs/vue3";

const props = defineProps({
   post: Object,
   comments: Object,
   csrf_token: String
});

const loader = ref(null);
const {items, loadMoreItems} = useInfiniteScroll('comments', loader);

const addComment = (commentForm) => {
    return commentForm.post(route('posts.comments.store', props.post.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                commentForm.reset();
                router.get(usePage().props['comments'].meta.path, {}, {
                    preserveScroll: true,
                    replace: true,
                });
            },
        },
    );
};

const deleteComment = (commentId) => {
    router.delete(route('comments.destroy', { comment: commentId, page: props.comments.meta.current_page}), {
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            window.history.replaceState({}, '', props.comments.meta.path);
            for (const [key, comment] of Object.entries(items.value)) {
                if (comment.id === commentId)  {
                    items.value.splice(key, 1);
                }
            }
        },
    });
};
</script>

<template>
    <Shell :title="post.title">
        <Post :post="post"/>
        <div class="mt-5">
            <CommentForm @add="addComment" v-if="$page.props.auth.user" :post="post" :csrf_token="props.csrf_token"/>
            <p v-if="items.length" class="mt-11 text-2xl font-bold">Comments</p>
            <Comment @delete="deleteComment" v-for="comment in items" :comment="comment" :key="comment.id" ></Comment>
            <div ref="loader"></div>
        </div>
    </Shell>
</template>
