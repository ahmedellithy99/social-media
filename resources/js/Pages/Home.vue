<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import GroupList from "@/Components/app/GroupList.vue";
import FollowingList from "@/Components/app/FollowingList.vue";
import CreatePost from "@/Components/app/CreatePost.vue";
import PostList from "@/Components/app/PostList.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    posts: Object,
    notifications: Array,
    followings: Array,
    chats: Array,
    countUnReads: Number,
    countUnReadChats: Number,
});
</script>
<template>
    <Head title="Social Media Website" />
    <AuthenticatedLayout
        :notifications="notifications"
        :chats="chats"
        :countUnReads="countUnReads"
        :countUnReadChats="countUnReadChats"
    >
        <div class="grid lg:grid-cols-12 gap-3 p-4 h-full">
            <div class="lg:col-span-3 lg:order-1 h-full overflow-hidden">
                <GroupList />
            </div>
            <div class="lg:col-span-3 lg:order-3 h-full overflow-hidden">
                <FollowingList :followings="followings" />
            </div>
            <div
                class="lg:col-span-6 lg:order-2 h-full overflow-hidden flex flex-col"
            >
                <CreatePost />
                <PostList class="flex-1" :posts="posts.data" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style></style>
