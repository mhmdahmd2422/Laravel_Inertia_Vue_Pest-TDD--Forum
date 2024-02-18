<script setup>

import {relativeDate} from "@/Utilities/date.js";
import ListItem from "@/Components/ListItem.vue";
import {useThumbnail} from "@/Composables/useThumbnail";
const props = defineProps({
    routes: {
        type: Object,
    },
    title: {
        type: String,
    },
    body: {
        type: String,
    },
    postedAt: {
        type: String,
    },
});
const thumbnail = useThumbnail().generateThumbnail();
</script>

<template>
    <ListItem>
        <li class="relative">
            <Link :href="props.routes.show">
                <div class="group aspect-h-7 aspect-w-10 block w-full overflow-hidden rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 focus-within:ring-offset-gray-100">
                    <img :src="thumbnail" alt="Video thumbnail" class="pointer-events-none object-cover group-hover:opacity-75" />
                </div>
                <p class="my-2 pointer-events-none mt-2 block truncate text-sm font-medium text-gray-900">{{ props.title }}</p>
                <p class="pointer-events-none line-clamp-3 text-sm text-gray-500">{{ props.body }}</p>
                <p class="mt-2 pointer-events-none block text-sm font-medium text-gray-500">{{ relativeDate(props.postedAt) }} Ago</p>
            </Link>
        </li>
    </ListItem>
</template>

