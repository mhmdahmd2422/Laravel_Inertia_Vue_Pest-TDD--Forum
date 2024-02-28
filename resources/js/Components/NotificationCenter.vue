<script setup>

import {relativeDate} from "@/Utilities/date.js";
import {BellIcon} from '@heroicons/vue/24/outline';
import DropdownLink from "@/Components/DropdownLink.vue";
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";

const newNotification = ref(null);
const notifications = ref(usePage().props.notifications);

if( usePage().props.auth.user ){
    Echo.private('users.' + usePage().props.auth.user?.id)
        .notification((notification) => {
            newNotification.value = notification;
            notifications.value.data = [newNotification.value ,...notifications.value.data];
            notifications.value.unread += 1;
        });
}

const markAsRead = (notificationId) => {
    axios({
        method: 'post',
        url: '/notifications/markAsRead/' + notificationId,
    });
};

const markAsSeen = (notificationId) => {
    axios({
        method: 'post',
        url: '/notifications/markAsRead/' + notificationId,
    });
    notifications.value.unread -= 1;
    for (const [notificationkey, notification] of Object.entries(notifications.value)) {
        for (const [Key, element] of Object.entries(notification)) {
            if (element.id === notificationId) {
                notifications.value.data[Key].read_at = 1;
            }
        }
    }
};

const markAllAsRead = () => {
    axios({
        method: 'post',
        url: '/notifications/markAllAsRead/' + usePage().props.auth.user.id,
    }).then(function (responseNotifications){
        notifications.value = responseNotifications;
    });
}
</script>

<template>
    <Dropdown align="right" width="96">
        <template #trigger>
            <button type="button" class="bg-gray-800 p-1 text-gray-400 hover:text-white focus:text-white">
                <span class="sr-only">View notifications</span>
                <strong class="relative inline-flex items-center px-2.5 py-1.5 text-xs font-medium">
                    <span v-if="notifications.unread" class="absolute -top-0 -right-0 h-5 w-5 rounded-full bg-midnight-100 flex justify-center items-center items">
                        <span>{{ notifications.unread }}</span>
                    </span>
                    <span>
                    <BellIcon class="h-7 w-7 mt-0.5" aria-hidden="true" />
                    </span>
                </strong>
            </button>
        </template>

        <template #content>
            <div class="flex px-4 py-2 text-xs text-gray-400">
                <div class="grow">
                    Notifications
                </div>
                <button type="button" @click.prevent="markAllAsRead">
                    <div class="text-black bg-gray-100 rounded-full py-1 px-2 hover:transition-all ease-in-out delay-50 hover:outline outline-midnight-50 outline-offset-2 outline-1 duration-800">
                        Read all
                    </div>
                </button>
            </div>

            <div v-if="$page.props.auth.user">
                <div v-if="! notifications.data?.length">
                    <div class="flex justify-center px-2 py-2 font-semibold bg-gray-200">
                        No Notifications yet!
                    </div>
                </div>

                <div v-for="notification in notifications.data">
                    <a :href="notification.data.link"
                       @click="markAsRead(notification.id)"
                       :class="{ 'bg-blue-100' : !notification.read_at}"
                       class="block px-3 py-1 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                        <div class="flex">
                            <div class="flex-shrink-0 sm:mb-0 sm:mr-3 self-start">
                                <img :src="notification.data.image" class="h-10 w-10 rounded-full" />
                            </div>
                            <div class="grow text-sm">
                                {{ notification.data.message }}
                            </div>
                            <div v-if="! notification.read_at">
                                <button @click.prevent="markAsSeen(notification.id)" class=" px-0.5 py-0.5 bg-gray-100 rounded-full">
                                    <v-icon name="fa-eye"/>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-end text-xs italic text-gray-400">
                            {{ relativeDate(notification.created_at) }} ago
                        </div>
                    </a>
                </div>

                <div class="border-t border-gray-200" />

                <DropdownLink as="button" v-if="notifications.data?.length">
                    <div class="flex justify-center">
                        View All
                    </div>
                </DropdownLink>
            </div>

            <div v-if="! $page.props.auth.user">
                <a :href="route('login')">
                    <div class="flex justify-center bg-gray-200 px-3 py-3 font-semibold">
                        Please Login to view your notifications!
                    </div>
                </a>
            </div>

        </template>
    </Dropdown>
</template>
