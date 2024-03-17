<script setup>
import TextInput from "@/Components/TextInput.vue";
import FollowingItem from "@/Components/app/FollowingItem.vue";
import {ref} from "vue";
const searchKeyword = ref('')

defineProps({
    followings : Array
})

</script>

<template>

    <TextInput :model-value="searchKeyword" placeholder="Type to search" class="w-full mt-3"/>
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            You don't have friends yet.
        </div>
        <div v-else>
            <FollowingItem v-for="following of followings" :image="following.avatar_url"
                        :title="following.name"
                        :username="following.username"/>
            
           
        </div>
    </div>
</template>

<style scoped>
</style>