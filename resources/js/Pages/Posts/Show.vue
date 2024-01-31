<script setup>
import {computed, ref} from "vue";
import CommentForm from "@/Components/CommentForm.vue";
import Post from "@/Components/Post.vue";
import Comment from "@/Components/Comment.vue";
import {useInfiniteScroll} from "@/Composables/useInfiniteScroll.js";
import Shell from "@/Components/Shell.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";

const props = defineProps({
   post: Object,
   comments: Object,
   csrf_token: String
});

const commentForm = useForm({
    body: '',
    images: [],
});

const loader = ref(null);
const {items, loadMoreItems} = useInfiniteScroll('comments', loader);

const commentIdBeingEdited = ref(null);
const commentBeingEdited = computed(() => items.value.find(comment => comment.id === commentIdBeingEdited.value));

const editArea = ref(null);
const editComment = (commentId) => {
    commentIdBeingEdited.value = commentId;
    console.log(commentBeingEdited.value);
    commentForm.body = commentBeingEdited.value?.body;
    commentForm.images = commentBeingEdited.value?.images;
    editArea.value.commentTextAreaRef?.focus();
}

const deleteImage = (index) => {
    return commentForm.delete(route('image.destroy', commentForm.images[index]),
        {
            preserveScroll: true,
            onSuccess: () => {
                commentForm.images.splice(index, 1);
            },
        },
    );
}

const cancelEditComment = () => {
    commentIdBeingEdited.value = null;
    commentForm.reset();
}

const addComment = () => {
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

const updateComment = () => {
    return commentForm.put(route('comments.update', { comment: commentIdBeingEdited.value, page: props.comments.meta.current_page}),
        {
            preserveScroll: true,
            onSuccess: () => {
                cancelEditComment();
                router.get(usePage().props['comments'].meta.path, {}, {
                    preserveScroll: true,
                    replace: true,
                });
            },
        },
    );
}

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
            <CommentForm ref="editArea" @deleteImage="deleteImage" @add="addComment" @update="updateComment" @cancelEdit="cancelEditComment" v-if="$page.props.auth.user" :post="post" :comment-form="commentForm" :inEditMode="!!commentIdBeingEdited" :csrf_token="props.csrf_token"/>
            <p v-if="items.length" class="mt-11 text-2xl font-bold">Comments</p>
            <Comment @edit="editComment" @delete="deleteComment" v-for="comment in items" :comment="comment" :key="comment.id"></Comment>
            <div ref="loader"></div>
        </div>
    </Shell>
</template>
