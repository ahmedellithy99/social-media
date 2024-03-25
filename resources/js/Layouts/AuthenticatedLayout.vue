<script setup>
import { computed, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";

const showingNavigationDropdown = ref(false);
const showingNotificationDropdown = ref(false);
const showingMessagesDropdown = ref(false);
const showProfileDropdown = ref(false);
const keywords = ref("");
const authUser = usePage().props.auth.user;
const isRead = ref(true);

const props = defineProps({
    notifications: Array,
    chats: Array,
    countUnReads: Number,
});

function search() {
    router.get("/users?search=" + keywords.value);
}

if (props.countUnReads > 0) {
    isRead.value = false;
}

function markAsRead() {
    if (props.countUnReads > 0) {
        router.post(route("user.markAsRead", authUser.id));
    }
}
</script>

<template>
    <div class="overflow-auto">
        <div
            class="h-screen overflow-hidden flex flex-col bg-gray-100 dark:bg-gray-900"
        >
            <nav
                class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700"
            >
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-4 h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                </Link>
                            </div>
                        </div>
                        <!-- Search -->
                        <div class="flex-1">
                            <TextInput
                                v-model="keywords"
                                placeholder="Search on the website"
                                class="w-full"
                                @keyup.enter="search"
                            />
                        </div>

                        <div
                            v-if="authUser"
                            class="hidden sm:flex sm:items-center"
                        >
                            <div class="ms-3 relative flex">
                                <!-- Profile and Logout Dropdown -->
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ authUser.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="
                                                route('profile', {
                                                    username: authUser.username,
                                                })
                                            "
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>

                                <div
                                    v-if="authUser"
                                    class="hidden sm:flex sm:items-center"
                                >
                                    <!-- Messages Dropdown -->
                                    <div class="relative flex">
                                        <Dropdown align="right" width="48">
                                            <template #trigger>
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                        class="w-6 h-6"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"
                                                        />
                                                    </svg>

                                                    <svg
                                                        class="ms-2 -me-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                        
                                                    </svg>
                                                </button>
                                            </template>

                                            <template #content>
                                                <div
                                                    class="h-48 w-64 origin-top-right absolute right-0 mt-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-auto"
                                                >
                                                    <div
                                                        v-for="chat of chats"
                                                        class="flex items-center justify-center"
                                                    >
                                                        <img
                                                            v-if="
                                                                chat.lastMessage !=
                                                                null
                                                            "
                                                            :src="
                                                                chat.user
                                                                    .avatar_url
                                                            "
                                                            class="aspect aspect-square ml-3 max-w-8 max-h-8 rounded-full"
                                                        />
                                                        <small
                                                            v-if="
                                                                chat.lastMessage !=
                                                                null
                                                            "
                                                            class="ml-1"
                                                        >
                                                            {{ chat.user.name }}
                                                        </small>

                                                        <DropdownLink
                                                            v-if="
                                                                chat.lastMessage !=
                                                                null
                                                            "
                                                            :href="
                                                                route(
                                                                    'chat.index',
                                                                    chat.subId
                                                                )
                                                            "
                                                            ><div>
                                                                <p>
                                                                    {{
                                                                        chat
                                                                            .lastMessage[
                                                                            "body"
                                                                        ]
                                                                    }}
                                                                </p>
                                                                <span
                                                                    class="text-xs text-gray-400"
                                                                    >{{
                                                                        chat.timeOflastMessage
                                                                    }}</span
                                                                >
                                                            </div>
                                                        </DropdownLink>
                                                    </div>
                                                </div>
                                            </template>
                                        </Dropdown>
                                    </div>
                                </div>

                                <div
                                    v-if="authUser"
                                    class="hidden sm:flex sm:items-center sm:ms-6 relative"
                                >
                                    <!-- Notifications Dropdown -->
                                    <div>
                                        <button
                                            @click="
                                                showProfileDropdown =
                                                    !showProfileDropdown;
                                                isRead = true;
                                                markAsRead();
                                            "
                                            type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                            aria-haspopup="true"
                                            aria-expanded="true"
                                        >
                                            <svg
                                                class="w-5 h-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <!-- Adjust the SVG icon for your notification icon -->
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 18v-6a9 9 0 0118 0v6"
                                                ></path>
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13.73 21a2 2 0 01-3.46 0"
                                                ></path>
                                                <circle
                                                    v-if="isRead == false"
                                                    cx="18"
                                                    cy="6"
                                                    r="3"
                                                    fill="red"
                                                ></circle>
                                            </svg>
                                        </button>

                                        <!-- Profile Dropdown Content -->
                                        <div
                                            v-if="showProfileDropdown"
                                            class="h-48 w-64 origin-top-right absolute right-0 mt-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-auto"
                                            role="menu"
                                            aria-orientation="vertical"
                                            aria-labelledby="options-menu"
                                        >
                                            <div class="py-1" role="none">
                                                <div
                                                    v-for="notification of notifications"
                                                    class="flex items-center justify-center"
                                                >
                                                    <img
                                                        :src="
                                                            notification.avatar_url
                                                        "
                                                        class="ml-3 aspect aspect-square max-w-8 max-h-8 rounded-full"
                                                    />
                                                    <DropdownLink
                                                        v-if="
                                                            notification.post_id
                                                        "
                                                        :href="
                                                            route(
                                                                'post.show',
                                                                notification.post_id
                                                            )
                                                        "
                                                    >
                                                        {{ notification.text }}
                                                    </DropdownLink>
                                                    <DropdownLink
                                                        v-if="
                                                            notification.username
                                                        "
                                                        :href="
                                                            route(
                                                                'profile',
                                                                notification.username
                                                            )
                                                        "
                                                    >
                                                        {{ notification.text }}
                                                    </DropdownLink>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else>
                            <Link :href="route('login')"> Login </Link>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                        <!-- Messages -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingMessagesDropdown =
                                        !showingMessagesDropdown
                                "
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-6 h-6"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNotificationDropdown =
                                        !showingNotificationDropdown;
                                    isRead = true;
                                    markAsRead();
                                "
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                            >
                                <svg
                                    class="w-5 h-5 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <!-- Adjust the SVG icon for your notification icon -->
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 18v-6a9 9 0 0118 0v6"
                                    ></path>
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.73 21a2 2 0 01-3.46 0"
                                    ></path>
                                    <circle
                                        v-if="isRead == false"
                                        cx="18"
                                        cy="6"
                                        r="3"
                                        fill="red"
                                    ></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    v-if="authUser"
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <!-- Responsive Settings Options -->
                    <div
                        class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600"
                    >
                        <div class="px-4">
                            <div
                                class="font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                {{ authUser.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ authUser.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                                :href="
                                    route('profile', {
                                        username: authUser.username,
                                    })
                                "
                            >
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                        <div></div>
                    </div>
                </div>

                <!-- Message -->

                <div
                    v-if="authUser"
                    :class="{
                        block: showingMessagesDropdown,
                        hidden: !showingMessagesDropdown,
                    }"
                    class="sm:hidden"
                >
                    <!-- Responsive Messages Dropdown Content -->
                    <!-- Your messages dropdown content here -->
                    <div
                        class="h-48 pt-4 pb-1 border-t border-gray-200 dark:border-gray-600 overflow-auto"
                    >
                        <div class="px-4">
                            <div
                                class="font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                Messages
                            </div>
                            <div class="mt-3 space-y-1">
                                <!-- Message items -->
                                <div
                                    v-for="chat of chats"
                                    class="flex items-center justify-center"
                                >
                                    <img
                                        v-if="chat.lastMessage != null"
                                        :src="chat.user.avatar_url"
                                        class="aspect aspect-square max-w-8 max-h-8 rounded-full"
                                    />
                                    <p
                                        v-if="chat.lastMessage != null"
                                        class="ml-1"
                                    >
                                        {{ chat.user.name }}
                                    </p>

                                    <DropdownLink
                                        v-if="chat.lastMessage != null"
                                        :href="route('chat.index', chat.subId)"
                                        ><div>
                                            <p>
                                                {{ chat.lastMessage["body"] }}
                                            </p>
                                            <span
                                                class="text-xs text-gray-400"
                                                >{{
                                                    chat.timeOflastMessage
                                                }}</span
                                            >
                                        </div>
                                    </DropdownLink>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="showingNotificationDropdown" class="sm:hidden">
                    <!-- Content for notifications dropdown -->
                    <div
                        class="h-48 pt-4 pb-1 border-t border-gray-200 dark:border-gray-600 overflow-auto"
                    >
                        <!-- Notification content here -->
                        <!-- For example: -->
                        <div class="px-4">
                            <div
                                class="font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                Notifications
                            </div>
                            <div class="mt-3 space-y-1">
                                <!-- Notification items -->
                                <div
                                    v-for="notification of notifications"
                                    class="flex items-center justify-center"
                                >
                                    <img
                                        :src="notification.avatar_url"
                                        class="aspect aspect-square max-w-8 max-h-8 rounded-full"
                                    />
                                    <DropdownLink
                                        v-if="notification.post_id"
                                        :href="
                                            route(
                                                'post.show',
                                                notification.post_id
                                            )
                                        "
                                    >
                                        {{ notification.text }}
                                    </DropdownLink>
                                    <DropdownLink
                                        v-if="notification.username"
                                        :href="
                                            route(
                                                'profile',
                                                notification.username
                                            )
                                        "
                                    >
                                        {{ notification.text }}
                                    </DropdownLink>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white dark:bg-gray-800 shadow"
                v-if="$slots.header"
            >
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-hidden">
                <slot />
            </main>
        </div>
    </div>
</template>
