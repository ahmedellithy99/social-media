<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import PostModal from "@/Components/app/PostModal.vue";

const authUser = usePage().props.auth.user;

const showModal = ref(false);

const createPost = ref({
    id: null,
    body: "",
    user: authUser,
});

function ShowCreatePostModal() {
    showModal.value = true;
}
</script>

<template>
    <div class="p-4 bg-white rounded-lg border mb-3">
        <div
            @click="ShowCreatePostModal"
            class="py-3 px-2 border-2 border-gray-200 rounded mb-3"
        >
            Create a Post Yalla
        </div>
        <!-- <div v-if="postCreating" class="flex gap-2 justify-between">
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 relative">
                Attach Files
                <input type="file" class="absolute left-0 top-0 right-0 bottom-0 opacity-0">
            </button>
            <button @click="submit" type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Submit
            </button>
        </div> -->
        <PostModal :post="createPost" v-model="showModal" />
    </div>
</template>

<style scoped></style>
