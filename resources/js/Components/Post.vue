<script setup>

import {relativeDate} from "@/Utilities/date.js";
import Card from "@/Components/Card.vue";
const props = defineProps({
    post: Object,
})
</script>

<template>
    <Card>
        <template #header>
            <h1 class="text-3xl font-extrabold">{{post.title}}</h1>
            <span class="block mt-1 text-sm text-gray-600">{{relativeDate(post.created_at)}} ago by {{post.user.name}}</span>
        </template>
        <article class="mt-6">
            <pre class="whitespace-pre-wrap font-sans">{{post.body}}</pre>
        </article>
    </Card>
</template>
