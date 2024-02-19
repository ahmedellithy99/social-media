<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {TabGroup, TabList, Tab, TabPanels, TabPanel} from '@headlessui/vue';
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import { usePage , Head , useForm } from '@inertiajs/vue3';
import { computed , ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Edit from '@/Pages/Profile/Edit.vue';

const props = defineProps(
    {
        user : Object
        ,
        status : String
    }
)


const imageForm = useForm({
    cover : null , 
    avatar : null
});


const authUser = usePage().props.auth.user ;

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const coverImageSrc = ref('');

const showNotification = ref(true);





function onCoverChange(event) 
{
    imageForm.cover = event.target.files[0];

    if(imageForm.cover)
    {
        const reader = new FileReader()
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        }

        reader.readAsDataURL(imageForm.cover)
    }

}

function cancelCoverImage() {
    imageForm.cover = null;
    coverImageSrc.value = null
}

function submitCoverImage()
{
    imageForm.post(route('update.image'),
    {
        onSuccess: (user) => {
            cancelCoverImage()
            setTimeout(() => {
                showNotification.value = false
            }, 3000)
        },
    });
}

</script>

<template>

    <Head title="Alo"/>
    
    <AuthenticatedLayout>
        
        
        <div class="max-w-[768px] mx-auto h-full overflow-auto">

                <div class="relative bg-white">
                    <div
                            v-show="showNotification && status === 'cover-image-update'"
                            class="my-2 py-2 px-3 font-medium text-sm bg-emerald-500 text-white"
                        >
                            Your cover image has been updated
                        </div>
                        <div
                            v-if="$page.props.errors.cover"
                            class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white"
                        >
                            {{ $page.props.errors.cover }}
                        </div>
                    <div class="group">
                        <img :src=" coverImageSrc || user.cover_url || '/img/default-cover.jpg'" class="w-full h-[200px] object-cover rounded " >

                        <div v-if="!coverImageSrc" class="absolute top-2 right-2"> 
                            <div v-if="authUser.id == user.id">

                                <button class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs flex items-center opacity-0 group-hover:opacity-100 ">
                                    Update Cover
                                </button>
                                <input type="file" class="absolute top-0 left-0 right-0 bottom-0 opacity-0"
                                    @change="onCoverChange"/>
                            </div>
                        </div>  

                        <div v-else class="flex absolute top-2 right-2 gap-3 " >
                            <button @click="cancelCoverImage" class=" bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs  ">
                                Cancel
                            </button>
                            <button @click="submitCoverImage" class=" bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs  ">
                                Submit
                            </button>
                        </div>
                    </div>
                    
                                    
                    <div class="flex">
                        <img src="https://cdn.iconscout.com/icon/free/png-256/free-avatar-370-456322.png?f=webp"
                            class="ml-[48px] w-[128px] h-[128px] -mt-[64px]">
                        <div class="flex justify-between items-center flex-1 p-4">
                            <h2 class="font-bold text-lg">{{user.name}}</h2>
                            <div v-if="isMyProfile">
                                <PrimaryButton>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="w-4 h-4 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                                    </svg>
                                    Edit
                                </PrimaryButton>
                            </div>
                            
                        </div>
                    </div>
                    <div class="border-t">
                        <TabGroup>
                            <TabList class="flex bg-white">
                                
                                <Tab v-slot="{ selected }" as="template">
                                    <TabItem text="Posts" :selected="selected"/>
                                </Tab>
                                <Tab v-slot="{ selected }" as="template">
                                    <TabItem text="Followers" :selected="selected"/>
                                </Tab>
                                <Tab v-slot="{ selected }" as="template">
                                    <TabItem text="Followings" :selected="selected"/>
                                </Tab>
                                <Tab v-slot="{ selected }" as="template">
                                    <TabItem text="Photos" :selected="selected"/>
                                </Tab>
                                <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
                                    
                                    <TabItem text="About" :selected="selected"/>
                                </Tab>
                            </TabList>
                            <TabPanels class="mt-2">
                                
                                <TabPanel class="bg-white p-3 shadow">
                                    Posts
                                </TabPanel>
                                <TabPanel class="bg-white p-3 shadow">
                                    Followers
                                </TabPanel>
                                <TabPanel class="bg-white p-3 shadow">
                                    Followings
                                </TabPanel>
                                <TabPanel class="bg-white p-3 shadow">
                                    Photos
                                </TabPanel>
                                <TabPanel v-if="isMyProfile" >
                                    <Edit /> 
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
                    </div>
                </div>
        </div>
    </AuthenticatedLayout>
    
</template>