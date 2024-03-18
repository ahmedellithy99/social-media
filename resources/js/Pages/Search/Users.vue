<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UsersListItem from "@/Components/app/UsersListItem.vue";

const props = defineProps({
    users : Array
});


</script>


<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <div class="">
                <div class="shadow bg-white p-3 rounded mb-3">
                    <h2 class="text-lg font-bold mb-5 ml-3">Users</h2>
                    <div>
                        <UsersListItem v-if="users.length"  :users="users"/>
                        <div v-else class="py-8 text-center text-gray-500">
                            No users were found.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
</style>