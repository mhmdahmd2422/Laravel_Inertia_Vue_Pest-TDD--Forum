<script setup>

import Shell from "@/Components/Shell.vue";
import Grid from "@/Components/Grid.vue";
import PostExcerpt from "@/Components/PostExcerpt.vue";
import {ref, watch} from "vue";
import {debounce} from "lodash";
import {router, usePage} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {useInfiniteScroll} from "@/Composables/useInfiniteScroll.js";

const props = defineProps({
    posts: Object,
    filters: Object,
});

const loader = ref(null);
const {items, canLoadMoreItems} = useInfiniteScroll('posts', loader);

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

const newPost = ref(null);
const showRefreshNotification = ref(false);
const refreshIconSpin = ref('');

window.Echo.channel('posts')
    .listen('NewPostPublished', (post) => {
       newPost.value = post;
       showRefreshNotification.value = true;
    });

const refreshPosts = () => {
    refreshIconSpin.value = 'spin';
    items.value = [newPost.value.post ,...items.value];
    showRefreshNotification.value = false;
};
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
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-out"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
        <div v-if="showRefreshNotification" class="flex justify-between bg-gray-400 px-2 py-2 rounded-lg">
            <div>
                <v-icon class="text-midnight-100" name="fa-info-circle" scale="1.6"/>
            </div>
            <div class="font-bold text-lg mt-0.5">
                New posts are published!
                <button
                    type="button"
                    @click.prevent="refreshPosts"
                >
                    <v-icon class="ml-2" name="co-reload" scale="1.3" :animation="refreshIconSpin"/>
                </button>
            </div>
            <div>
                <button
                    type="button"
                    class="p-1 mr-2 focus:outline-none transition"
                    aria-label="Dismiss"
                    @click.prevent="showRefreshNotification = false"
                >
                    <v-icon name="ri-close-fill" scale="1.1"/>
                </button>
            </div>
        </div>
        </transition>
        <Grid>
            <PostExcerpt v-for="post in items"
                   :key="post.id"
                   :routes="post.routes"
                   :title="post.title"
                   :body="post.body"
                   :posted-at="post.created_at">
            </PostExcerpt>
        </Grid>
        <div ref="loader" class="flex justify-center mt-10">
            <v-icon v-if="items.length > 9 && canLoadMoreItems" name="fa-spinner" scale="2" animation="spin"/>
        </div>
    </Shell>
</template>
