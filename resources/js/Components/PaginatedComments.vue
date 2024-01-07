<script setup>

import Pagination from "@/Components/Pagination.vue";
import Comment from "@/Components/Comment.vue";
import Card from "@/Components/Card.vue";

defineProps({
    comments: Object,
})
</script>

<template>
    <Card class="mt-4 rounded-t-lg bg-gray-50">
        <ul class="divide-y">
            <li v-for="comment in comments.data"
                :key="comment.id"
                class="px-2 py-4"
            >
                <Comment :comment="comment" :key="comment.id" ></Comment>
            </li>
        </ul>
        <Pagination :meta="comments.meta" :links="comments.links" :only="['comments']"/>
    </Card>
</template>

<style scoped>

</style>
