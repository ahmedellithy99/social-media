<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import {
    PencilIcon,
    TrashIcon,
    EllipsisVerticalIcon,
} from "@heroicons/vue/20/solid";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import {
    ChatBubbleLeftRightIcon,
    HandThumbUpIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";
import PostUserHeader from "@/Components/app/PostUserHeader.vue";
import { router, usePage } from "@inertiajs/vue3";
import { isImage, isVideo } from "@/helpers.js";
import axiosClient from "@/axiosClient.js";
import ReadMoreReadLess from "@/Components/app/ReadMoreReadLess.vue";
import InputTextarea from "../InputTextarea.vue";
import IndigoButton from "@/Components/app/IndigoButton.vue";

const props = defineProps({
    post: Object,
});

const authUser = usePage().props.auth.user;

const emit = defineEmits(["editClick", "attachmentClick"]);

const newCommentText = ref("");

function openEditModal() {
    emit("editClick", props.post);
}

function deletePost() {
    if (window.confirm("Are you sure you want to delete this post?")) {
        router.delete(route("post.delete", props.post), {
            preserveScroll: true,
        });
    }
}

function openAttachment(ind) {
    emit("attachmentClick", props.post, ind);
}

function sendReaction() {
    console.log("alo");
    axiosClient
        .post(route("post.reaction", props.post), {
            reaction: "like",
        })

        .then(({ data }) => {
            props.post.current_user_has_reaction =
                data.current_user_has_reaction;
            props.post.num_of_reactions = data.num_of_reactions;
        });
}

function createComment() {
    axiosClient
        .post(route("post.comment", props.post), {
            comment: newCommentText.value,
        })
        .then(({ data }) => {
            newCommentText.value = "";
            props.post.comments.unshift(data);
            props.post.num_of_comments++;
        });
}

function deleteComment(commentId) {
    axiosClient.delete(route("delete.comment", commentId), {}).then(() => {
        props.post.comments = props.post.comments.filter(
            (c) => c.id !== commentId
        );
        props.post.num_of_comments--;
    });
}

function commentReaction(comment) {
    axiosClient
        .post(route("comment.reaction", comment.id), {
            reaction: "like",
        })
        .then(({ data }) => {
            comment.current_user_has_reaction = data.current_user_has_reaction;
            comment.num_of_reactions = data.num_of_reactions;
        });
}

function getPost() {
    router.get(route("post.show", props.post));
}
</script>
<template>
    <div class="bg-white border rounded p-4 mb-3">
        <!-- Post Header -->

        <div class="flex items-center justify-between mb-3">
            <PostUserHeader :post="post" />
            <Menu as="div" class="relative z-10 inline-block text-left">
                <div>
                    <MenuButton
                        class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center"
                    >
                        <EllipsisVerticalIcon
                            class="w-5 h-5"
                            aria-hidden="true"
                        />
                    </MenuButton>
                </div>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems
                        class="absolute z-20 right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                    >
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="openEditModal"
                                    :class="[
                                        active
                                            ? 'bg-indigo-500 text-white'
                                            : 'text-gray-900',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                >
                                    <PencilIcon
                                        class="mr-2 h-5 w-5"
                                        aria-hidden="true"
                                    />
                                    Edit
                                </button>
                            </MenuItem>
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="deletePost"
                                    :class="[
                                        active
                                            ? 'bg-indigo-500 text-white'
                                            : 'text-gray-900',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                >
                                    <TrashIcon
                                        class="mr-2 h-5 w-5"
                                        aria-hidden="true"
                                    />
                                    Delete
                                </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </div>

        <!-- Post Content -->
        <div class="mb-3 cursor-pointer" @click="getPost">
            <ReadMoreReadLess :content="post.body" />
        </div>

        <!-- Post Attachments -->
        <div
            class="grid gap-3 mb-3"
            :class="[
                post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2',
            ]"
        >
            <!-- Attachment Grid -->
            <template v-for="(attachment, ind) of post.attachments.slice(0, 4)">
                <!-- Attachment Item -->
                <div
                    @click="openAttachment(ind)"
                    class="group bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative cursor-pointer"
                >
                    <div
                        v-if="ind === 3 && post.attachments.length > 4"
                        class="absolute left-0 top-0 right-0 bottom-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl"
                    >
                        +{{ post.attachments.length - 4 }} more
                    </div>
                    <!-- Download Button -->
                    <button
                        class="z-20 opacity-0 group-hover:opacity-100 transition-all w-8 h-8 flex items-center justify-center text-gray-100 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                    </button>
                    <!-- Image or Icon -->
                    <img
                        v-if="isImage(attachment)"
                        :src="attachment.url"
                        class="w-full h-full"
                        alt="Attachment"
                    />
                    <div
                        v-else-if="isVideo(attachment)"
                        class="relative flex justify-center items-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="z-20 absolute w-16 h-16 text-white opacity-70"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z"
                            />
                        </svg>

                        <div
                            class="absolute left-0 top-0 w-full h-full bg-black/50 z-10"
                        ></div>
                        <video :src="attachment.url"></video>
                    </div>
                    <template v-else>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-12 h-12"
                        >
                            <path
                                d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z"
                            />
                            <path
                                d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"
                            />
                        </svg>
                        <small>{{ attachment.name }}</small>
                    </template>
                </div>
            </template>
        </div>

        <Disclosure>
            <!-- Post Actions (Like, Comment) -->

            <div class="flex gap-2">
                <!-- Like Button -->
                <button
                    @click="sendReaction"
                    class="text-gray-800 flex gap-1 items-center justify-center rounded-lg py-2 px-4 flex-1 hover:bg-gray-200"
                    :class="[
                        post.current_user_has_reaction
                            ? 'bg-gray-300'
                            : 'bg-gray-100',
                    ]"
                >
                    <HandThumbUpIcon class="w-5 h-5" />
                    <span class="mr-2">{{ post.num_of_reactions }}</span>
                    {{ post.current_user_has_reaction ? "Unlike" : "Like" }}
                </button>
                <!-- Comment Button -->
                <DisclosureButton
                    class="text-gray-800 flex gap-1 items-center justify-center rounded-lg py-2 px-4 flex-1 bg-gray-100 hover:bg-gray-200"
                >
                    <ChatBubbleLeftRightIcon class="w-5 h-5" />
                    <span class="mr-2">{{ post.num_of_comments }}</span>
                    Comment
                </DisclosureButton>
            </div>

            <!-- Comments -->
            <DisclosurePanel class="mt-3">
                <div class="flex gap-2 mb-3">
                    <!-- User Avatar and Comment Input -->
                    <InputTextarea
                        v-model="newCommentText"
                        placeholder="Enter your comment here"
                        rows="1"
                        class="w-full max-h-[160px] resize-none rounded-r-none"
                    ></InputTextarea>
                    <IndigoButton
                        @click="createComment"
                        class="rounded-l-none w-[100px]"
                        >Submit</IndigoButton
                    >
                </div>
                <div>
                    <!-- Comment Items -->
                    <div
                        class="flex mb-4 mt-8"
                        v-for="comment of props.post.comments"
                    >
                        <!-- Comment Content -->
                        <div class="flex items-start">
                            <!-- User Avatar -->
                            <a :href="route('profile', comment.user.username)"
                                ><img
                                    :src="comment.user.avatar_url"
                                    alt="User Avatar"
                                    class="rounded-full w-10 h-10 mr-2"
                            /></a>
                            <!-- Comment Details -->
                            <div>
                                <p class="font-semibold text-blue-500">
                                    {{ comment.user.name }}
                                </p>
                                <p v-html="comment.comment"></p>
                                <div class="flex items-center text-gray-500">
                                    <span class="mr-2">{{
                                        comment.updated_at
                                    }}</span>
                                    <p>
                                        {{
                                            comment.num_of_reactions
                                                ? comment.num_of_reactions
                                                : ""
                                        }}
                                    </p>
                                    <button
                                        @click="commentReaction(comment)"
                                        :class="[
                                            comment.current_user_has_reaction
                                                ? 'bg-gray-300'
                                                : 'bg-gray-100',
                                        ]"
                                    >
                                        {{
                                            comment.current_user_has_reaction
                                                ? "Unlike"
                                                : "Like"
                                        }}
                                    </button>
                                    <span class="mx-2">·</span>
                                    <span>Reply</span>
                                </div>
                            </div>
                            <!-- Delete Comment Button -->
                            <div
                                class="ml-auto"
                                v-if="authUser.id === comment.user.id"
                            >
                                <button
                                    @click="deleteComment(comment.id)"
                                    class="text-red-500"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>

<style scoped>
/* Your scoped styles go here */
</style>
<style scoped></style>
