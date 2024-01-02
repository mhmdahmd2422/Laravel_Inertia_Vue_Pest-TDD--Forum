<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";

defineProps({
    posts: Object,
})
</script>

<template>
    <AppLayout>
        <Container>
            <template #header>Posts</template>
            <ul class="divide-y">
                <li v-for="post in posts" :key="post.id" class="px-2 py-4">
                    <span class="font-semibold">
                        {{post.title}}
                    </span>
                </li>
            </ul>
        </Container>
    </AppLayout>
</template>
