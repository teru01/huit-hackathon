<template>
  <div class="photo-list">
    <div class="grid">
      <Photo
        class="grid__item"
        v-for="photo in photos"
        :key="photo.id"
        :item="photo"
        @like="onLikeClick"
      />
    </div>
    <Pagination :current-page="currentPage" :last-page="lastPage" />
    <a href="/photos/download">csvダウンロード</a>
  </div>
</template>

<script>
import { OK } from '../util'
import Photo from '../components/Photo'
import Pagination from '../components/Pagination'
import Axios from 'axios'
import {isStatusOk} from '../util.js'

export default {
  components: {
    Photo,
    Pagination
  },

  data () {
    return {
      photos: [],
      currentPage: 0,
      lastPage: 0
    }
  },

  props: {
    page: {
      type: Number,
      required: false,
      default: 1
    }
  },

  methods: {
    async fetchPhotos () {
      const response = await Axios.get(`/api/photos?page=${this.page}`)
      if(!isStatusOk(response)) return false

      this.currentPage = response.data.current_page
      this.lastPage = response.data.last_page
      this.photos = response.data.data
    },

    onLikeClick ({id, liked}) {
      if (!this.$store.getters['auth/check']) {
        alert('いいねするにはログインしてください')
        return false
      }

      if (liked) {
        this.unlike(id)
      } else {
        this.like(id)
      }
    },

    async like(id) {
      const response = await Axios.put(`/api/photos/${id}/like`)
      if (!isStatusOk(response)) return false

      this.photos = this.photos.map(photo => {
        if (photo.id === response.data.photo_id) {
          photo.likes_count += 1
          photo.liked_by_user = true
        }
        return photo
      })
    },

    async unlike(id) {
      const response = await Axios.delete(`/api/photos/${id}/like`)
      if(!isStatusOk(response)) return false

      this.photos = this.photos.map(photo => {
        if (photo.id === id) {
          photo.likes_count -= 1
          photo.liked_by_user = false
        }
        return photo
      })
    }
  },

  watch: {
    $route: {
      async handler () {
        await this.fetchPhotos()
      },
      immediate: true
    }
  }
}
</script>
