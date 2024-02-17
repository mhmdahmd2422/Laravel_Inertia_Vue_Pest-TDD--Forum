<script setup>
import {computed, ref} from "vue";
import CommentForm from "@/Components/CommentForm.vue";
import Post from "@/Components/Post.vue";
import Comment from "@/Components/Comment.vue";
import {useInfiniteScroll} from "@/Composables/useInfiniteScroll.js";
import Shell from "@/Components/Shell.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import {useConfirm} from "@/Composables/useConfirm.js";

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
};

let commentFormKey = ref(0);

const addComment = () => {
    return commentForm.post(route('posts.comments.store', props.post.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                commentForm.reset();
                items.value = [props.comments.data[0] ,...items.value];
                commentFormKey.value += 1;
            },
        },
    );
};

const { confirmation } = useConfirm();

const updateComment = async () => {
    if (!await confirmation('Are you sure you want to update this comment?')) {
        editArea.value.commentTextAreaRef?.focus();
        return;
    }

    return commentForm.put(route('comments.update', {
            comment: commentIdBeingEdited.value,
            page: props.comments.meta.current_page
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                window.history.replaceState({}, '', props.comments.meta.path);
                for (const [commentkey, comment] of Object.entries(items.value)) {
                    if (comment.id === commentIdBeingEdited.value) {
                        items.value[commentkey].body = commentForm.body;
                        if (commentForm.images?.length) {
                            let counter = 0;
                            for (const [Key, element] of Object.entries(commentForm.images)) {
                                console.log(element);
                                if (!element.id) {
                                    items.value[commentkey].images.push({
                                        "id": usePage().props.flash?.info[counter],
                                        "name": element
                                    });
                                    counter++;
                                }
                            }
                        }
                    }
                }
                commentForm.reset();
                cancelEditComment();
                commentFormKey.value += 1;
            },
        },
    );
};

const deleteComment = async (commentId) => {
    if (! await confirmation('Are you sure you want to delete this comment?')){
        return;
    }

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
            <p class="mt-5 text-2xl font-bold">Comments</p>
            <CommentForm ref="editArea"
                         @deleteImage="deleteImage"
                         @add="addComment"
                         @update="updateComment"
                         @cancelEdit="cancelEditComment"
                         v-if="$page.props.auth.user"
                         :post="post"
                         :comment-form="commentForm"
                         :inEditMode="!!commentIdBeingEdited"
                         :csrf_token="props.csrf_token"
                         :key="commentFormKey"
            />
            <Comment @edit="editComment"
                     @delete="deleteComment"
                     v-for="comment in items"
                     :comment="comment"
                     :key="comment.id"
            ></Comment>
            <div ref="loader"></div>
        </div>
    </Shell>
</template>
