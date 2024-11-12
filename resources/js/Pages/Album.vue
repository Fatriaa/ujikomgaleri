<template>
    <div class="album-container">
        <h2 class="album-title">My Album</h2>
        <div class="album-gallery">
            <div v-for="(image, index) in albumImages" :key="index" class="album-item">
                <img :src="image.src" :alt="image.alt" class="album-image" />
                <h4>{{ image.judul }}</h4>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const albumImages = ref([]);

onMounted(async () => {
    try {
        const response = await axios.get('/albums');
        albumImages.value = response.data;
    } catch (error) {
        console.error('Error fetching album images:', error);
    }
});
</script>

<style scoped>
/* Album styling */
.album-container {
    padding: 20px;
}
.album-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.album-item {
    width: 200px;
}
.album-image {
    width: 100%;
    height: auto;
}
</style>
