<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-2xl font-bold mb-4">Edit Photo</h2>
            
            <div class="mb-4">
                <label for="photo-title" class="block text-sm font-semibold text-gray-700">Title</label>
                <input 
                    v-model="editedPhoto.title"
                    id="photo-title" 
                    type="text" 
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
            
            <div class="mb-4">
                <label for="photo-description" class="block text-sm font-semibold text-gray-700">Description</label>
                <textarea 
                    v-model="editedPhoto.description" 
                    id="photo-description"
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="3"
                ></textarea>
            </div>
            
            <div class="mb-4">
                <label for="photo-image" class="block text-sm font-semibold text-gray-700">Change Image</label>
                <input 
                    @change="handleImageUpload" 
                    type="file" 
                    id="photo-image" 
                    accept="image/*"
                    class="mt-1"
                />
                <div v-if="imagePreview" class="mt-2">
                    <img :src="imagePreview" alt="Image Preview" class="w-32 h-32 object-cover rounded-md" />
                </div>
            </div>
            
            <div class="flex justify-between mt-4">
                <button @click="close" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button @click="save" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    isOpen: Boolean,
    photo: Object,
});

const emit = defineEmits(['close', 'save']);

const editedPhoto = ref({
    title: '',
    description: '',
    image: null,
});

const imagePreview = ref(null);

watch(
    () => props.photo,
    (newPhoto) => {
        if (newPhoto) {
            editedPhoto.value = { ...newPhoto };
            imagePreview.value = newPhoto.src;
        }
    },
    { immediate: true }
);

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
            editedPhoto.value.image = file;
        };
        reader.readAsDataURL(file);
    }
};

const close = () => {
    emit('close');
};

const save = async () => {
    const formData = new FormData();
    formData.append('title', editedPhoto.value.title);
    formData.append('description', editedPhoto.value.description);
    
    // Append the image if available
    if (editedPhoto.value.image) {
        formData.append('image', editedPhoto.value.image);
    }

    try {
        const response = await fetch(`/photos/update/${editedPhoto.value.id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData,
        });

        if (response.ok) {
            const result = await response.json();
            console.log('Photo updated successfully:', result);
            emit('save', result.photo); // Emit updated photo to parent component
            close();
        } else {
            console.error('Failed to update photo');
        }
    } catch (error) {
        console.error('Error updating photo:', error);
    }
};

</script>

<style scoped>
/* Modal styling can be customized here */
</style>
