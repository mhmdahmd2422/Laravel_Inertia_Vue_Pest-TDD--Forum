<script setup>

import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";

const props = defineProps({
    comment: Object,
});

const emit = defineEmits(['delete', 'edit']);
</script>

<template>
    <Menu>
        <MenuButton
            class="flex items-center bg-gray-200 hover:bg-gray-300 rounded-md px-1 py-1"
        >
            <v-icon name="bi-three-dots-vertical"/>
        </MenuButton>
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-out"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute bg-gray-200 w-14 px-1 py-2 mt-8 shadow-md rounded-lg
                                 flex flex-col focus:outline-none origin-top-right"
            >
                <MenuItem as="form"
                          v-if="props.comment.author"
                          :disabled="!props.comment.can?.update"
                          @submit.prevent="$emit('edit')"
                          class="flex justify-center mx-0.5 my-0.5 px-1 py-1 rounded
                                          transition ease-in-out delay-150 bg-gray-300 hover:scale-110
                                          hover:bg-midnight-100 hover:text-white duration-300"
                          v-slot="{ active, disabled }">
                    <button :disabled="!props.comment.can?.update" class="w-full" :class='{ "transition ease-in-out delay-150 bg-gray-300 hover:scale-110 hover:bg-midnight-100 hover:text-white duration-300": active, "opacity-50 cursor-not-allowed" : disabled }' type="submit">
                        <v-icon name="fa-edit" scale="1"/>
                    </button>
                </MenuItem>
                <MenuItem as="form"
                          v-if="props.comment.author"
                          :disabled="!props.comment.can?.delete"
                          @submit.prevent="$emit('delete')"
                          class="flex justify-center mx-0.5 my-0.5 px-1 py-1 rounded
                                          transition ease-in-out delay-150 bg-gray-300 hover:scale-110
                                          hover:bg-red-800 hover:text-white duration-300"
                          v-slot="{ active, disabled }">
                    <button :disabled="!props.comment.can?.delete" class="w-full" :class='{"transition ease-in-out delay-150 bg-gray-300 hover:scale-110 hover:bg-red-800 hover:text-white duration-300": active, "opacity-50 cursor-not-allowed" : disabled}' type="submit">
                        <v-icon name="fa-trash" scale="1"/>
                    </button>
                </MenuItem>
                <MenuItem as="form"
                          :disabled="!$page.props.auth.user"
                          class="flex justify-center mx-0.5 my-0.5 px-1 py-1 rounded
                                          transition ease-in-out delay-150 bg-gray-300 hover:scale-110
                                          hover:bg-yellow-700 hover:text-white duration-300"
                          v-slot="{ active, disabled }">
                    <button class="w-full" :class='{"transition ease-in-out delay-150 bg-gray-300 hover:scale-110 hover:bg-yellow-700 hover:text-white duration-300": active, "opacity-50 cursor-not-allowed" : disabled}' type="submit">
                        <v-icon name="oi-report" scale="1"/>
                    </button>
                </MenuItem>
            </MenuItems>
        </transition>
    </Menu>
</template>
