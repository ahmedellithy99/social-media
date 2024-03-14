<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "@/Components/app/PostModal.vue";
import AttachmentModal from "@/Components/app/AttachmentModal.vue";
import {onUpdated, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import { onMounted } from "vue";
import axiosClient from "@/axiosClient.js";

const props =defineProps({
    posts : Object
});

const page = usePage();

const allPosts = ref({
    data: page.props.posts.data,
    next: page.props.posts.links.next
})

const authUser = usePage().props.auth.user;
const showEditModal = ref(false)
const showAttachmentsModal = ref(false)
const previewAttachmentPost = ref({})
const editPost = ref({})
const loadMoreIntersect = ref(null)

function openEditModal(post) {
    editPost.value = post;
    
    showEditModal.value = true;
}

function onModalHide() {
    editPost.value = {
        id: null,
        body: '',
        user: authUser
    }   
}

function openAttachmentPreviewModal(post , index)
{   
    previewAttachmentPost.value = {
        post ,
        index
    }
    showAttachmentsModal.value = true
}

function loadMore()
{
    console.log('alo');
    if (!allPosts.value.next) {
        console.log('alo mn el a5er');
        return;
    }

    axiosClient.get(allPosts.value.next)
        .then(({data}) => {
            // console.log(data);
            
            allPosts.value.data = [...allPosts.value.data, ...data.data]
            allPosts.value.next = data.links.next
        })
}

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
            rootMargin: '-250px 0px 0px 0px'
        })
    observer.observe(loadMoreIntersect.value)
})

// onUpdated(()=>{
//     console.log(props.posts);
//     props.posts = allPosts.data
// }
// )

</script>

<template>
  
    <div class="overflow-auto">
        <PostItem v-for="post of allPosts.data" :key="post.id" :post="post"
        @editClick="openEditModal"
        @attachmentClick="openAttachmentPreviewModal"/>
        <!-- <div class="mb-10"></div> -->
        
        <div ref="loadMoreIntersect"></div> 

        <PostModal :post="editPost" v-model="showEditModal" @hide="onModalHide"/>
        <AttachmentModal :attachments="previewAttachmentPost.post?.attachments || []" , 
                        v-model:index="previewAttachmentPost.index"
                        v-model="showAttachmentsModal"/>

    </div>
</template>

<style scoped>
</style>