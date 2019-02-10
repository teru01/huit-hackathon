<template>
  <div v-if="photo" class="photo-detail" :class="{ 'photo-detail--column': fullWidth }">
    <figure class="photo-detail__pane photo-detail__image" @click="fullWidth = ! fullWidth">
      <img :src="photo.url" alt="">
      <figcaption>Posted by {{ photo.owner.name }}</figcaption>
    </figure>
    <div class="photo-detail__pane">
      <button class="button button--like"
      :class="{ 'button--liked': photo.liked_by_user }"
      title="Like photo"
      @click="onLikeClick">
        <i class="icon ion-md-heart"></i>{{ photo.likes_count }}
      </button>
      <a
        :href="`/photos/${photo.id}/download`"
        class="button"
        title="Download photo"
      >
        <i class="icon ion-md-arrow-round-down"></i>Download
      </a>
      <h2 class="photo-detail__title">
        <i class="icon ion-md-chatboxes"></i>Comments
      </h2>
      <ul v-if="photo.comments.length > 0" class="photo-detail__comments">
        <li
          v-for="comment in photo.comments"
          :key="comment.content"
          class="photo-detail__commentItem"
        >
          <p class="photo-detail__commentBody">
            {{ comment.content }}
          </p>
          <p class="photo-detail__commentInfo">
            {{ comment.author.name }}
          </p>
        </li>
      </ul>
      <p v-else>No comments yet.</p>
      <form v-if="isLogin" @submit.prevent="addComment" class="form">
        <div v-if="commentErrors" class="errors">
          <ul v-if="commentErrors.content">
            <li v-for="msg in commentErrors.content" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <textarea class="form__item" v-model="commentContent"></textarea>
        <div class="form__button">
          <button type="submit" class="button button--inverse">submit comment</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { OK, CREATED, UNPROCESSABLE_ENTITY, isStatusOk } from '../util'
import Axios from 'axios'

export default {
  props: {
    id: {
      type: String,
      require: true
    }
  },

  data () {
    return {
      photo: null,
      fullWidth: false,
      commentContent: '',
      commentErrors: null
    }
  },

  computed: {
    isLogin () {
      return this.$store.getters['auth/check']
    }
  },

  methods: {
    async fetchPhoto () {
      const response = await Axios.get(`/api/photos/${this.id}`)
      if (!isStatusOk(response)) return false

      this.photo = response.data
    },

    async addComment () {
      const response = await Axios.post(`/api/photos/${this.id}/comments`, {'content': this.commentContent})

      // バリデーションエラー
      if (response.status === UNPROCESSABLE_ENTITY) {
        this.commentErrors = response.data.errors
        return false
      }

      this.commentErrors = null
      this.commentContent = ''

      if (response.status !== CREATED) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      this.$set(this.photo, 'comments', [
        response.data,
        ...this.photo.comments
      ])
    },

    onLikeClick () {
      if (!this.isLogin) {
        alert('いいねするにはログインしてください')
        return false
      }

      if (this.photo.liked_by_user) {
        this.unlike()
      } else {
        this.like()
      }
    },

    async like () {
      const response = await Axios.put(`/api/photos/${this.photo.id}/like`)
      if (!isStatusOk(response)) return false

      this.$set(this.photo, 'likes_count', this.photo.likes_count + 1)
      this.$set(this.photo, 'liked_by_user', true)
    },

    async unlike () {
      const response = await Axios.delete(`/api/photos/${this.photo.id}/like`)
      if (!isStatusOk(response)) return false

      this.$set(this.photo, 'likes_count', this.photo.likes_count - 1)
      this.$set(this.photo, 'liked_by_user', false)
    }
  },

  watch: {
    $route: {
      async handler () {
        await this.fetchPhoto()
      },
      immediate: true
    }
  }
}
</script>

