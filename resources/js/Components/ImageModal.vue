<template>
  <div v-if="isVisible" class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3 class="modal-title">{{ image.judul }}</h3>
        <button @click="$emit('close')" class="close-btn">✖</button>
      </div>

      <div class="modal-body">
        <!-- Image Section -->
        <div class="image-container">
          <img v-if="image.src" :src="image.src" :alt="image.judul" class="modal-image" />
        </div>

        <!-- Description and Profile Section -->
        <div class="image-description">
          <p>{{ image.deskripsi }}</p>
        </div>

        <div class="profile-info">
          <img
            v-if="image.user.profile_picture"
            :src="image.user.profile_picture"
            alt="Profile"
            class="profile-avatar"
          />
          <div>
            <p class="username">{{ image.user.username }}</p>
            <p class="location">{{ image.user.location || 'Unknown' }}</p>
          </div>
        </div>

        <!-- Like and Comment Buttons -->
        <div class="actions">
          <button @click="toggleLike" class="like-btn">
            {{ likedPhotos[image.id] ? '❤️' : '🤍' }} Like
          </button>
          <button @click="openCommentSection" class="comment-btn">💬 Comment</button>
        </div>
      </div>

      <!-- Comment Section (Positioned to the right) -->
      <div v-if="isCommentSectionOpen" class="comments-section-right">
        <div v-if="isLoading" class="loading-text">Loading...</div>
        <div v-else>
          <div v-for="comment in comments" :key="comment.id" class="comment-item">
            <div class="comment-content">
              <p class="comment-text">{{ comment.text }}</p>
            </div>
            <div class="comment-avatar-container">
              <img
                v-if="comment.user.profile_picture"
                :src="comment.user.profile_picture"
                alt="Profile"
                class="comment-avatar"
              />
            </div>
          </div>

          <div class="comment-input-container">
            <textarea v-model="newComment" class="comment-input" placeholder="Add a comment"></textarea>
            <button @click="addComment" class="add-comment-btn">Add</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, watch } from 'vue';
import axios from 'axios';

const props = defineProps({ image: Object, isVisible: Boolean });
const likedPhotos = ref(JSON.parse(localStorage.getItem('likedPhotos') || '{}'));
const comments = ref([]);
const newComment = ref('');
const isLoading = ref(false);
const isCommentSectionOpen = ref(false);

watch(() => props.isVisible, async (newVal) => {
  if (newVal && props.image?.id) {
    await fetchLikeStatus();
    fetchComments();
  }
});

const fetchLikeStatus = async () => {
  const localLikedPhotos = JSON.parse(localStorage.getItem('likedPhotos') || '{}');
  likedPhotos.value = localLikedPhotos;
};

const toggleLike = async () => {
  if (!props.image?.id) return;

  try {
    const response = await axios.post(`/photos/${props.image.id}/like`);
    likedPhotos.value[props.image.id] = response.data.liked;

    localStorage.setItem('likedPhotos', JSON.stringify(likedPhotos.value));
  } catch (error) {
    console.error('Error liking photo:', error);
  }
};

const addComment = async () => {
  if (newComment.value.trim()) {
    try {
      const response = await axios.post('/comments', { photo_id: props.image.id, text: newComment.value });
      comments.value.push(response.data);
      newComment.value = '';
    } catch (error) {
      console.error('Error adding comment:', error);
    }
  }
};

const fetchComments = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(`/photos/${props.image.id}/comments`);
    comments.value = response.data;
  } catch (error) {
    console.error('Error fetching comments:', error);
  } finally {
    isLoading.value = false;
  }
};

const openCommentSection = () => {
  isCommentSectionOpen.value = !isCommentSectionOpen.value;
};
</script>

<style scoped>
/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

/* Modal Content */
.modal-content {
  background-color: #fff;
  padding: 15px;
  border-radius: 10px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
}

/* Modal Header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-title {
  font-size: 18px;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

/* Image Section */
.image-container {
  text-align: center;
}

.modal-image {
  width: 100%;
  height: auto;
  max-height: 300px;
  object-fit: cover;
  border-radius: 8px;
  margin: 10px 0;
}

/* Description Section */
.image-description {
  margin: 10px 0;
  font-size: 14px;
  color: #666;
  text-align: center;
}

/* Profile Info */
.profile-info {
  display: flex;
  align-items: center;
  margin: 15px 0;
}

.profile-avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 10px;
}

.username {
  font-weight: bold;
}

.location {
  font-size: 12px;
  color: #777;
}

/* Actions */
.actions {
  display: flex;
  justify-content: space-between;
  margin-top: 15px;
}

.like-btn, .comment-btn {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.like-btn:hover, .comment-btn:hover {
  background-color: #2980b9;
}

/* Comments Section (Right-Aligned) */
.comments-section-right {
  position: absolute;
  top: 50px; /* Adjust to where you want it to appear */
  right: 0;
  width: 300px; /* Adjust size as needed */
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 10px;
  max-height: 400px;
  overflow-y: auto;
}

.comment-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.comment-avatar-container {
  flex-shrink: 0;
}

.comment-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  object-fit: cover;
  margin-left: 10px;
}

.comment-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.comment-text {
  font-size: 13px;
  color: #333;
}

/* Comment Input */
.comment-input-container {
  margin-top: 15px;
}

.comment-input {
  width: 100%;
  padding: 8px;
  font-size: 14px;
  border-radius: 6px;
  border: 1px solid #ccc;
  resize: vertical;
}

.add-comment-btn {
  background-color: #27ae60;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
}

.add-comment-btn:hover {
  background-color: #2ecc71;
}

.loading-text {
  text-align: center;
  color: #f39c12;
  font-size: 14px;
}
</style>
