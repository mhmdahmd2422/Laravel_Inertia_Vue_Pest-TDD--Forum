<script setup>

import Pagination from "@/Components/Pagination.vue";
import Shell from "@/Components/Shell.vue";
import Grid from "@/Components/Grid.vue";
import PostExcerpt from "@/Components/PostExcerpt.vue";
import {ref, watch} from "vue";
import {debounce} from "lodash";
import {router, usePage} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    posts: Object,
    links: Object,
    filters: Object,
});

let search = ref(props.filters.search);
watch(search, debounce(function(value){
        router.get(
            route('posts.index'),
            {search: value},
            {
                preserveState: true,
                replace: true,
            });
    }, 300)
);
</script>

<template>
    <Shell title="Posts">
        <template #header>
            <h2 class="text-3xl font-bold tracking-tight text-white">
                Posts
            </h2>
        </template>
        <SecondaryButton v-show="usePage().props.permissions.create_posts" @click="router.get(route('posts.create'))" class="h-23 pl-3">
            <v-icon name="fa-plus" class="mr-2"/>New Post</SecondaryButton>
        <template #search>
            <input v-model="search"
                   type="text"
                   placeholder="Search"
                   class="h-12 pl-4 w-56 rounded-full text-black placeholder-grey-800"
            >
        </template>
        <div v-if="!posts.data.length" class="grid justify-items-center bg-gray-400 text-gray-600 h-16 rounded-md">
            No Posts Found!
        </div>
        <Grid>
            <PostExcerpt v-for="post in posts.data"
                   :key="post.id"
                   :routes="post.routes"
                   :title="post.title"
                   :body="post.body"
                   :posted-at="post.created_at">
            </PostExcerpt>
        </Grid>
        <Pagination
            :links="posts.links"
            :prev_page_url="posts.prev_page_url"
            :next_page_url="posts.next_page_url"
            :from="posts.from"
            :to="posts.to"
            :total="posts.total"
        ></Pagination>
    </Shell>
</template>
