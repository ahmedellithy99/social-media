<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import {TabGroup, TabList, Tab, TabPanels, TabPanel} from '@headlessui/vue';
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import { usePage , Head , useForm, router } from '@inertiajs/vue3';
import { computed , ref, watch } from 'vue';
import {XMarkIcon, CheckCircleIcon, CameraIcon} from '@heroicons/vue/24/solid'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Edit from '@/Pages/Profile/Edit.vue';
import CreatePost from '@/Components/app/CreatePost.vue';
import PostList from '@/Components/app/PostList.vue';
import ImageModal from '@/Components/app/ImageModal.vue';
import UsersListItem from '@/Components/app/UsersListItem.vue';
import TextInput from '@/Components/TextInput.vue';
import { debounce } from 'lodash';


const authUser = usePage().props.auth.user ;

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const coverImageSrc = ref('');
const avatarImageSrc = ref('');
const showNotification = ref(true);
const showProfilePhoto = ref(false);
const showCoverPhoto = ref(false);


const props = defineProps(
    {
        user : Object
        ,
        success : String
        ,
        followersCount : Number,
        
        followingsCount : Number,

        followers : Array,

        followings : Array,
        
        isFollowing: Boolean,

        posts : Array,

        filter : String, 

        notifications : Array


    }
)

const imagesForm = useForm({
    cover : null , 
    avatar : null
});

function openProfilePhoto()
{
    showProfilePhoto.value = true;
}
function openCoverPhoto()
{
    showCoverPhoto.value = true;
}


function onAvatarChange(event) {
    imagesForm.avatar = event.target.files[0]
    if (imagesForm.avatar) {
        const reader = new FileReader()
        reader.onload = () => {
            avatarImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.avatar)
    }
}


function onCoverChange(event) 
{
    imagesForm.cover = event.target.files[0];

    if(imagesForm.cover)
    {
        const reader = new FileReader()
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        }

        reader.readAsDataURL(imagesForm.cover)
    }

}

function resetCoverImgae() {
    imagesForm.cover = null;
    coverImageSrc.value = null
}

function resetAvatarImage() {
    imagesForm.avatar = null;
    avatarImageSrc.value = null
}

function submitCoverImage()
{
    imagesForm.post(route('update.image'),
    {
        onSuccess: (user) => {
            resetCoverImgae()
            setTimeout(() => {
                showNotification.value = false
            }, 3000)
        },
    });
}


function submitAvatarImage() {
    imagesForm.post(route('update.image'), {
        onSuccess: (user) => {
            resetAvatarImage()
            setTimeout(() => {
                showNotification.value = false
            }, 3000)
        },
    })
}

function follow()
{
    const form = useForm({});

    form.post(route('user.follow', props.user.id), {
        preserveScroll: true
    });

    props.isFollowing = true;
}

function unFollow()
{
    const form = useForm({});

    form.post(route('user.unfollow', props.user.id), {
        preserveScroll: true
    });

    props.isFollowing = false;
}

const search = ref(props.filter);

watch(search , debounce (function(value){
    {
    router.get('/u/'+ props.user.username +'?search=' + value );
}
} , 1500));


</script>

<template>


    <Head title="Alo"/>
    <AuthenticatedLayout :notifications="notifications" >
        
        <ImageModal v-model="showCoverPhoto"  :photo="user.cover_url"/>
        <div class="max-w-[768px] mx-auto h-full overflow-auto px-6">

                <div class="relative bg-white">
                    <!-- Notification Session -->
                    <div
                            v-show="showNotification && success"
                            class="my-2 py-2 px-3 font-medium text-sm bg-emerald-500 text-white"
                        >
                            {{success}}
                    </div>
                    <!-- Error  -->
                    <div
                        v-if="$page.props.errors.cover"
                        class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white"
                    >
                        {{ $page.props.errors.cover }}
                    </div>
                    <!-- Cover  -->
                    <div class="group">
                        <img @click="openCoverPhoto" :src=" coverImageSrc || user.cover_url || '/img/default-cover.jpg'" class="w-full h-[200px] object-cover rounded cursor-pointer z-30" >

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
                            <button @click="resetCoverImgae" class=" bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs  ">
                                Cancel
                            </button>
                            <button @click="submitCoverImage" class=" bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs  ">
                                Submit
                            </button>
                        </div>
                    </div>
                    
                    
                    <div class="flex items-center">
                        <!-- Profile Photo -->
                        <div class="flex items-center justify-center relative group/avatar -mt-[64px] ml-[48px] w-[128px] h-[128px] rounded-full">
                            <img @click="openProfilePhoto" :src="avatarImageSrc || user.avatar_url || '/img/avatar.png'"
                            class="w-full h-full object-cover rounded-full cursor-pointer">
                            <ImageModal v-model="showProfilePhoto"  :photo="user.avatar_url"/>
                            
                            <div v-if="authUser.id == user.id">
                                <button
                                    v-if="!avatarImageSrc "
                                    class="absolute left-0 top-0 right-0 bottom-0 bg-black/50 text-gray-200 rounded-full opacity-0 flex items-center justify-center group-hover/avatar:opacity-100">
                                    <CameraIcon class="w-8 h-8"/>
    
                                    <input type="file" class="absolute left-0 top-0 bottom-0 right-0 opacity-0"
                                        @change="onAvatarChange"/>
                                </button>
                                <div v-else class="absolute top-1 right-0 flex flex-col gap-2">
                                    <button
                                        @click="resetAvatarImage"
                                        class="w-7 h-7 flex items-center justify-center bg-red-500/80 text-white rounded-full">
                                        <XMarkIcon class="h-5 w-5"/>
                                    </button>
                                    <button
                                        @click="submitAvatarImage"
                                        class="w-7 h-7 flex items-center justify-center bg-emerald-500/80 text-white rounded-full">
                                        <CheckCircleIcon class="h-5 w-5"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Under Cover literally -->
                        <div class="flex justify-between items-center flex-1 p-4">
                            <div>
                                <h2 class="font-bold text-lg">{{ user.name }}</h2>
                                <p class="text-xs text-gray-500">{{followersCount}} follower(s)</p>
                                <p class="text-xs text-gray-500">{{followingsCount}} following(s)</p>
                            </div>
                            <div v-show="isMyProfile">
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
                        <!-- Follow Button -->
                        <div class="my-auto">
                            <PrimaryButton @click="follow" v-if="!isFollowing && authUser.id != user.id"> Follow </PrimaryButton>
                        </div>
                        <!-- Unfollow Button -->
                        <div class="my-auto">
                            <DangerButton @click="unFollow" v-if="isFollowing && authUser.id != user.id"> UnFollow </DangerButton>
                        </div>

                        



                    </div>
                    <!-- Tabs -->
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
                                    <TextInput v-model="search" 
                                    class="mb-4" placeholder="Search For Posts"/>
                                    <CreatePost v-if="authUser.id == user.id"/>
                                    <PostList class="flex-1" :posts="posts" />
                                </TabPanel>
                                <TabPanel class="bg-white p-3 shadow">
                                    <UsersListItem :users="followers" />
                                </TabPanel>
                                <TabPanel class="bg-white p-3 shadow">
                                    <UsersListItem :users="followings" />
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