<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";

defineProps({
    posts: Object,
})
</script>

<template>
    <AppLayout>
        <Container>
            <template #header>Posts</template>
            <ul class="divide-y">
                <li v-for="post in posts.data" :key="post.id" class="px-2 py-4">
                    <span class="font-semibold">
                        {{post.title}}
                    </span>
                </li>
            </ul>
            <Pagination :links="posts.links" :meta="posts.meta" ></Pagination>
        </Container>
    </AppLayout>
</template>
