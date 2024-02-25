<script setup>

import { Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { router, usePage } from "@inertiajs/vue3";
import DropdownLink from "@/Components/DropdownLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Banner from "@/Components/Banner.vue";
import ConfirmationModalWrapper from "@/Components/ConfirmationModalWrapper.vue";

const logout = () => {
    router.post(route('logout'));
};

const navigation = [
    {
        name: 'Dashboard',
        url: route('dashboard'),
        route: 'dashboard',
        when: () => usePage().props.auth.user,
    },
    {
        name: 'Posts',
        url: route('posts.index'),
        route: 'posts.index',
    },
];

const props = defineProps({
    title: String,
});
</script>

<template>
    <div>
        <Banner />
        <Head :title="title" />
        <div class="min-h-full">
            <div class="bg-gray-800 pb-32">
                <Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div class="border-b border-gray-700">
                            <div class="flex h-16 items-center justify-between px-4 sm:px-0">
                                <div class="flex items-center">
                                    <div class="mr-4">
                                        <a :href="route('dashboard')">
                                            <img src="http://laracasts-forum.test/assets/symbol-negative.png" alt="" class="h-10">
                                        </a>
                                    </div>
                                    <div class="hidden md:block">
                                        <div class="flex items-baseline space-x-4">
                                            <a v-for="item in navigation"
                                               :key="item.name" :href="item.url"
                                               :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'rounded-md px-3 py-2 text-sm font-medium']" :aria-current="item.current ? 'page' : undefined"
                                            >{{ item.name }}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="hidden md:block">
                                    <div class="ml-4 flex items-center md:ml-6">
                                        <button type="button" class="rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                                            <span class="sr-only">View notifications</span>
                                            <BellIcon class="h-6 w-6" aria-hidden="true" />
                                        </button>

                                        <!-- Profile dropdown -->
                                        <div v-if="$page.props.auth.user" class="ms-3 relative">
                                            <Dropdown align="right" width="48">
                                                <template #trigger>
                                                    <button id="account-dropdown" v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                        <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                                    </button>

                                                    <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                                </template>

                                                <template #content>
                                                    <!-- Account Management -->
                                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                                        Manage Account
                                                    </div>

                                                    <DropdownLink :href="route('profile.show')">
                                                        Profile
                                                    </DropdownLink>

                                                    <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                                        API Tokens
                                                    </DropdownLink>

                                                    <div class="border-t border-gray-200" />

                                                    <!-- Authentication -->
                                                    <form @submit.prevent="logout">
                                                        <DropdownLink as="button">
                                                            Log Out
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </Dropdown>
                                        </div>
                                        <div v-else class="ml-2" >
                                            <Link class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium" :href="route('login')">Login</Link>
                                        </div>
                                    </div>
                                </div>
                                <div class="-mr-2 flex md:hidden">
                                    <!-- Mobile menu button -->
                                    <DisclosureButton class="inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                                        <span class="sr-only">Open main menu</span>
                                        <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                                        <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                                    </DisclosureButton>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DisclosurePanel class="border-b border-gray-700 md:hidden">
                        <div class="space-y-1 px-2 py-3 sm:px-3">
                            <DisclosureButton v-for="item in navigation" :key="item.name" as="a" :href="item.href" :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'block rounded-md px-3 py-2 text-base font-medium']" :aria-current="item.current ? 'page' : undefined">{{ item.name }}</DisclosureButton>
                        </div>
                        <div class="border-t border-gray-700 pb-3 pt-4">
                            <div class="flex items-center px-5">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" :src="user.imageUrl" alt="" />
                                </div>
                                <div class="ml-3">
                                    <div class="text-base font-medium leading-none text-white">{{ user.name }}</div>
                                    <div class="text-sm font-medium leading-none text-gray-400">{{ user.email }}</div>
                                </div>
                                <button type="button" class="ml-auto flex-shrink-0 rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                                    <span class="sr-only">View notifications</span>
                                    <BellIcon class="h-6 w-6" aria-hidden="true" />
                                </button>
                            </div>
                            <div class="mt-3 space-y-1 px-2">
                                <DisclosureButton v-for="item in userNavigation" :key="item.name" as="a" :href="item.href" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">{{ item.name }}</DisclosureButton>
                            </div>
                        </div>
                    </DisclosurePanel>
                </Disclosure>
                <div class="flex">
                    <div class=" grow">
                        <header v-if="$slots.header" class="py-5">
                            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                                <slot name="header"/>
                            </div>
                        </header>
                    </div>
                    <div v-if="$slots.header" class="mr-6 mt-4">
                        <slot name="search"></slot>
                    </div>
                </div>
            </div>

            <main class="-mt-32">
                <div class="mt-2 mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                    <slot></slot>
                </div>
            </main>
        </div>

        <ConfirmationModalWrapper/>
    </div>
</template>

<style scoped>

</style>
