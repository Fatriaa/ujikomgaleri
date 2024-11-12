<template>
    <AuthenticatedLayout>
        <Head title="Upload Your Photos" />

        <div class="max-w-2xl mx-auto py-12 px-6 bg-black rounded-lg shadow-lg text-white text-center">
            <h1 class="text-3xl font-bold mb-6">Upload Your Photos</h1>
            <p class="text-gray-400 mb-6">Easily upload and manage your photo collection.</p>

            <!-- Drag-and-Drop Area -->
            <div
                @dragover.prevent
                @drop.prevent="handleDrop"
                @click="selectFiles"
                class="border-2 border-dashed border-gray-600 rounded-lg p-4 mb-6 text-center cursor-pointer hover:border-gray-400 transition-all duration-300"
            >
                <p class="text-sm font-semibold text-gray-200">Drag & Drop images here or click to select files</p>
                <input 
                    type="file" 
                    ref="fileInput" 
                    @change="previewSelectedImages" 
                    accept="image/*" 
                    class="hidden" 
                    multiple 
                />
            </div>

            <!-- Clear All Button -->
            <button 
                v-if="previewImages.length" 
                @click="clearAllImages" 
                class="mb-4 py-1 px-3 bg-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-600 transition-colors"
            >
                Clear All
            </button>

            <!-- Preview Section -->
            <div v-if="previewImages.length" class="grid grid-cols-3 gap-3">
                <div 
                    v-for="(image, index) in previewImages" 
                    :key="index" 
                    class="relative bg-gray-800 p-2 rounded-lg shadow-sm transition-transform transform hover:scale-105"
                >
                    <!-- Remove Image Button -->
                    <button 
                        @click="removeImage(index)" 
                        class="absolute top-1 right-1 bg-gray-700 rounded-full p-1 hover:bg-gray-600 transition-colors"
                    >
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                            <path d="M6.225 4.811L4.81 6.226 10.586 12l-5.776 5.774 1.415 1.414L12 13.414l5.774 5.774 1.415-1.414L13.414 12l5.774-5.774-1.414-1.415L12 10.586 6.225 4.811z"></path>
                        </svg>
                    </button>

                    <!-- Image Preview -->
                    <img :src="image.url" alt="Preview" class="w-full h-auto object-contain rounded-md mb-2 shadow-sm">

                    <!-- Title Input -->
                    <input
                        v-model="image.title"
                        type="text"
                        class="w-full bg-gray-900 text-white rounded-lg p-1 text-xs placeholder-gray-500 mb-1 focus:outline-none focus:ring-1 focus:ring-gray-500"
                        placeholder="Add a title"
                    />

                    <!-- Description Input -->
                    <textarea
                        v-model="image.description"
                        class="w-full bg-gray-900 text-white rounded-lg p-1 text-xs placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                        placeholder="Add a description"
                        rows="1"
                    ></textarea>
                </div>
            </div>

            <!-- Upload Button -->
            <button 
                @click="uploadImages" 
                class="w-full mt-6 py-2 bg-gray-700 rounded-lg font-semibold hover:bg-gray-600 transition-colors"
                :disabled="!previewImages.length || uploading"
            >
                {{ uploading ? `Uploading... ${uploadProgress}%` : 'Upload Images' }}
            </button>

            <!-- Progress Bar -->
            <div v-if="uploading" class="mt-4">
                <div class="relative w-full h-2 bg-gray-800 rounded-full overflow-hidden">
                    <div :style="{ width: `${uploadProgress}%` }" class="h-full bg-gray-600 transition-all duration-500"></div>
                </div>
            </div>

            <!-- Success Message -->
            <p v-if="uploadSuccess" class="mt-6 text-gray-400">Images uploaded successfully!</p>

            <!-- Uploaded Images Display -->
            <div v-if="uploadedImages.length" class="mt-12">
                <h2 class="text-2xl font-semibold mb-4">Uploaded Images</h2>
                <div class="grid grid-cols-3 gap-3">
                    <div v-for="(image, index) in uploadedImages" :key="index" class="relative bg-gray-800 rounded-lg p-2">
                        <img :src="image.src" alt="Uploaded Image" class="w-full h-auto object-contain rounded-md mb-2 shadow-sm">
                        <h3 class="text-sm font-bold mb-1 text-gray-200">{{ image.judul }}</h3>
                        <p class="text-xs text-gray-500">{{ image.deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>

        <Transition name="modal">
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="bg-gray-900 rounded-lg p-6 max-w-sm mx-auto text-white shadow-lg flex flex-col items-center">
            <svg class="w-12 h-12 mb-4 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
            </svg>
            <h2 class="text-lg font-bold mb-2">Validation Error</h2>
            <p class="mb-4 text-sm text-gray-300">{{ modalMessage }}</p>
            <button 
                @click="showModal = false" 
                class="mt-4 w-full py-2 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                Close
            </button>
        </div>
    </div>
</Transition>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const previewImages = ref([]);
const uploadedImages = ref([]);
const uploadProgress = ref(0);
const uploading = ref(false);
const uploadSuccess = ref(false);
const fileInput = ref(null);
const showModal = ref(false);
const modalMessage = ref('');
const maxFileSize = 5 * 1024 * 1024; // 5MB

function selectFiles() {
    fileInput.value.click();
}

function previewSelectedImages(event) {
    const files = Array.from(event.target.files);
    files.forEach(file => {
        if (file && file.type.startsWith('image/') && file.size <= maxFileSize) {
            const imageUrl = URL.createObjectURL(file);
            previewImages.value.push({ file, url: imageUrl, title: '', description: '' });
        } else if (file.size > maxFileSize) {
            showModal.value = true;
            modalMessage.value = `File ${file.name} exceeds the maximum size of 5MB.`;
        }
    });
}

function handleDrop(event) {
    const files = Array.from(event.dataTransfer.files);
    files.forEach(file => {
        if (file && file.type.startsWith('image/') && file.size <= maxFileSize) {
            const imageUrl = URL.createObjectURL(file);
            previewImages.value.push({ file, url: imageUrl, title: '', description: '' });
        } else if (file.size > maxFileSize) {
            showModal.value = true;
            modalMessage.value = `File ${file.name} exceeds the maximum size of 5MB.`;
        }
    });
}

function clearAllImages() {
    previewImages.value = [];
}

function removeImage(index) {
    previewImages.value.splice(index, 1);
}

async function uploadImages() {
    // Check if all images have titles and descriptions
    for (const image of previewImages.value) {
        if (!image.title.trim()) {
            modalMessage.value = 'Please fill in the title for all images.';
            showModal.value = true; // Show the modal
            return; // Stop the function if validation fails
        }
        if (!image.description.trim()) {
            modalMessage.value = 'Please fill in the description for all images.';
            showModal.value = true; // Show the modal
            return; // Stop the function if validation fails
        }
    }

    uploading.value = true;
    uploadProgress.value = 0;

    const formData = new FormData();
    previewImages.value.forEach((image) => {
        formData.append('images[]', image.file); // Append the actual file
        formData.append('judul[]', image.title); // Append titles
        formData.append('deskripsi[]', image.description); // Append descriptions
    });

    try {
        const response = await axios.post('/upload-photos', formData, {
            onUploadProgress: (progressEvent) => {
                uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            },
        });

        if (response.data.success) {
            uploadedImages.value = response.data.images; // Assuming your backend returns the uploaded images
            uploadSuccess.value = true;
            clearAllImages(); // Clear preview images after upload
        }
    } catch (error) {
        console.error('Upload failed:', error);
    } finally {
        uploading.value = false;
    }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.modal-enter, .modal-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

</style>
