<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import {computed} from "vue";
import {formatDistance, parseISO} from "date-fns";
import Pagination from "@/Components/Pagination.vue";
import {relativeDate} from "../../Utilities/date.js";
import Comment from "@/Components/Comment.vue";

const props = defineProps({
   post: Object,
   comments: Object,
})

const formattedDate = computed(() => formatDistance(parseISO(props.post.created_at), new Date()));
</script>

<template>
    <AppLayout :title="post.title">
        <container>
            <h1 class="text-2xl font-extrabold">{{post.title}}</h1>
            <span class="block mt-1 text-sm text-gray-600">{{relativeDate(post.created_at)}} ago by {{post.user.name}}</span>
            <article class="mt-6">
                <pre class="whitespace-pre-wrap font-sans">{{post.body}}</pre>
            </article>

            <div class="mt-12">
                <h2 class="text-xl font-semibold">Comments</h2>

                <ul class="divide-y  mt-4">
                    <li v-for="comment in comments.data"
                        :key="comment.id"
                        class="px-2 py-4"
                    >
                        <Comment :comment="comment" :key="comment.id" ></Comment>
                    </li>
                </ul>
                <Pagination :meta="comments.meta" :links="comments.links" :only="['comments']"/>
            </div>
        </container>
    </AppLayout>
</template>
