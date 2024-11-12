<template>
    <Head title="Photo Gallery" />

    <AuthenticatedLayout>
        <div class="bg-gradient-to-b from-gray-900 via-black to-gray-900 min-h-screen py-12">
            <div class="container mx-auto px-8">

                <!-- Search Bar Toggle Button (Fixed) -->
                <div class="fixed top-6 right-8 z-50">
                    <button 
                        @click="toggleSearch" 
                        class="p-3 bg-transparent text-white border-2 border-white rounded-full focus:outline-none hover:bg-white hover:text-black transition-all duration-300"
                    >
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.41-1.41l4.28 4.29a1 1 0 01-1.42 1.42l-4.28-4.3zM8 14A6 6 0 108 2a6 6 0 000 12z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <!-- Search Bar -->
                <div v-if="isSearchVisible" class="sticky top-0 left-0 right-0 z-40 py-4 transition-all duration-300 ease-in-out  from-gray-900 via-black to-gray-900">
                    <div class="flex justify-center">
                        <div class="relative w-full max-w-md">
                            <input 
                                v-model="searchQuery" 
                                type="text" 
                                placeholder="Search photos..." 
                                class="w-full p-4 pl-10 pr-4 rounded-full bg-transparent text-white placeholder-gray-400 border-2 border-white/30 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 ease-in-out transform hover:scale-105 focus:scale-105"
                            />
                            <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-blue-500 transition-transform duration-300 transform hover:scale-110" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.41-1.41l4.28 4.29a1 1 0 01-1.42 1.42l-4.28-4.3zM8 14A6 6 0 108 2a6 6 0 000 12z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Masonry Grid with Hover Effects -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 mt-20">
                    <div 
                        v-for="photo in filteredPhotos" 
                        :key="photo.id" 
                        class="relative overflow-hidden rounded-lg shadow-2xl group transform transition duration-500 hover:scale-105 hover:shadow-blue-500 hover:shadow-lg"
                        @click="openModal(photo)"
                        @mousemove="handleTilt($event)"
                        @mouseleave="resetTilt"
                    >
                        <img 
                            :src="photo.src" 
                            :alt="photo.alt" 
                            loading="lazy" 
                            class="w-full h-72 object-cover transition-transform duration-500 transform group-hover:scale-110"
                        />
                    </div>
                </div>

                <!-- Modal -->
                <ImageModal 
                    :image="selectedPhoto" 
                    :isVisible="isModalVisible" 
                    @close="closeModal" 
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ImageModal from '@/Components/ImageModal.vue';

const searchQuery = ref('');
const photos = ref([]);
const selectedPhoto = ref(null);
const isModalVisible = ref(false);
const isSearchVisible = ref(false);

onMounted(async () => {
    try {
        const response = await axios.get('/photos');
        photos.value = response.data;
    } catch (error) {
        console.error('Error fetching photos:', error);
    }
});

const filteredPhotos = computed(() => {
    if (!searchQuery.value) return photos.value;
    return photos.value.filter(photo =>
        photo.judul.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const openModal = (photo) => {
    selectedPhoto.value = photo;
    isModalVisible.value = true;
};

const closeModal = () => {
    isModalVisible.value = false;
    selectedPhoto.value = null;
};

const handleTilt = (event) => {
    const { target } = event;
    const rect = target.getBoundingClientRect();
    const x = ((event.clientX - rect.left) / rect.width - 0.5) * 15;
    const y = ((event.clientY - rect.top) / rect.height - 0.5) * 15;
    target.style.transform = `rotateX(${y}deg) rotateY(${x}deg) scale(1.05)`;
};

const resetTilt = (event) => {
    event.target.style.transform = "rotateX(0) rotateY(0) scale(1)";
};

const toggleSearch = () => {
    isSearchVisible.value = !isSearchVisible.value;
};
</script>

<style scoped>
.bg-gradient-to-b {
    background: linear-gradient(to bottom, #1f2937, #0a0a0a, #1f2937);
}

input[type="text"]::placeholder {
    color: rgba(255, 255, 255, 0.8);
}

input[type="text"]:focus {
    outline: none;
    transform: scale(1.05);
}

.grid {
    display: grid;
    gap: 2rem;
}

.shadow-blue-500 {
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.5);
}

.fixed {
    position: fixed;
}

.mt-20 {
    margin-top: 5rem; /* Adjust to your preferred space */
}
</style>
