<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import PostModal from "@/Components/app/PostModal.vue";
import AttachmentModal from "@/Components/app/AttachmentModal.vue";
import {usePage} from "@inertiajs/vue3";

defineProps({
    post: Object,
    notifications : Array,
    chats : Array,
    countUnReads: Number,

})


const authUser = usePage().props.auth.user;
const showEditModal = ref(false)
const showAttachmentsModal = ref(false)
const editPost = ref({})
const previewAttachmentsPost = ref({})

function openEditModal(post) {
    editPost.value = post;
    showEditModal.value = true;
}

function openAttachmentPreviewModal(post, index) {
    previewAttachmentsPost.value = {
        post,
        index
    }
    showAttachmentsModal.value = true;
}

function onModalHide() {
    editPost.value = {
        id: null,
        body: '',
        user: authUser
    }
}

</script>

<template>
    <AuthenticatedLayout
        :notifications="notifications"
        :chats="chats"
        :countUnReads="countUnReads"
    >
        <div class="p-8 lg:w-[700px] mx-auto h-full overflow-auto">
            <PostItem :post="post"
                    @editClick="openEditModal"
                    @attachmentClick="openAttachmentPreviewModal"/>
        </div>
        <PostModal :post="editPost" v-model="showEditModal" @hide="onModalHide"/>
        <AttachmentModal :attachments="previewAttachmentsPost.post?.attachments || []"
                                v-model:index="previewAttachmentsPost.index"
                                v-model="showAttachmentsModal"/>
    </AuthenticatedLayout>
</template>

<style scoped>
</style>