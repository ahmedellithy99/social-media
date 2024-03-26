<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUpdated } from "vue";

const props = defineProps({
    chat: Object,
    sender: Object,
    recipient: Object,
    notifications: Array,
    chats: Array,
    countUnReads: Number,
    countUnReadChats: Number,
});

const lastMessage = props.chat["messages"][props.chat["messages"].length - 1];
const authUser = usePage().props.auth.user.id;
const chat_id = props.chat["id"];
const form = useForm({
    body: "",
    chat_id: chat_id,
});

if (lastMessage) {
    if (lastMessage["read"] == 0 && lastMessage["sender_id"] != authUser) {
        props.countUnReadChats--;
        router.post(route("chat.markAsRead", lastMessage["id"]));
    }
}

const msg = ref("");

function sendMessage() {
    form.body = msg.value;
    msg.value = "";
    form.post(route("chat.store"), {
        preserveScroll: true,
        onSuccess: () => {
            msg.value = "";
        },
    });
}
const chatContainer = ref(null);

onMounted(scrollToBottom);
onUpdated(scrollToBottom);

function scrollToBottom() {
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
}

Echo.join("chat." + chat_id).listen("SendMessage", (e) => {
    props.chat.messages.push(e["message"]);
});
</script>

<template>
    <AuthenticatedLayout
        :notifications="notifications"
        :chats="chats"
        :countUnReads="countUnReads"
        :countUnReadChats="countUnReadChats"
    >
        <section
            class="container mx-auto mt-24 p-4 bg-white rounded-lg shadow-lg"
        >
            <h2 class="text-2xl font-bold mb-4">
                <a :href="route('profile', recipient.username)">{{
                    recipient.name
                }}</a>
            </h2>
            <div
                class="overflow-y-auto h-96 border rounded-lg border-gray-300 p-4"
                ref="chatContainer"
            >
                <!-- Chat Messages -->

                <div
                    v-for="message of chat['messages']"
                    :key="message.id"
                    class="flex flex-col space-y-4"
                >
                    <!-- Message from sender -->
                    <div
                        v-if="authUser == message.sender_id"
                        class="flex items-start justify-end mb-1"
                    >
                        <div class="flex flex-col items-end">
                            <div
                                class="bg-blue-500 text-white py-2 px-4 rounded-lg max-w-sm"
                            >
                                <span>{{ message.body }}</span>
                            </div>
                            <span class="text-xs text-gray-400 mt-1">{{
                                message.created_at
                            }}</span>
                        </div>
                        <img
                            :src="sender.avatar_url"
                            alt="User Avatar"
                            class="w-8 h-8 ml-2 rounded-full"
                        />
                    </div>
                    <!-- Message from receiver -->
                    <div v-else class="flex items-start mb-1">
                        <img
                            :src="recipient.avatar_url"
                            alt="User Avatar"
                            class="w-8 h-8 mr-2 rounded-full"
                        />
                        <div class="flex flex-col">
                            <div
                                class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg max-w-sm"
                            >
                                <span>{{ message.body }}</span>
                            </div>
                            <span class="text-xs text-gray-400 mt-1">{{
                                message.created_at
                            }}</span>
                        </div>
                    </div>
                    <!-- Add more messages as needed -->
                </div>
            </div>

            <!-- Message Input -->
            <form class="mt-4">
                <div class="flex">
                    <input
                        v-model="msg"
                        type="text"
                        class="flex-1 border rounded-l-lg border-gray-300 p-2 focus:outline-none"
                        placeholder="Type your message..."
                    />
                    <button
                        @click.prevent="sendMessage"
                        type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 focus:outline-none"
                    >
                        Send
                    </button>
                </div>
            </form>
        </section>
    </AuthenticatedLayout>
</template>
