<script setup>
import {computed, ref  } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Link, router, usePage} from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';



const showingNavigationDropdown = ref(false);
const showingNotificationDropdown = ref(false);
const showProfileDropdown = ref(false); 
const keywords = ref('');
const authUser = usePage().props.auth.user ;

const props = defineProps({
    notifications : Array
})

function search()
{
    router.get('/users?search='+ keywords.value);
}


</script>

<template>


    
    <div class="overflow-auto" >   
        <div class="h-screen overflow-hidden flex flex-col bg-gray-100 dark:bg-gray-900">
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-4 h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                </Link>
                            </div>

                            
                        </div>
                        <div class="flex-1">
                            <TextInput v-model="keywords" placeholder="Search on the website" class="w-full"
                                    @keyup.enter="search"/>
                        </div>
                        
                        <div v-if="authUser" class="hidden sm:flex sm:items-center ">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative flex">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ authUser.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile' , {username: authUser.username})"> Profile </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>

                                <div v-if="authUser" class="hidden sm:flex sm:items-center sm:ms-6 relative">
                                <!-- Profile Dropdown -->
                                <div>
                                    <button
                                        @click="showProfileDropdown = !showProfileDropdown"
                                        type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                        aria-haspopup="true"
                                        aria-expanded="true"
                                    >
                                        
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Adjust the SVG icon for your notification icon -->
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 18v-6a9 9 0 0118 0v6"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"></path>
                                    </svg>  
                                    </button>

                                    <!-- Profile Dropdown Content -->
                                    <div v-if="showProfileDropdown" class="h-48 w-64 origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-auto" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                        <div class="py-1" role="none">
                                            <div v-for="notification of notifications" class="flex items-center justify-center">
                                            <img :src="notification.data.avatar_url" class="aspect aspect-square max-w-8 max-h-8 rounded-full"/>
                                            <DropdownLink v-if="notification.data.post_id" :href="route('post.show' , notification.data.post_id)"> {{ notification.data.text }} </DropdownLink>
                                            <DropdownLink v-if="notification.data.username" :href="route('profile' , notification.data.username)"> {{ notification.data.text }} </DropdownLink>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                
                                
                            </div>
                        </div>

                        <div v-else>
                            <Link :href="route('login')">
                                Login
                            </Link>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNotificationDropdown = !showingNotificationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                        >
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Adjust the SVG icon for your notification icon -->
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 18v-6a9 9 0 0118 0v6"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"></path>
                            </svg>
                        </button>
                        </div>

                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div v-if="authUser"
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                
                    

                    <!-- Responsive Settings Options -->
                    <div  class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                {{ authUser.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ authUser.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile' , {username: authUser.username})"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                        <div>
                        </div>
                    </div>
                </div>

                <div v-if="showingNotificationDropdown" class="sm:hidden">
                    <!-- Content for notifications dropdown -->
                    <div class="h-48 pt-4 pb-1 border-t border-gray-200 dark:border-gray-600 overflow-auto">
                        <!-- Notification content here -->
                        <!-- For example: -->
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                Notifications
                            </div>
                            <div class="mt-3 space-y-1">
                                <!-- Notification items -->
                                <div v-for="notification of notifications" class="flex items-center justify-center">
                                    <img :src="notification.data.avatar_url" class="aspect aspect-square max-w-8 max-h-8 rounded-full"/>
                                    <DropdownLink v-if="notification.data.post_id" :href="route('post.show' , notification.data.post_id)"> {{ notification.data.text }} </DropdownLink>
                                    <DropdownLink v-if="notification.data.username" :href="route('profile' , notification.data.username)"> {{ notification.data.text }} </DropdownLink>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white dark:bg-gray-800 shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-hidden">
                <slot />
            </main>
        </div>
    </div>
</template>
