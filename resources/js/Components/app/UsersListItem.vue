<script setup>

defineProps({
    users : Array
})
</script>

<template>

    <div v-for="user of users">
        <a :href="route('profile' , user.username)" class="flex items-center gap-2 py-2 px-2"> 
            <img :src="user.avatar_url" class="w-[32px] h-[32px] rounded-full cursor-pointer" /> 
            <div class="flex justify-between flex-1">
                <h3 class="font-black hover:underline cursor-pointer">{{ user.name }}</h3>
            </div>
        </a>
    </div>
</template>