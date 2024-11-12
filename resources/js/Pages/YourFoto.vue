<template>
    <Head title="Your Photos" />
    
    <AuthenticatedLayout>
        <div class="bg-gradient-to-b from-gray-900 via-black to-gray-800 min-h-screen py-12">
            <div class="container mx-auto px-8">
                <h1 class="text-3xl font-bold text-white mb-6">Your Uploaded Photos</h1>
                
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="photo in photos" :key="photo.id" class="relative group">
                        <img :src="photo.src" :alt="photo.alt" class="w-full h-auto object-cover rounded-lg shadow-md" />
                        <div class="absolute top-0 right-0 p-2">
                            <button @click="openEditModal(photo)" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                           <button @click="deletePhoto(photo.id)" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <EditPhotoModal
            v-if="isEditModalOpen"
            :isOpen="isEditModalOpen"
            :photo="currentPhoto"
            @close="closeEditModal"
            @save="savePhoto"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EditPhotoModal from '@/Components/EditPhotoModal.vue';

const props = defineProps({
    photos: Array,
});

const isEditModalOpen = ref(false);
const currentPhoto = ref(null);

const openEditModal = (photo) => {
    currentPhoto.value = photo;
    isEditModalOpen.value = true;
};

const closeEditModal = () => {
    isEditModalOpen.value = false;
    currentPhoto.value = null;
};

const savePhoto = (updatedPhoto) => {
    // Handle the photo save here
    // This should include making an API request to save the updated photo data
    console.log("Saving photo", updatedPhoto);
    // You can replace this with an actual request to save the photo data
};
// Delete Photo method
const deletePhoto = async (photoId) => {
    if (confirm('Are you sure you want to delete this photo?')) {
        try {
            const response = await fetch(`/photos/delete/${photoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            });

            if (response.ok) {
                // Remove the photo from the local list after successful deletion
                props.photos = props.photos.filter(photo => photo.id !== photoId);
                console.log('Photo deleted successfully');
            } else {
                console.error('Failed to delete photo');
            }
        } catch (error) {
            console.error('Error deleting photo:', error);
        }
    }
};
</script>


<style scoped>
/* Add any additional styles if needed */
</style>
