<template>
  <div>
    <header>
      <Navbar />
    </header>
    <main>
      <div class="container">
        <Message />
        <RouterView/>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script>
import Navbar from './components/Navbar'
import Footer from './components/Footer'
import Message from './components/Message'

import { INTERNAL_SERVER_ERROR, UNAUTHORIZED, NOT_FOUND } from './util'
import Axios from 'axios';

export default {
  components: {
    Navbar,
    Footer,
    Message
  },

  computed: {
    errorCode () {
      return this.$store.state.error.code
    }
  },

  watch: {
    errorCode: {
      async handler (val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/500')
        } else if (val === UNAUTHORIZED) {
          await Axios.get('/api/refresh-token')
          this.$store.commit('auth/setUser', null)
          this.$router.push('/login')
        } else if (val === NOT_FOUND) {
          this.$router.push('/not-found')
        }
      },
      immediate: true
    },
    $route () {
      this.$store.commit('error/setCode', null)
    }
  }
}
</script>
