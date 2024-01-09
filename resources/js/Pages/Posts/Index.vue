<script setup>

import Pagination from "@/Components/Pagination.vue";
import Shell from "@/Components/Shell.vue";
import Grid from "@/Components/Grid.vue";
import PostExcerpt from "@/Components/PostExcerpt.vue";

defineProps({
    posts: Object,
});

</script>

<template>
    <Shell title="Posts">
        <template #header>
            <h2 class="text-3xl font-bold tracking-tight text-white">
                Posts
            </h2>
        </template>
        <Grid class="mt-6">
            <PostExcerpt v-for="post in posts.data"
                   :key="post.id"
                   :title="post.title"
                   :body="post.body"
                   :user="post.user.name"
                   :posted-at="post.created_at">
            </PostExcerpt>
        </Grid>
        <Pagination :links="posts.links" :meta="posts.meta" ></Pagination>
    </Shell>
</template>
