<script setup>

import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";
import {relativeDate} from "../../Utilities/date.js";

defineProps({
    posts: Object,
});

</script>

<template>
    <Container title="Posts">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Posts
            </h2>
        </template>
        <ul class="divide-y">
            <li v-for="post in posts.data" :key="post.id" class="">
                <Link :href="route('posts.show', post.id)" class="block group px-2 py-4">
                    <span class="font-semibold group-hover:text-indigo-600">
                        {{post.title}}
                    </span>
                    <span class="block mt-1 text-sm text-gray-600"
                    >{{relativeDate(post.created_at)}} ago by {{post.user.name}}
                    </span>
                </Link>
            </li>
        </ul>
        <Pagination :links="posts.links" :meta="posts.meta" ></Pagination>
    </Container>
</template>
