<template>
  <div class="photo-list">
    <div class="grid">
      <Photo
        class="grid__item"
        v-for="photo in photos"
        :key="photo.id"
        :item="photo"
      />
    </div>
    <Pagination :current-page="currentPage" :last-page="lastPage" />
  </div>
</template>

<script>
import { OK } from '../util'
import Photo from '../components/Photo'
import Pagination from '../components/Pagination'
import Axios from 'axios';

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
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.currentPage = response.data.current_page
      this.lastPage = response.data.last_page
      this.photos = response.data.data
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
