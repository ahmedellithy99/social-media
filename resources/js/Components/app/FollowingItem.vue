<script setup>
defineProps({
    image: String,
    title: String,
    username: String
})
</script>

<template>
    <div class="mb-3 cursor-pointer hover:bg-gray-100">
        <a :href="route('profile' , username)">
            <div class="flex items-center gap-2 py-2 px-2">
                <img :src="image" class="w-[32px] rounded-full"/>
                <div>
                    <h3 class="font-black">{{ title }}</h3>
                </div>
            </div>

        </a>
    </div>
</template>

<style scoped>
</style>