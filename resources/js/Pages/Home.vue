<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GroupList from "@/Components/app/GroupList.vue";
import FollowingList from "@/Components/app/FollowingList.vue";
import CreatePost from '@/Components/app/CreatePost.vue';
import PostItem from '@/Components/app/PostItem.vue';



</script>

<template>
    <Head title="Social Media Website" />


    <div class="grid lg:grid-cols-12 gap-3 p-4 h-full ">
        <div class="lg:col-span-3 lg:order-1  overflow-hidden h-5/6">
            <GroupList /> 
        </div>
        <div class="lg:col-span-3 lg:order-3  overflow-hidden h-5/6">
            <FollowingList />
        </div>
        <div class="lg:col-span-6 lg:order-2 h-full overflow-hidden flex flex-col">
            <CreatePost />
            <PostItem />
        </div>
    </div>
</template>

<style>
</style>