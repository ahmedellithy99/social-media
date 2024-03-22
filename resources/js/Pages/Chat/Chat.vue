<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    chat: Object,
});

const authUser = usePage().props.auth.user.id;
const chat_id = props.chat[0]["id"];
const form = useForm({
    body: "",
    chat_id: chat_id,
});

const msg = ref("");

function sendMessage() {
    form.body = msg.value;
    form.post(route("chat.store"), {
        preserveScroll: true,
        onSuccess: () => {
            msg.value = "";
        },
    });
}
</script>

<template>
    <section class="container mx-auto my-8 p-4 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4">Conversation Chat</h2>
        <div class="overflow-y-auto h-80 border rounded-lg border-gray-300 p-4">
            <!-- Chat Messages -->
            <div
                v-for="message of chat[0]['messages']"
                class="flex flex-col space-y-4"
            >
                <!-- Message from sender -->

                <div
                    v-if="authUser == message.sender_id"
                    class="flex items-center justify-end mb-1"
                >
                    <div
                        class="bg-blue-500 text-white py-2 px-4 rounded-lg max-w-xs"
                    >
                        {{ message.body }}
                    </div>
                </div>
                <!-- Message from receiver -->
                <div v-else class="flex items-center">
                    <div
                        class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg max-w-xs"
                    >
                        {{ message.body }}
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
</template>
