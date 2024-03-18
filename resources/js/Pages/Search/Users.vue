<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UsersListItem from "@/Components/app/UsersListItem.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, watch } from "vue";
import { debounce } from "lodash";
import { Link, router } from "@inertiajs/vue3";


const props = defineProps({
    users : Object,
    filter : String
});

const search = ref(props.filter);

watch(search , debounce (function(value){
    {
    router.get('/users?search=' + value );
}
} , 1000));


</script>


<template>
    <AuthenticatedLayout>
        <div class="p-4 w-full sm:px-32 ">
            <TextInput v-model="search" 
            class="mb-4" placeholder="Search For Users"></TextInput>
            <div class="">
                <div class="shadow bg-white p-3 rounded mb-3">
                    <h2 class="text-lg font-bold mb-5 ml-3">Users</h2>
                    <div>
                        <UsersListItem v-if="users.data.length" :users="users.data"/>
                        <div v-else class="py-8 text-center text-gray-500">
                            No users were found.
                        </div>
                        
                        <!-- Pagination To be Done Later  -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
</style>